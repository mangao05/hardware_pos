
$(document).ready(function() {
    loadUser()
});

function loadUser(){
    myUrl = "users/"
    axios.get(myUrl, null, {
        headers: {
        'Content-Type': 'application/json'
        }
    })
    .then(function (response) {
        $("#data-table tbody").empty();
        
        response.data.data.forEach(item => {
            const row = `
                <tr>
                    <td>${item.username}</td>
                    <td>${item.firstname}</td>
                    <td>${item.lastname}</td>
                    <td>test</td>
                    <td>
                        ${item.is_active == 0?`<span class="badge bg-danger">Inactive</span>`:`<span class="badge bg-success">Active</span>`}
                        
                    </td>
                    <td>
                        <span class="badge bg-primary">Edit</span>
                        <span class="badge bg-danger delete-button" onclick="delete_user(${item.id})">Delete</span>
                    </td>
                </tr>
            `;
            $("#data-table tbody").append(row);
        });
    })
    .catch(function (error) {
        alert('oops');
        console.log(error);
    });
}


function add_user(){
    let username = $('#username').val()
    let firstname = $('#firstname').val()
    let lastname = $('#lastname').val()
    let password = $('#password').val()
    let user_role = $('#user_role').val()
    let password_confirmation = $('#confirm-password').val()
    let isChecked = $('#is_active').prop('checked');
    var myUrl = 'users/';

    var myData = {
        username:username,
        firstname:firstname,
        lastname:lastname,
        password:password,
        role:user_role,
        password_confirmation:password_confirmation,
        is_active:isChecked
    };
    
    store_data(myUrl, myData)

    $("#exampleModal").modal('hide')
}


function delete_user(user_id){
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            myUrl = "users/"+user_id
            delete_record(myUrl)
            Swal.fire({
            title: "Deleted!",
            text: "Your file has been deleted.",
            icon: "success"
            });
        }
    });
}