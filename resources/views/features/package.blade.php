@extends('homepage')

@section('header')
    Package
@endsection

@section('content')


<div class="container">
    <div class="row border position-relative header-content" style="background-image: url('../img/header/header1.jpg');">
    
    </div>
    <div class="row pt-2">
        <div class="col text-end"><button class="btn button-success" data-bs-toggle="modal" data-bs-target="#add_package_modal">Create Package</button></div>
    </div>

    @include('modal.package-management.package_create')
    @include('modal.package-management.package_edit')

    <div class="row">
        <div class="col-12"> 
            <table class="table table-hover" id="package_table_list">
                <thead>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Is Available?</th>
                    <th>Action</th>
                </thead>
                <tbody>
                   
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


@section('js')
    <script src="{{ asset('js/features/package_management.js') }}"></script>
    <script src="{{ asset('js/helper/app_helper.js') }}"></script>
@endsection
