@extends('homepage')

@section('header')
    Room Category
@endsection

@section('content')


<div class="container">
    <div class="row border position-relative header-content" style="background-image: url('../img/header/header2.jpeg');">
    
    </div>
    <div class="row pt-2">
        <div class="col text-end"><button class="btn button-success" data-bs-toggle="modal" data-bs-target="#exampleModal">Create Rooms Category</button></div>
    </div>

    @include('modal.room-category-management.room_category_create')

    <div class="row">
        <div class="col-12"> 
            <table class="table table-hover">
                <thead>
                    <th>Name</th>
                    <th>Bed Types</th>
                    <th>Near At</th>
                    <th>Description</th>
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
