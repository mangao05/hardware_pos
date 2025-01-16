function checkOut(){
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Check-out!"
    }).then(async (result) => {
        if (result.isConfirmed) {

            const reservation_id = $('#edit_reservation_id').val()
            const room_id = $('#edit_room_id').val()
            
            const myUrl = "/api/reservations/"+reservation_id+"/update-status/"+room_id
            const myData = {
                status:"checkout"
            }
            const update_reservation = await update_data(myUrl,myData)
            const category_id = update_reservation.data.room_details.room_category_id;
            
            Swal.fire({
            title: "Deleted!",
            text: "Booked has been check-out.",
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
    $('#view_summary_modal').modal('show')
}


function printDiv(divId) {
    const printContents = document.getElementById(divId).innerHTML;
    const originalContents = document.body.innerHTML;

    // Replace the body content with the print content
    document.body.innerHTML = printContents;

    // Trigger the print
    
    window.print();

    // Restore the original content
    document.body.innerHTML = originalContents;
    
    // Reload scripts and styles if necessary
    window.location.reload();
}
