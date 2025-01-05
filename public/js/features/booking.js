// Cache for room data and bookings
let cachedRooms = null;
let cachedBookings = null;


// Debounce function to optimize frequent calls
function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function loadDate(startDate, extendDays = 2) {
    const datesArray = [];
    const currentDate = new Date(startDate);

    // Start from the first day of the selected month
    currentDate.setDate(1);

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    // Include dates from the previous month
    const previousMonthDays = new Date(year, month, 0).getDate();
    for (let i = previousMonthDays - extendDays + 1; i <= previousMonthDays; i++) {
        const date = new Date(year, month - 1, i);
        datesArray.push(date.toISOString().split("T")[0]); // Format as YYYY-MM-DD
    }

    // Include dates for the current month
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    for (let i = 1; i <= daysInMonth; i++) {
        const date = new Date(year, month, i);
        datesArray.push(date.toISOString().split("T")[0]); // Format as YYYY-MM-DD
    }

    // Include dates from the next month
    for (let i = 1; i <= extendDays; i++) {
        const date = new Date(year, month + 1, i);
        datesArray.push(date.toISOString().split("T")[0]); // Format as YYYY-MM-DD
    }

    return datesArray;
}

function handleReservationClick(reservation) {
    selectedRooms = []; // Clear selectedRooms when editing a new reservation

    const startDate = reservation.start_date ? new Date(reservation.start_date) : new Date();
    const endDate = reservation.end_date ? new Date(reservation.end_date) : new Date();

    // Initialize daterangepicker with a callback
    $('#edit_daterange').daterangepicker({
        startDate: startDate,
        endDate: endDate,
        locale: {
            format: 'YYYY-MM-DD'
        }
    }, function (start, end) {
        start_book = start.format('YYYY-MM-DD');
        end_book = end.format('YYYY-MM-DD');
    });

    start_book = startDate.toISOString().split("T")[0];
    end_book = endDate.toISOString().split("T")[0];

    // Load reservation details into the modal
    $('#edit_name').val(reservation.name);
    $('#edit_address').val(reservation.address);
    $('#edit_nationality').val(reservation.nationality);
    $('#edit_email').val(reservation.email);
    $('#edit_phone').val(reservation.phone);
    $('#edit_bookingType').val(reservation.type);
    $('#edit_remarks').val(reservation.remarks);
    $('#edit_reservation_id').val(reservation.reservation_id);

    // Load category and pre-select rooms
    loadCategory(reservation.category_id, reservation.room_id.toString());

    $('#edit_booking').modal('show');
}



async function loadCalendar(startDate) {
    const rooms = cachedRooms || await loadRoom();
    const date_value = loadDate(startDate);

    $("#calendar_book thead, #calendar_book tbody").empty();

    const dateFormatter = new Intl.DateTimeFormat("en-US", {
        weekday: "short",
        month: "short",
        day: "numeric",
    });

    // Create header row
    const headerRow = [
        '<th class="sticky-left" style="width:150px;">Rooms</th>',
        ...date_value.map(date => {
            const formattedDate = dateFormatter.format(new Date(date));
            const dayOfWeek = new Date(date).getDay(); // 0 = Sunday, 6 = Saturday
            const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;
            return `<th class="${isWeekend ? 'weekend' : ''}" style="width:150px;font-size:10px;">${formattedDate}</th>`;
        }),
    ];
    $("#calendar_book thead").html(headerRow.join(""));

    // Generate body rows
    const tbodyRows = [];
    for (const room of rooms) {
        const roomReservations = await getReservationsForRoom(room, date_value);

        if (roomReservations.length === 0) {
            const emptyRow = `<tr><td class="border sticky-left">${room}</td>` +
                date_value.map(() => '<td class="border"></td>').join('') +
                "</tr>";
            tbodyRows.push(emptyRow);
        } else {
            roomReservations.forEach((reservations, index) => {
                let row = "<tr>";
                if (index === 0) {
                    row += `<td class="border sticky-left" rowspan="${roomReservations.length}">${room}</td>`;
                }
                date_value.forEach(date => {
                    const reservation = reservations.find(res => res.date_list.includes(date));
                    if (reservation) {
                        const startIndex = reservation.date_list.indexOf(date);
                        if (startIndex === 0) {
                            const daysSpanning = reservation.date_list.filter(d => date_value.includes(d)).length;
                            if (daysSpanning > 0 && daysSpanning <= date_value.length) {
                                const bgColor = getReservationColor(reservation.status);
                                row += `<td class="border clickable-reservation text-capitalize" 
                                             colspan="${daysSpanning}" 
                                             style="background-color:${bgColor}; text-align:center; vertical-align:middle; 
                                             padding:2px; border-radius:15px; color:white;font-size:12px; cursor:pointer;"
                                             data-reservation='${JSON.stringify(reservation)}'>
                                            ${reservation.name}
                                        </td>`;
                            }
                        }
                    } else {
                        row += '<td class="border"></td>'; // Empty cell for dates without reservations
                    }
                });
                row += "</tr>";
                tbodyRows.push(row);
            });
        }
    }
    $("#calendar_book tbody").html(tbodyRows.join(""));

    // Event delegation for click handler
    $("#calendar_book").off("click").on("click", ".clickable-reservation", function () {
        const reservationData = $(this).data("reservation");
        handleReservationClick(reservationData);
    });
}


