function change_status(status){

    var confirm_button = ""
    var title = ""
    var text = ""
    
    if(status="checkout"){
        confirm_button = "Yes, Check-out!"
        title = "Deleted!"
        text = "Booked has been check-out."
    }

    if(status="checkin"){
        confirm_button = "Yes, Check-in!"
        title = "Deleted!"
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
            
            const myUrl = "/api/reservations/"+reservation_id+"/update-status/"+room_id
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

function view_summary(){
    transaction = $(".btn-success").data("reservation_details");

    $('#transaction_name').text(transaction.name)
    $('#trans_email').text(transaction.email)
    $('#trans_phone').text(transaction.phone)
    $('#trans_address').text(transaction.address)
    
    $('#view_summary_modal').modal('show')
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
    console.log(reservation_details_id);
    
}

function display_logs_history(history_data){
    console.log(history_data);
    
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
        const formattedDate = dayjs(item.created_at).format("MMM D, YYYY h:mm A");
        const row = `
            <tr>
                <td>${type}</td>
                <td>Juan Dela Cruz</td>
                <td>${item.message}</td>
                <td>${formattedDate}</td>
            </tr>
        `
        $('#history_logs tbody').append(row)
    });
    
}
