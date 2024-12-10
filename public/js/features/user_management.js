

$(document).ready(function() {
    
});



function add_user(){
    username = $('#username').val()
    firstname = $('#firstname').val()
    lastname = $('#lastname').val()
    password = $('#password').val()
    user_role = $('#user_role').val()
    
    var myUrl = 'users/';

    var myData = {
        username:username,
        firstname:firstname,
        lastname:lastname,
        password:password,
        role:user_role,
        is_active:true
    };

    axios.post(myUrl, myData, {
        headers: {
        'Content-Type': 'application/json'
        }
    })
    .then(function (response) {
        console.log(response);
    })
    .catch(function (error) {
        alert('oops');
        console.log(error);
    });
    
}