async function get_all_booking() {
    const myUrl = "/api/reservations/";
    try {
        const response = await axios.get(myUrl, {
            headers: {
                'Content-Type': 'application/json',
            },
        });

        res = response.data.data
        return res

    } catch (error) {
        console.error('Error fetching bookings:', error);
        return []; // Return an empty array on error
    }
}

function getReservationColor(status) {
    switch (status) {
        case "checkin": return "#012866";
        case "checkout": return "#F6911B";
        case "cancel": return "red";
        default: return "#012866";
    }
}

async function getReservationsForRoom(room, date_value) {
    if (!cachedBookings) {
        cachedBookings = await get_all_booking();
    }

    const reservationsForRoom = cachedBookings.filter(item => item.room === room);
    const expandedReservations = reservationsForRoom.map(reservation => {
        const startDate = new Date(reservation.start_date);
        const endDate = new Date(reservation.end_date);
        const date_list = [];
        for (let date = new Date(startDate); date <= endDate; date.setDate(date.getDate() + 1)) {
            date_list.push(date.toISOString().split("T")[0]);
        }
        return { ...reservation, date_list };
    });

    const rows = [];
    expandedReservations.forEach(reservation => {
        let placed = false;
        for (const row of rows) {
            if (!row.some(r => r.date_list.some(date => reservation.date_list.includes(date)))) {
                row.push(reservation);
                placed = true;
                break;
            }
        }
        if (!placed) {
            rows.push([reservation]);
        }
    });

    return rows;
}



async function loadRoom() {
    if (cachedRooms) return cachedRooms;

    const myUrl = "rooms-data";
    try {
        const response = await axios.get(myUrl);
        cachedRooms = response.data.rooms.data.map(item => item.name);

        return cachedRooms;
    } catch (error) {
        console.error(error);
        return [];
    }
}


var start_book = "";
var end_book = ""
var selectedCategory = "";
var selectedRooms = [];

async function loadCategory(category_id = null, checkedRoomId = null) {
    const myUrl = "api/room-categories";
    const response = await axios.get(myUrl);
    const data = response.data.categories.data;

    $(".category_list").empty();
    $(".category_list").append('<option value="" selected hidden>-- Select Room --</option>');

    data.forEach(element => {
        const isSelected = category_id == element.id ? 'selected' : ''; // Check if this is the default category
        const row = `
            <option id="${element.id}" data_list='${JSON.stringify(element)}' ${isSelected}>
                ${element.display_name}
            </option>
        `;
        $(".category_list").append(row);
    });

    // Handle category pre-selection (initial load)
    if (category_id) {
        const selectedOption = $(".category_list").find(`option[id="${category_id}"]`);
        if (selectedOption.length) {
            selectedCategory = category_id;
            const selectedData = JSON.parse(selectedOption.attr("data_list"));

            $('.room_list_data').empty();
            selectedRooms = []; // Clear `selectedRooms`

            selectedData.rooms.forEach(room => {
                const isChecked = room.id.toString() === checkedRoomId ? 'checked' : '';
                if (isChecked) {
                    selectedRooms.push(room.id.toString());
                }

                const row = `
                    <div class='col-md-6'>
                        <label>
                            <input type='checkbox' value='${room.id}' class='room-checkbox' ${isChecked}> ${room.name}
                        </label>
                    </div>
                `;
                $('.room_list_data').append(row);
            });

            attachRoomCheckboxEvent(); // Attach event listener to room checkboxes
        }
    }

    // Handle category change
    $(".category_list").on("change", function () {
        const selectedOption = $(this).find("option:selected");
        selectedCategory = selectedOption.attr("id");
        const selectedData = JSON.parse(selectedOption.attr("data_list"));

        $('.room_list_data').empty();
        selectedRooms = []; // Clear `selectedRooms` when category changes

        selectedData.rooms.forEach(room => {
            const row = `
                <div class='col-md-6'>
                    <label>
                        <input type='checkbox' value='${room.id}' class='room-checkbox'> ${room.name}
                    </label>
                </div>
            `;
            $('.room_list_data').append(row);
        });

        attachRoomCheckboxEvent(); // Attach event listener to room checkboxes
    });
}


