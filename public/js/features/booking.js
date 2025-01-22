// Cache for room data and bookings
let cachedRooms = null;
let cachedBookings = null;
let selected_category = null;
let all_add_ons = null;
let current_room_guest = null;
let current_status = null


async function loadallAddOns(){
    myUrl = "/api/leisures"
    res = await get_data(myUrl);
    all_add_ons = res.data.leisures;
}


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

async function handleReservationClick(reservation) {
    $('.room_list_display tbody').empty();
    $('.add_ons_table tbody').empty();
    selectedAddOns = []
    if(reservation.add_ons){
        reservation.add_ons.forEach(element => {
            selectedAddOns.push({
                "addon_id": element.addon_id, 
                "qty": element.qty, 
                "addon_price": element.addon_price
            })
        });
    }
    
    
    selectedRooms = [];
    const startDate = reservation.start_date ? new Date(reservation.start_date) : new Date();
    const endDate = reservation.end_date ? new Date(reservation.end_date) : new Date();
    current_room_guest = reservation.guest
    current_status = reservation.status
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
    $('#edit_room_id').val(reservation.room_id)
    $('#edit_current_room').text(reservation.room)
    $('#booking_status').text(reservation.status)
    $('#reservation_room_details_id').val(reservation.reservation_room_details_id)

    $('#edit_room_list_display').empty();

    reservation.other_rooms.forEach(element => {
        const row = `
            <li>${element.room_name}</li>
        `
        $('#edit_room_list_display').append(row);
    });

    
    
    check_status_date = transaction_button_control(start_book);

    if(current_status == "checkin_paid"){
        $('.btn_edit').hide()
        $('.btn_view_summary').hide()
    }else{
        $('.btn_edit').show()
        $('.btn_view_summary').show()
    }

    if(reservation.status == "checkin" && current_status == "checkin_paid"){
        $('.btn_early_check_out').show()
        $('.btn_check_out').show()
    }else{
        $('.btn_early_check_out').hide()
        $('.btn_check_out').hide()
        // $('.btn_edit').show()
    }
        
    
    if(check_status_date && reservation.status != "checkin_paid" && reservation.status != "checkin"){
        $('.btn_checkin').show()
    }else{
        $('.btn_checkin').hide()
    }
 
    

    myUrl = "/history-logs?reservation_id="+reservation.reservation_id
    history_data = await get_data(myUrl);

    display_logs_history(history_data);
    
    // Load category and pre-select rooms
    
    $('#edit_booking').modal('show');
}




function transaction_button_control(input_date){
    const currentDate = new Date();

    const inputDate = new Date(input_date); 

    currentDate.setHours(0, 0, 0, 0);
    inputDate.setHours(0, 0, 0, 0);

    if (inputDate > currentDate) {
        console.log("The input date is in the future.");
        return false
    } else if (inputDate < currentDate) {
        console.log("The input date is in the past.");
        return true
    } else {
        console.log("The input date is today.");
        return true
    }
}





