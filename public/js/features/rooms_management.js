$(document).ready(function() {
    loadRoom()
    loadRoomCategory()
});

async function loadRoomCategory(){
    myUrl = "api/room-categories/"
    
    try {
        const response = await axios.get(myUrl);
        room_category = response.data.categories.data;
        
        $(".room_type_list").empty();
        $(".room_type_list").append('<option value="" disabled selected>-- Select Type --</option>');
        
        room_category.forEach(item => {
            const row = `
                <option value="${item.id}">${item.display_name}</option>
            `;
            $(".room_type_list").append(row);
        });
        
    } catch (error) {
        if (error.response) {
            return error.response.data
        }
    }
}

var no_of_page = 10
var page = 1

function next_page(){
    page++
    loadRoom()
}

function prev_page(){
    page--
    loadRoom()
}

async function loadRoom(){
    myUrl = "rooms-data?per_page="+no_of_page+"&page="+page
    try {
        const response = await axios.get(myUrl);
        $("#data-table-rooms tbody").empty();
        response.data.rooms.data.forEach(item => {
            console.log(item);
            
            const row = `
                <tr>
                    <td>${item.name}</td>
                    <td>₱${item.price}</td>
                    <td>pending data ${item.room_category_id}</td>
                    <td>${item.pax}</td>
                    <td>
                        ${item.availability == 0?`<span class="badge bg-danger">Inactive</span>`:`<span class="badge bg-success">Active</span>`}
                    </td>
                    <td>
                        <span class="badge bg-primary edit-button" data-room='${JSON.stringify(item)}' onclick="edit_room(this)">Edit</span>
                        <span class="badge bg-danger delete-button" onclick="delete_rooms(${item.id})">Delete</span>
                    </td>
                </tr>
            `;
            $("#data-table-rooms tbody").append(row);
        });
        
    } catch (error) {
        if (error.response) {
            return error.response.data
        }
    }
}


function add_rooms(){
    let room_name = $('#room_name').val()
    let room_price = $('#room_price').val()
    let room_pax = $('#room_pax').val()
    let room_type = $('#room_type').val()
    let is_available = $('#is_available').prop('checked');

    var myUrl = 'rooms-data/';

    var myData = {
        name:room_name,
        price:room_price,
        pax:room_pax,
        room_category_id:room_type,
        availability:is_available,
    };

    store_data(myUrl, myData).then(response => {
        close_add()
        if (response && response.errors) {
            response.errors['name']?.[0] && $('#room_name_error').text(response.errors['name'][0]);
            response.errors['price']?.[0] && $('#room_price_error').text(response.errors['price'][0]);
            response.errors['pax']?.[0] && $('#room_pax_error').text(response.errors['pax'][0]);
        } else {
            loadRoom()
            clear_form()
            toaster("Rooms successfully added!","success")
            $("#room_modal").modal('hide')
        }
    })
}

function edit_room(element){
    const data = JSON.parse(element.getAttribute('data-room'));
    
    $('#room_name_edit').val(data['name'])
    $('#room_price_edit').val(data['price'])
    $('#room_pax_edit').val(data['pax'])
    $('#room_type_edit').val(data['room_category_id'])
    $('#room_id_edit').val(data['id'])
    
    isActive = data['availability']

    if(isActive == true){
        $('#is_available_edit').prop('checked',true)
    }else{
        $('#is_available_edit').prop('checked',false)
    }
    $('#room_modal_edit').modal('show')
}

function update_data_set(){
    const data_id = $('#room_id_edit').val()

    let room_name = $('#room_name_edit').val()
    let room_price = $('#room_price_edit').val()
    let room_pax = $('#room_pax_edit').val()
    let room_category = $('#room_type_edit').val()
    let is_available = $('#is_available_edit').prop('checked');

    var myData = {
        name:room_name,
        price:room_price,
        pax:room_pax,
        room_category_id:room_category,
        availability:is_available,
    };
    
    url = "api/rooms/"+data_id
    update_data(url,myData).then(response => {
        toaster("Rooms successfully updated!","success")
        $('#room_modal_edit').modal('hide')
        loadRoom()
    })
}


function delete_rooms(rooms_id){
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
            myUrl = "/api/rooms/"+rooms_id
            delete_record(myUrl)
            Swal.fire({
            title: "Deleted!",
            text: "Your file has been deleted.",
            icon: "success"
            });
            loadRoom()
        }
    });
}


function close_add(){
    $('#room_name_error').text("")
    $('#room_price_error').text("")
    $('#room_pax_error').text("")
    clear_form()
}

function clear_form(){
    $('#name').val("")
    $('#price').val("")
    $('#pax').val("")
}