function attachRoomCheckboxEvent() {
    $('.room-checkbox').on('change', function () {
        const roomId = $(this).val();

        if ($(this).is(':checked')) {
            if (!selectedRooms.includes(roomId)) {
                selectedRooms.push(roomId);
            }
        } else {
            selectedRooms = selectedRooms.filter(id => id !== roomId);
        }

        console.log("Updated selectedRooms:", selectedRooms); // Debugging log
    });

    // Populate selectedRooms initially based on checked checkboxes
    $('.room-checkbox:checked').each(function () {
        const roomId = $(this).val();

        if (!selectedRooms.includes(roomId)) {
            selectedRooms.push(roomId);
        }
    });

    console.log("Initial selectedRooms:", selectedRooms); // Debugging log
}





function add_booking(){
    let name = $('#name').val()
    let address = $('#address').val()
    let nationality = $('#nationality').val()
    let email = $('#email').val()
    let phone = $('#phone').val()
    let bookingType = $('#bookingType').val()
    let remarks = $('#remarks').val()

    var myUrl = '/api/reservations/';

    var myData = {
        name: name,
        email: email,
        address: address,
        phone: phone,
        nationality: nationality,
        type: bookingType,
        check_in_date: start_book,
        check_out_date: end_book,
        category_id:selectedCategory,
        room: selectedRooms,
        remarks: remarks
    };

    console.log(myData);
    
    

    store_data(myUrl, myData).then(async (response) => {
        
        if (response && response.data.length == 0) {
            $('#error_checkin').text(response.message)
            toaster("Room not available!", "error");
        }else{
            // Reload bookings and refresh the calendar
            cachedBookings = await get_all_booking(); // Refresh the cached bookings
            const today = new Date();
            const initialMonth = today.toISOString().slice(0, 7); // Format as YYYY-MM
            await loadCalendar(initialMonth + "-01"); // Refresh the calendar
            $('#add_booking').modal('hide')
            toaster("Room successfully reserved!", "success");
        }
    })
    
}

function add_booking_modal(){
    $('.room_list_data').empty();
    loadCategory()
    $('#add_booking').modal('show')
}



function cancelReservation(){
    const reservation_id = $('#edit_reservation_id').val()
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            myUrl = "/api/reservations/"+reservation_id
            delete_record(myUrl)
            Swal.fire({
            title: "Deleted!",
            text: "Your file has been deleted.",
            icon: "success"
            });
            // Reload bookings and refresh the calendar
            cachedBookings = await get_all_booking(); // Refresh the cached bookings
            const today = new Date();
            const initialMonth = today.toISOString().slice(0, 7); // Format as YYYY-MM
            await loadCalendar(initialMonth + "-01"); // Refresh the calendar
            $('#edit_booking').modal('hide')
        }
    });
    
}

function update_booking() {
    const reservation_id = $('#edit_reservation_id').val();

    let name = $('#edit_name').val();
    let address = $('#edit_address').val();
    let nationality = $('#edit_nationality').val();
    let email = $('#edit_email').val();
    let phone = $('#edit_phone').val();
    let bookingType = $('#edit_bookingType').val();
    let remarks = $('#edit_remarks').val();

    const myUrl = `/api/reservations/${reservation_id}`;

    const myData = {
        name: name,
        email: email,
        address: address,
        phone: phone,
        nationality: nationality,
        type: bookingType,
        check_in_date: start_book,
        check_out_date: end_book,
        category_id: selectedCategory,
        room: selectedRooms,
        remarks: remarks
    };

    console.log("Updating Reservation:", myData);

    update_data(myUrl, myData).then(response => {
        toaster("Reservation successfully updated!", "success");
        liveReload();
    }).catch(error => {
        console.error("Failed to update reservation:", error);
        toaster("Failed to update reservation!", "error");
    });
}



async function liveReload(){
    cachedBookings = await get_all_booking(); // Refresh the cached bookings
    const today = new Date();
    const initialMonth = today.toISOString().slice(0, 7); // Format as YYYY-MM
    await loadCalendar(initialMonth + "-01"); // Refresh the calendar
    $('#edit_booking').modal('hide')
}

