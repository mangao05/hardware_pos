$(document).ready(function() {
    loadRoomCategory()
});



async function loadRoomCategory(){
    myUrl = "room-categories"
    try {
        const response = await axios.get(myUrl);
        $("#data-table-rooms_category tbody").empty();
        response.data.categories.data.forEach(item => {
            const row = `
                <tr>
                    <td>${item.display_name}</td>
                    <td>${item.bed_type}</td>
                    <td>${item.near_at}</td>
                    <td>${item.description}</td>
                    <td>
                        ${item.availability == 0?`<span class="badge bg-danger">Inactive</span>`:`<span class="badge bg-success">Active</span>`}
                        
                    </td>
                    <td>
                        <span class="badge bg-primary edit-button" data-user='${JSON.stringify(item)}' onclick="edit_room_category(this)">Edit</span>
                        <span class="badge bg-danger delete-button" onclick="delete_room_category(${item.id})">Delete</span>
                    </td>
                </tr>
            `;
            $("#data-table-rooms_category tbody").append(row);
        });
        
    } catch (error) {
        if (error.response) {
            return error.response.data
        }
    }
}


function store_room_category(){
    let room_category_name = $('#room_category_name').val()
    let room_category_type = $('#room_category_type').val()
    let room_category_near_at = $('#room_category_near_at').val()
    let room_category_description = $('#room_category_description').val()
    let is_available = $('#room_category_is_available').prop('checked');

    var myUrl = 'room-categories';

    var myData = {
        display_name:room_category_name,
        bed_type:room_category_type,
        near_at:room_category_near_at,
        description:room_category_description,
        availability:is_available,
    };
   
    store_data(myUrl, myData).then(response => {
        // close_add_user()
        if (response && response.errors) {
            response.errors['display_name']?.[0] && $('#room_category_name_error').text(response.errors['display_name'][0]);
            response.errors['bed_type']?.[0] && $('#room_category_type_error').text(response.errors['bed_type'][0]);
        } else {
            loadRoomCategory()
            clear_form()
            $("#room_category_modal").modal('hide')
        }
    })
}

function edit_room_category(element){
    const data = JSON.parse(element.getAttribute('data-user'));
    
    $('#room_category_name_edit').val(data['display_name'])
    $('#room_category_type_edit').val(data['bed_type'])
    $('#room_category_near_at_edit').val(data['near_at'])
    $('#room_category_description_edit').val(data['description'])
    $('#room_category_id_edit').val(data['id'])

    isActive = data['availability']

    if(isActive == true){
        $('#room_category_is_available_edit').prop('checked',true)
    }else{
        $('#room_category_is_available_edit').prop('checked',false)
    }
    
    $('#room_category_modal_edit').modal('show')
   
}

function update_room_category(){
    const room_category_id = $('#room_category_id_edit').val()

    let room_category_name = $('#room_category_name_edit').val()
    let room_category_type = $('#room_category_type_edit').val()
    let room_category_near_at = $('#room_category_near_at_edit').val()
    let room_category_description = $('#room_category_description_edit').val()
    let is_available = $('#room_category_is_available_edit').prop('checked');

    var myData = {
        display_name:room_category_name,
        bed_type:room_category_type,
        near_at:room_category_near_at,
        description:room_category_description,
        availability:is_available,
    };
    
    url = "/api/room-categories/"+room_category_id

    update_data(url,myData).then(response => {
        toaster("Room Category successfully updated!","success")
        $('#room_category_modal_edit').modal('hide')
        loadRoomCategory()
    })
}

function delete_room_category(room_category){
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
            myUrl = "/api/room-categories/"+room_category
            delete_record(myUrl)
            Swal.fire({
            title: "Deleted!",
            text: "Your file has been deleted.",
            icon: "success"
            });
            loadRoomCategory()
        }
    });
}


function close_add(){
    $('#room_category_name_error').text("")
    $('#room_category_type_error').text("")
    clear_form()
}

function clear_form(){
    $('#room_category_name').val("")
    $('#room_category_type').val("")
}