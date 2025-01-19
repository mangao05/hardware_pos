function change_status(status){

    var confirm_button = ""
    var title = ""
    var text = ""
    
    if(status=="checkout"){
        confirm_button = "Yes, Check-out!"
        title = "Check-out!"
        text = "Booked has been check-out."
    }

    if(status=="checkin"){
        confirm_button = "Yes, Check-in!"
        title = "Check-in!"
        text = "Booked has been check-out."
    }

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: confirm_button
    }).then(async (result) => {
        if (result.isConfirmed) {

            const reservation_id = $('#edit_reservation_id').val()
            const room_id = $('#edit_room_id').val()
            
            const myUrl = "/reservations/"+reservation_id+"/update-status/"+room_id
            const myData = {
                status:status
            }
            const update_reservation = await update_data(myUrl,myData)
            const category_id = update_reservation.data.room_details.room_category_id;
            
            Swal.fire({
                title: title,
                text: text,
                icon: "success"
            });
            liveReload(category_id)
            $('#edit_booking').modal('hide')
        }
    });
}

function extend_book(){
    const textbox = document.getElementById("edit_daterange");
    textbox.disabled = false;
    textbox.focus();
}



let payment = 0; // Default initial payment value
const total_price = []; // Array to store room prices
let sum = 0; // Total price

async function view_summary() {
    const reservation_id = $('#edit_reservation_id').val();
    const myUrl = "/reservations/" + reservation_id;
    const res = await get_data(myUrl);

    console.log(res.data);
    

    $('#transaction_name').text(res.data.name);
    $('#trans_email').text(res.data.email);
    $('#trans_phone').text(res.data.phone);
    $('#trans_address').text(res.data.address);

    $('.order_details_summary tbody').empty();
    total_price.length = 0; // Clear previous values
    res.data.reservation_details.forEach(details => {
        total_price.push(details.room_details.price);

        const row = `
            <tr>
                <td class="table-custome-align">${details.room_details.name}</td>
                <td class="table-custome-align">Pending</td>
                <td class="table-custome-align">N/A</td>
                <td class="table-custome-align">₱${details.room_details.price}</td>
            </tr>
        `;
        $('.order_details_summary tbody').append(row);
    });

    sum = total_price.reduce((acc, val) => acc + val, 0);

    update_summary();
    $('#view_summary_modal').modal('show');
}

function update_summary() {
    $('.order_details_summary tbody').append(`
        <tr style="background-color:linen">
            <td class="table-custome-align"><strong>Total Payment:</strong></td>
            <td class="table-custome-align"></td>
            <td class="table-custome-align"></td>
            <td class="table-custome-align">₱${sum}</td>
        </tr>    
    `);

    $('.order_details_summary tbody').append(`
        <tr style="background-color:linen">
            <td class="table-custome-align"><strong>Initial Payment:</strong></td>
            <td class="table-custome-align"></td>
            <td class="table-custome-align"></td>
            <td class="table-custome-align">₱${payment}</td>
        </tr>    
    `);

    $('.order_details_summary tbody').append(`
        <tr style="background-color:linen">
            <td class="table-custome-align"><strong>Balance:</strong></td>
            <td class="table-custome-align"></td>
            <td class="table-custome-align"></td>
            <td class="table-custome-align">₱${sum - payment}</td>
        </tr>    
    `);
}



function printDiv(divId) {
    const printContents = document.getElementById(divId).innerHTML;
    const originalContents = document.body.innerHTML;

    // Replace the body content with the print content
    document.body.innerHTML = printContents;

    // Trigger the print
    $('.rules_regulation').show()
    window.print();

    // Restore the original content
    document.body.innerHTML = originalContents;
    
    // Reload scripts and styles if necessary
    window.location.reload();
}


function check_in(){
    var reservation_details_id = $('#reservation_room_details_id').val()
    
}

function display_logs_history(history_data){
    
    var res = history_data;
    var type = "pending"
    
    $('#history_logs tbody').empty()

    res.forEach(item => {
        
        if(item.action == "create"){
            type = "Reserved"
        }

        if(item.action == "checkin"){
            type = "Check-in"
        }
        if(item.action == "checkout"){
            type = "Check-out"
        }
        const formattedDate = dayjs(item.created_at).format("MMM D, YYYY h:mm A");
        const row = `
            <tr>
                <td>${type}</td>
                <td>${item.user_name}</td>
                <td>${item.message}</td>
                <td>${formattedDate}</td>
            </tr>
        `
        $('#history_logs tbody').append(row)
    });
    
}


// add-ons add reservation modal 

let selectedAddOns = [];