async function loadCalendar(startDate,category_id = 0) {
    const rooms = await loadRoom(category_id);
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
                            var transaction_type = "Group";
                            if (reservation.other_rooms.length == 0){
                                transaction_type = "Single"
                            }

                            if (daysSpanning > 0 && daysSpanning <= date_value.length) {
                                const bgColor = getReservationColor(reservation.status);
                                row += `<td class="border clickable-reservation text-capitalize" 
                                             colspan="${daysSpanning}" 
                                             style="background-color:${bgColor}; text-align:center; vertical-align:middle; 
                                             padding:2px; border-radius:15px; color:white;font-size:12px; cursor:pointer;"
                                             data-reservation='${JSON.stringify(reservation)}'>
                                            ${reservation.name} (${transaction_type})
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
    const myUrl = "/api/reservations";
    try {
        const response = await axios.get(myUrl, {
            headers: {
                'Content-Type': 'application/json',
            },
        });

        res = response.data.data
        return res

    } catch (error) {
        return []; // Return an empty array on error
    }
}

function getReservationColor(status) {
    switch (status) {
        case "booked": return "#EE534F";
        case "reserved_partial": return "#5E6AC0";
        case "checkout": return "#F6911B";
        case "cancelled": return "BDBDBD";
        case "checkin": return "#65BB6A";
        case "checkin_partial": return "#42A5F6";
        case "checkin_paid": return "#FFEE58";
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



async function loadRoom(category_id) {
    // if (cachedRooms) return cachedRooms;
    const myUrl = "api/room-categories/"+category_id;
    try {
        const response = await axios.get(myUrl);
        
        cachedRooms = response.data.category.rooms.map(item => item.name);
        return cachedRooms;
    } catch (error) {
        
        return [];
    }
}


var start_book = "";
var end_book = ""
var selectedCategory = "";
var selectedRooms = [];
var trans_type = null;
var category_list_data = null;

async function loadCategory(category_id = null, checkedRoomId = null) {
    const myUrl = "api/room-categories";
    const response = await axios.get(myUrl);
    const data = response.data.categories.data;
    category_list_data = response.data.categories.data;
    $(".category_list").empty();
    $(".category_list").append('<option value="" selected hidden>-- Select Category --</option>');

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
                            <input type='checkbox' onclick='onSelectCategory()' value='${room.id}' class='room-checkbox' ${isChecked}> ${room.name}
                        </label>
                    </div>
                `;
                $('.room_list_data').append(row);
            });

            attachRoomCheckboxEvent(); // Attach event listener to room checkboxes
        }
    }

    // Handle category change
    $(".category_list").on("change",async function () {
        const selectedOption = $(this).find("option:selected");
        selectedCategory = selectedOption.attr("id");
        const selectedData = JSON.parse(selectedOption.attr("data_list"));
        $('.select_room_div').show();
        $('.room_list_data').empty();
        
        $(".room_list_data").append('<option value="" selected hidden>-- Select Room --</option>');
        selectedData.rooms.forEach(room => {
            const row = `
                <option value='${room.id}' data='${JSON.stringify(room)}'> ${room.name} </option>
            `;
            $('.room_list_data').append(row);
        });


        attachRoomCheckboxEvent(); // Attach event listener to room checkboxes
        liveReload(selectedCategory)
        selected_category = selectedCategory
        
        await load_available_room_per_category(selected_category,start_book, end_book)
    });

    // $('.room_list_selection').on("change",function(){
    //     const selectedOption = $(this).find("option:selected");
    //     const roomData = JSON.parse(selectedOption.attr("data"));
    //     const category_name = category_list_data.find(category => category.id === roomData.room_category_id)

    //     const isRoomAlreadySelected = selectedRooms.some(room => room.room_id === roomData.id);
        
    //     if (isRoomAlreadySelected) {
    //         toaster("This room is already selected.!","error")
    //         return; 
    //     }

    //     selectedRooms.push({
    //         "room_id":roomData.id,
    //         "guest":0
    //     })
        
    //     const row = `
    //         <tr data-room-id="${roomData.id}">
    //             <td class="table-custome-align">${roomData.name}(${category_name.display_name})<small class="text-danger" id="room_${roomData.id}"></small></td>
    //             <td class="table-custome-align"><input class="form-control" name="guests[]" type="text"></td>
    //             <td class="table-custome-align"><badge class="badge bg-danger remove-room" type="button" name="guests[]">X</badge></td>
    //         </tr>
    //     `
    //     $('.room_list_display tbody').append(row)

    //     // edit part 
    //     if(trans_type == "add_room_update"){
    //         const editrow = `
    //             <div><small><span class="badge bg-danger">X</span>${roomData.name}(room category)</small></div>
    //         `
    //         $('#edit_room_list_display').append(editrow)
    //     }

    //     if(trans_type == "transfer_room_update"){
    //         $('#edit_current_room').empty()
    //         $('#edit_current_room').text(roomData.name)
    //     }
        
    // })
}


function add_room(){
    const textbox = document.getElementById("edit_category");
    textbox.disabled = false;
    textbox.focus();   
    trans_type = "add_room_update" 
}

function transfer_room(){
    const textbox = document.getElementById("edit_category");
    textbox.disabled = false;
    textbox.focus();   
    trans_type = "transfer_room_update" 
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

    });

    // Populate selectedRooms initially based on checked checkboxes
    $('.room-checkbox:checked').each(function () {
        const roomId = $(this).val();

        if (!selectedRooms.includes(roomId)) {
            selectedRooms.push(roomId);
        }
    });
}


