@extends('homepage')

@section('header')
    User Management
@endsection

@section('content')


<div class="container">
    <div class="row pt-2">
        <div class="col text-end"><button class="btn button-success" data-bs-toggle="modal" data-bs-target="#exampleModal">Create User</button></div>
    </div>

    @include('modal.user-management.user_create')

    <div class="row">
        <div class="col-12"> 
            <table class="table table-hover">
                <thead>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Role</th>
                    <th>Is Active</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    <tr>
                        <td>test</td>
                        <td>test</td>
                        <td>test</td>
                        <td>test</td>
                        <td>
                            <span class="badge bg-danger">Inactive</span>
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
                            <span class="badge bg-success">Active</span>
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