function loadNationalities(){
    const nationalities = [
        "Afghan",
        "Albanian",
        "Algerian",
        "American",
        "Andorran",
        "Angolan",
        "Argentine",
        "Armenian",
        "Australian",
        "Austrian",
        "Azerbaijani",
        "Bahamian",
        "Bahraini",
        "Bangladeshi",
        "Barbadian",
        "Belarusian",
        "Belgian",
        "Belizean",
        "Beninese",
        "Bhutanese",
        "Bolivian",
        "Bosnian",
        "Botswana",
        "Brazilian",
        "British",
        "Bruneian",
        "Bulgarian",
        "Burkinabe",
        "Burmese",
        "Burundian",
        "Cambodian",
        "Cameroonian",
        "Canadian",
        "Cape Verdean",
        "Chadian",
        "Chilean",
        "Chinese",
        "Colombian",
        "Comoran",
        "Congolese",
        "Costa Rican",
        "Croatian",
        "Cuban",
        "Cypriot",
        "Czech",
        "Danish",
        "Djiboutian",
        "Dominican",
        "Dutch",
        "East Timorese",
        "Ecuadorean",
        "Egyptian",
        "Emirati",
        "Equatorial Guinean",
        "Eritrean",
        "Estonian",
        "Ethiopian",
        "Fijian",
        "Filipino",
        "Finnish",
        "French",
        "Gabonese",
        "Gambian",
        "Georgian",
        "German",
        "Ghanaian",
        "Greek",
        "Grenadian",
        "Guatemalan",
        "Guinea-Bissauan",
        "Guinean",
        "Guyanese",
        "Haitian",
        "Honduran",
        "Hungarian",
        "Icelander",
        "Indian",
        "Indonesian",
        "Iranian",
        "Iraqi",
        "Irish",
        "Israeli",
        "Italian",
        "Ivorian",
        "Jamaican",
        "Japanese",
        "Jordanian",
        "Kazakh",
        "Kenyan",
        "Kiribati",
        "Kuwaiti",
        "Kyrgyz",
        "Laotian",
        "Latvian",
        "Lebanese",
        "Liberian",
        "Libyan",
        "Liechtensteiner",
        "Lithuanian",
        "Luxembourger",
        "Macedonian",
        "Malagasy",
        "Malawian",
        "Malaysian",
        "Maldivian",
        "Malian",
        "Maltese",
        "Marshallese",
        "Mauritanian",
        "Mauritian",
        "Mexican",
        "Micronesian",
        "Moldovan",
        "Monacan",
        "Mongolian",
        "Moroccan",
        "Mozambican",
        "Namibian",
        "Nauruan",
        "Nepalese",
        "New Zealander",
        "Nicaraguan",
        "Nigerien",
        "Nigerian",
        "North Korean",
        "Norwegian",
        "Omani",
        "Pakistani",
        "Palauan",
        "Palestinian",
        "Panamanian",
        "Papua New Guinean",
        "Paraguayan",
        "Peruvian",
        "Polish",
        "Portuguese",
        "Qatari",
        "Romanian",
        "Russian",
        "Rwandan",
        "Saint Lucian",
        "Salvadoran",
        "Samoan",
        "San Marinese",
        "Sao Tomean",
        "Saudi",
        "Senegalese",
        "Serbian",
        "Seychellois",
        "Sierra Leonean",
        "Singaporean",
        "Slovak",
        "Slovene",
        "Solomon Islander",
        "Somali",
        "South African",
        "South Korean",
        "Spanish",
        "Sri Lankan",
        "Sudanese",
        "Surinamer",
        "Swazi",
        "Swedish",
        "Swiss",
        "Syrian",
        "Tajik",
        "Tanzanian",
        "Thai",
        "Togolese",
        "Tongan",
        "Trinidadian/Tobagonian",
        "Tunisian",
        "Turkish",
        "Turkmen",
        "Tuvaluan",
        "Ugandan",
        "Ukrainian",
        "Uruguayan",
        "Uzbek",
        "Vanuatuan",
        "Venezuelan",
        "Vietnamese",
        "Yemenite",
        "Zambian",
        "Zimbabwean"
    ];

    const $nationalitySelect = $(".nationality_list"); // Select the dropdown by ID

    // Add options to the dropdown
    $.each(nationalities, function (index, nationality) {
        $nationalitySelect.append(
            $("<option></option>").val(nationality).text(nationality)
        );
    });
}

$(document).ready(() => {
    const today = new Date();
    const initialMonth = today.toISOString().slice(0, 7); // Format as YYYY-MM
    $("#datePicker").val(initialMonth);

    const debouncedLoadCalendar = debounce(date => loadCalendar(date + "-01"), 300);

    // Initialize calendar
    loadCalendar(initialMonth + "-01");

    // Reload calendar on date picker change
    $("#datePicker").on("change", function () {
        debouncedLoadCalendar($(this).val());
    });
    loadRoom();
    loadNationalities();
});

$(function() {
    $('input[name="daterange"]').daterangepicker({
        opens: 'left'
    }, function(start, end, label) {
        start_book = start.format('YYYY-MM-DD')
        end_book = end.format('YYYY-MM-DD')
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    });
});
