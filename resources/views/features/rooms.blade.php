@extends('homepage')

@section('header')
    Rooms
@endsection

@section('content')


<div class="container">
    <div class="row border position-relative header-content" style="background-image: url('../img/header/header1.jpg');">
    
    </div>
    <div class="row pt-2">
        <div class="col text-end"><button class="btn button-success" data-bs-toggle="modal" data-bs-target="#room_modal">Create Room</button></div>
    </div>

    @include('modal.rooms-management.room_create')
    @include('modal.rooms-management.room_edit')

    <div class="row">
        <div class="col-12"> 
            <table class="table table-hover" id="data-table-rooms">
                <thead>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Type</th>
                    <th>Pax</th>
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
    <script src="{{ asset('js/features/rooms_management.js') }}"></script>
    <script src="{{ asset('js/helper/app_helper.js') }}"></script>
@endsection
