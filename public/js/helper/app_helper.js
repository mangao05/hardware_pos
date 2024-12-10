function get_all_record(myUrl){
    axios.get(myUrl, null, {
        headers: {
        'Content-Type': 'application/json'
        }
    })
    .then(function (response) {
        return response
    })
    .catch(function (error) {
        alert('oops');
        console.log(error);
    });
}




function store_data(myUrl, myData){
    axios.post(myUrl, myData, {
        headers: {
        'Content-Type': 'application/json'
        }
    })
    .then(function (response) {
        loadUser()
        console.log(response);
    })
    .catch(function (error) {
        alert('oops');
        console.log(error);
    });
}


function delete_record(myUrl){
    axios.delete(myUrl, {
        headers: {
        'Content-Type': 'application/json'
        }
    })
    .then(function (response) {
        loadUser()
        console.log(response);
    })
    .catch(function (error) {
        alert('oops');
        console.log(error);
    });
}