function add_booking(){
    const guestInputs = document.querySelectorAll('input[name="guests[]"]');
    const guest = [];
    const rooms = [];

    guestInputs.forEach(item => {
        if(item.value == ''){
            guests = 0
        }else{
            guests = item.value
        }
        guest.push(parseInt(guests))
        
    });

    selectedRooms.forEach((element, index) => {
        element.guest = guest[index]
        rooms.push(element)
    });

   

    let name = $('#name').val()
    let address = $('#address').val()
    let nationality = $('#nationality').val()
    let email = $('#email').val()
    let phone = $('#phone').val()
    let bookingType = $('#bookingType').val()
    let remarks = $('#remarks').val()
    let category = $('#category').val()

    var myUrl = '/reservations';

    var myData = {
        name: name,
        email: email,
        address: address,
        phone: phone,
        nationality: nationality,
        type: bookingType,
        check_in_date: start_book,
        check_out_date: end_book,
        guests: rooms_selected,
        remarks: remarks,
        addons:selectedAddOns
    };   
    
    
    if (date_book_validation(start_book)) {
        $('#date_error').text("Please select a valid start date.")
        return;
    } 

    // if (rooms.length == 0){
    //     $('#room_list_selection').val(null)
    //     $('#room_error').text("Please select at least one room.")
    //     return;
    // }
    

    store_data(myUrl, myData).then(async (response) => {
       
        if (response && response.errors) {
            if(category == ""){
                $('#room_category_error').text("Please select category.")
            }
            $('#error_checkin').text(response.message)
            response.errors['check_in_date']?.[0] && $('#date_error').text(response.errors['check_in_date'][0]);
            response.errors['name']?.[0] && $('#name_error').text(response.errors['name'][0]);
            response.errors['nationality']?.[0] && $('#nationality_error').text(response.errors['nationality'][0]);
            response.errors['type']?.[0] && $('#bookingType_error').text(response.errors['type'][0]);
            toaster("Room not available!", "error");
        }else if(response.error){
            var data = response.unavailable_rooms;
            data.forEach(element => {
                tag_room_not_available(element.id)
            });
            
            toaster("Room not available!", "error");
        }else{
            
            cachedBookings = await get_all_booking(); 
            const today = new Date();
            const initialMonth = today.toISOString().slice(0, 7); // Format as YYYY-MM
            await loadCalendar(initialMonth + "-01", selectedCategory); // Refresh the calendar
            $('#add_booking').modal('hide')
            toaster("Room successfully reserved!", "success");
        }
    })
    
}

function tag_room_not_available(room_id){
    $('#room_'+room_id).text("Not Available")
}


function date_book_validation(inputDate){
    var dateToCheck = new Date(inputDate);
    var currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0);

    return dateToCheck < currentDate;
}

function add_booking_modal(){
    close_add()
    $('.select_room_div').hide();
    $('.room_list_display tbody').empty()
    $('.room_list_data').empty();
    $('#add_booking').modal('show')
}

function cancelReservation(){
    const reservation_room_details_id = $('#reservation_room_details_id').val()
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
            myUrl = "/api/reservation-rooms/"+reservation_room_details_id
            delete_record(myUrl)
            Swal.fire({
            title: "Deleted!",
            text: "Your file has been deleted.",
            icon: "success"
            });
            liveReload(0);
            $('#edit_booking').modal('hide')
        }
    });
    
}