$(".selected_add_ons").on("change", function () {
    const selectedValue = $(this).val();
    const filteredAddOns = all_add_ons.filter(addOn => addOn.type === selectedValue);

    $('.add_ons_table tbody').empty();
    filteredAddOns.forEach(add_ons => {
        const row = `
            <tr>
                <td class="table-custome-align">
                    <input type="checkbox" class="add-on-checkbox" data-id="${add_ons.id}">
                </td>
                <td class="table-custome-align">${add_ons.item_name}</td>
                <td class="table-custome-align">
                    <input type="number" class="form-control add-on-qty" data-id="${add_ons.id}" value="0" min="1">
                </td>
                <td class="table-custome-align">₱${add_ons.price_rate}</td>
            </tr>
        `;
        $('.add_ons_table tbody').append(row);
    });

    $('.add-on-checkbox').each(function () {
        const addOnId = $(this).data("id");
        const selectedAddOn = selectedAddOns.find(addOn => addOn.addon_id === addOnId);

        if (selectedAddOn) {
            $(this).prop("checked", true);
            $(`.add-on-qty[data-id="${addOnId}"]`).val(selectedAddOn.qty);
        }
    });

});

$(".add_ons_table").on("change", ".add-on-checkbox", function () {
    const addOnId = $(this).data("id");
    const quantityInput = $(`.add-on-qty[data-id="${addOnId}"]`);
    const qty = parseInt(quantityInput.val()) || 1; 
    
    const priceRate = parseFloat($(`td:contains("₱")`, $(this).closest('tr')).text().replace("₱", ""));

    if ($(this).is(":checked")) {
        if (!selectedAddOns.some(addOn => addOn.addon_id === addOnId)) {
            selectedAddOns.push({ 
                addon_id: addOnId, 
                qty: qty, 
                addon_price: priceRate * qty 
            });
        }
    } else {
        selectedAddOns = selectedAddOns.filter(addOn => addOn.addon_id !== addOnId);
    }
});

$(".add_ons_table").on("input", ".add-on-qty", function () {
    const addOnId = $(this).data("id");
    const newQty = parseInt($(this).val()) || 1; 

    const priceRate = parseFloat($(`td:contains("₱")`, $(this).closest('tr')).text().replace("₱", ""));
    
    const selectedAddOn = selectedAddOns.find(addOn => addOn.addon_id === addOnId);
    if (selectedAddOn) {
        selectedAddOn.qty = newQty;
        selectedAddOn.price = priceRate * newQty; 
    }
});
//end add-ons add reservation modal



// add rooms on create reservation

const rooms_selected = []; 

async function load_available_room_per_category(category_id, start_book, end_book) {
    const myUrl = "/api/categories/" + category_id + "/available-rooms?start=" + start_book + "&end=" + end_book;
    const roomDataList = await get_data(myUrl);

    console.log(roomDataList);
    
    $('.room_list_display tbody').empty();

    roomDataList.forEach(roomData => {
        
        const existingRoom = rooms_selected.find(room => room.room_id === roomData.id);
        const isChecked = !!existingRoom; 
        const guestCount = existingRoom ? existingRoom.guest : 0; 

        const row = `
            <tr data-room-id="${roomData.id}">
                <td class="table-custome-align">${roomData.name}(category name)<small class="text-danger" id="room_${roomData.id}"></small></td>
                <td class="table-custome-align"><input class="form-control guest-input" name="guests[]" type="number" min="1" value="${guestCount}"></td>
                <td class="table-custome-align"><input type="checkbox" class="room-checkbox" data-room-id="${roomData.id}" ${isChecked ? "checked" : ""}></td>
            </tr>
        `;
        $('.room_list_display tbody').append(row);
    });

    
    $('.room-checkbox').on('change', function () {
        const roomId = $(this).data('room-id');
        const guestInput = $(this).closest('tr').find('.guest-input'); 
        const guestCount = parseInt(guestInput.val()) || 1; 

        if ($(this).is(':checked')) {
            
            if (!rooms_selected.some(room => room.room_id === roomId)) {
                rooms_selected.push({
                    room_id: roomId,
                    guest: guestCount
                });
            }
        } else {
           
            const index = rooms_selected.findIndex(room => room.room_id === roomId);
            if (index > -1) {
                rooms_selected.splice(index, 1);
            }
        }
    });

    
    $('.guest-input').on('input', function () {
        const roomId = $(this).closest('tr').find('.room-checkbox').data('room-id');
        const guestCount = parseInt($(this).val()) || 1; 

        
        const roomIndex = rooms_selected.findIndex(room => room.room_id === roomId);
        if (roomIndex > -1) {
            rooms_selected[roomIndex].guest = guestCount;
        }

    });
}

// end add rooms on create reservation

