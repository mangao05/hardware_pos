@extends('homepage')

@section('header')
    Rooms
@endsection

@section('content')


<div class="container">
    <div class="row border position-relative header-content" style="background-image: url('../img/header/header1.jpg');">
    
    </div>
    <div class="row pt-2">
        <div class="col text-end"><button class="btn button-success" data-bs-toggle="modal" data-bs-target="#exampleModal">Create Room</button></div>
    </div>

    @include('modal.rooms-management.room_create')

    <div class="row">
        <div class="col-12"> 
            <table class="table table-hover">
                <thead>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Type</th>
                    <th>Pax</th>
                    <th>Is Available?</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    <tr>
                        <td>test</td>
                        <td>test</td>
                        <td>test</td>
                        <td>test</td>
                        <td>
                            <span class="badge bg-danger">No</span>
                        </td>
                        <td>
                            <span class="badge bg-primary">Edit</span>
                            <span class="badge bg-danger">Delete</span>
                        </td>
                    </tr>
                    <tr>
                        <td>test</td>
                        <td>test</td>
                        <td>test</td>
                        <td>test</td>
                        <td>
                            <span class="badge bg-success">Yes</span>
                        </td>
                        <td>
                            <span class="badge bg-primary">Edit</span>
                            <span class="badge bg-danger">Delete</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