function update_booking() {
    var reservation_id = $('#edit_reservation_id').val()
    var date = $('#edit_daterange').val()
    var date_convert = date.split(' - ')
    const reservation_details_id = $('#reservation_room_details_id').val()

    if(trans_type == "extend_date"){
        const myUrl = "/reservation-rooms/room/"+reservation_details_id+"/extend"
        const myData = {
            "check_out_date":end_book
        }

        update_data(myUrl,myData).then(async response => {
            if (response && response.data.length == 0) {
                toaster("something wrong!", "error");
            }else{
                const category_id = response.data.room_details.room_category_id;
                toaster("Transfer room succefully update!", "success");
                liveReload(category_id);
                $('#edit_booking').modal('hide')
            }
        })
        return;
    }
    

    if(trans_type == "add_room_update"){
        const myUrl = `/api/reservations/change-room/`+reservation_id;

        const myData = {
            "new_room" : rooms_selected,
            "check_in_date": date_convert[0],
            "check_out_date": date_convert[1]
        }

        store_data(myUrl, myData).then(async response => {
            var room_category = response.data.data.reservation_details;
            var get_last_room = room_category[room_category.length - 1]
            var category_id = get_last_room.room_details.room_category_id;
            
            if (response && response.data.length == 0) {
                toaster("Room not available!", "error");
            }else{
                toaster("Add room succefully update!", "success");
                liveReload(category_id);
                $('#edit_booking').modal('hide')
            }
        })
        return;

    }else if(trans_type == "transfer_room_update"){
        var room_id = $('#edit_room_id').val() 
        const myUrl = `/api/reservations/change-room/`+reservation_id;

        const myData = {
            "old_room":parseInt(room_id),
            "new_room" : rooms_selected,
            "check_in_date": date_convert[0],
            "check_out_date": date_convert[1]
        }
        
        store_data(myUrl, myData).then(async response => {
            var room_category = response.data.data.reservation_details;
            var get_last_room = room_category[room_category.length - 1]
            var category_id = get_last_room.room_details.room_category_id;
            
            if (response && response.data.length == 0) {
                toaster("Room not available!", "error");
            }else{
                
                toaster("Transfer room succefully update!", "success");
                liveReload(category_id);
                $('#edit_booking').modal('hide')
            }
        })

        return;
    }else{
        let name = $('#edit_name').val();
        let address = $('#edit_address').val();
        let nationality = $('#edit_nationality').val();
        let email = $('#edit_email').val();
        let phone = $('#edit_phone').val();
        let bookingType = $('#edit_bookingType').val();
        let remarks = $('#edit_remarks').val();
        let room_id = $('#edit_room_id').val();
        let status = $("#booking_status").text();
        
        
        const myUrl = `/api/reservations/${reservation_id}`;

        const myData = {
            "reservation" : {
                "name": name,
                "email": email,
                "address": address,
                "phone": phone,
                "nationality": nationality,
                "type": bookingType,
                "remarks": remarks
            },
            "room" : {
                "room_id" : room_id,
                "check_in_date": start_book,
                "check_out_date": end_book,
                "status" : status,
                "guest" : current_room_guest
            },
            addons:selectedAddOns
            
        };     
        
        
        update_data(myUrl, myData).then(async response => {
            const textbox = document.getElementById("edit_daterange");
            textbox.disabled = true;
            var category_id = null;
           
            response.data.reservation_details.forEach(element => {
                category_id = element.room_details.room_category_id
            });
            
            if (response && response.data.length == 0) {
                $('#error_checkin').text(response.message)
                toaster("Room not available!", "error");
            }else{
                toaster("Reservation successfully updated!", "success");
                liveReload(category_id);
                $('#edit_booking').modal('hide')
            }
        })
    }
    
}



async function liveReload(category_id){
    cachedBookings = await get_all_booking(); // Refresh the cached bookings
    const today = new Date();
    const initialMonth = today.toISOString().slice(0, 7); // Format as YYYY-MM
    await loadCalendar(initialMonth + "-01",category_id); // Refresh the calendar
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
    selected_category = 0

    const debouncedLoadCalendar = debounce(date => loadCalendar(date + "-01"), 300);

    // Initialize calendar
    loadCalendar(initialMonth + "-01",selected_category);

    // Reload calendar on date picker change
    $("#datePicker").on("change", function () {
        debouncedLoadCalendar($(this).val());
    });
    
    loadNationalities();
    loadCategory();
    loadallAddOns()
});

$(function() {
    $('input[name="daterange"]').daterangepicker({
        opens: 'left'
    }, function(start, end, label) {
        start_book = start.format('YYYY-MM-DD')
        end_book = end.format('YYYY-MM-DD')
        // console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    });
});


function close_add(){
    $('#room_category_error').text("")
    $('#date_error').text("")
    $('#name_error').text("")
    $('#nationality_error').text("")
    $('#bookingType_error').text("")
    clear_form()
}

function clear_form(){
    $('#daterange').val("")
    $('#category').val("")
    $('#room_list_selection').val("")
    $('#name').val("")
    $('#address').val("")
    $('#nationality').val("")
    $('#email').val("")
    $('#phone').val("")
    $('#bookingType').val("")
    $('#remarks').val("")
}

