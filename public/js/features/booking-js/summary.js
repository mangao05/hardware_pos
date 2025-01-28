
let type_rate = null
let customer = null
let initial = null
let bal = null

function savePayment(){
    type_rate = $('#type_rate').val()
    $('.transaction_history tbody').empty()

    // view_summary()
}

function preview(){
    const intial_customer = $('#intial_customer').val()
    const intial_payment = $('#intial_payment').val()

    if(intial_customer == ""){
        $('#intial_customer_error').text("Customer field is required!")
        $('#intial_payment_error').text("Customer field is required!")
    }else{
        const currentDate = new Date();
        const formattedDate = dayjs(currentDate).format("MMM D, YYYY h:mm A");

        customer = intial_customer;
        initial = intial_payment
    
        const row = `
            <tr>
                <td>${intial_customer}</td>
                <td>₱${intial_payment}</td>
                <td>₱${total_balance - initial}</td>
                <td>${formattedDate}</td>
            </tr>
        `
        $('.transaction_history tbody').append(row)

        $('#btn_save_transaction_receipt').show()

        $('#btn_preview_transaction').hide()
        $('#intial_customer_error').text("")
        $('#intial_payment_error').text("")

        
        toaster("Transaction history successfully updated!","success")
    }
    
}

function store_transaction(){
    const reservation_id = $('#edit_reservation_id').val()
    const myUrl = "/checkout"
    const myData = {
        "customer_name":customer,
        "initial_payment":initial,
        "reservation_id":reservation_id,
        "discount":type_rate,
        "total":sum
    }

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, save transaction!"
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
            title: "Create!",
            text: "Your transaction has been created!.",
            icon: "success"
            });
            store_data(myUrl, myData).then(response => {
                const payment = response.data;
                const payment_list = payment.data.payments
                fetchTransaction(payment_list);
                const a = payment_list[payment_list.length - 1];
                $('#total_balance').html(a.balance <= 0 
                ? "<span class='text-success'><strong>Paid</strong></span>" 
                : "₱" + a.balance);
                initial_pyment_list = []
                change_status_after_payment(a.balance)
            })
        }
    });
    
    

    $('#btn_save_transaction_receipt').hide()
}

async function change_status_after_payment(balance){
    const reservation_id = $('#edit_reservation_id').val()
    const room_id = $('#edit_room_id').val()
    let status = null;

    if(balance == 0 && current_status == "checkin"){
        status = "checkin_paid"
    }else{
        status = "checkin_partial"
    }

    if(balance != 0 && current_status == "booked"){
        status = "reserved_partial"
    }
    
    const myUrl = "/reservations/"+reservation_id+"/update-status/"+room_id
    const myData = {
        status:status
    }
    await update_data(myUrl,myData)
    window.location.reload();
    
}


async function fetchTransaction(payments){

    $('.transaction_history tbody').empty()
    $('.transaction_receipt_logs tbody').empty()

    payments.forEach(payment => {
        const formattedDate = dayjs(payment.created_at).format("MMM D, YYYY h:mm A");

        const row = `
            <tr>
                <td style="font-size: 12px;padding:1px">${payment.customer}</td>
                <td style="font-size: 12px;padding:1px">₱${payment.initial_payment}</td>
                <td style="font-size: 12px;padding:1px">₱${payment.balance}</td>
                <td style="font-size: 12px;padding:1px">${formattedDate}</td>
            </tr>
        `
        $('.transaction_history tbody').append(row)

        const row_internal = `
            <tr>
                <td class="summary_label">
                    ${payment.customer}
                </td>
                <td class="summary_label">
                    Pantukan Staff
                </td>
                <td class="summary_label">
                    ₱${payment.initial_payment}
                </td>
                <td class="summary_label">
                    ₱${payment.balance}
                </td>
                <td class="summary_label">
                    ${formattedDate}
                </td>
            </tr>
        `
        $('.transaction_receipt_logs tbody').append(row_internal)
    });

    
   
    
}

function printDiv(divId) {
    const printContents = document.getElementById(divId).innerHTML;
    const originalContents = document.body.innerHTML;

    // Temporarily change the page content to only the div's content
    document.body.innerHTML = printContents;

    // Show elements with class 'rules_regulation' (if any)
    $('.rules_regulation').show();
    $('.customers_copy').show()

    // Log before printing
    console.log("Attempting to print...");

    // Add event listeners for beforeprint and afterprint
    window.addEventListener("beforeprint", function() {
        console.log("Print dialog is opening...");
    });

    window.addEventListener("afterprint", function() {
        console.log("Print dialog has closed.");
        // You can infer the action here (e.g., print was completed or canceled)
        document.body.innerHTML = originalContents;
        window.location.reload();
    });

    // Trigger the print dialog
    window.print();
}


function close_summary_modal(){
    $('#view_summary_modal').modal('hide')
    $('#btn_preview_transaction').show()
}


$(document).ready(() => {
    
});