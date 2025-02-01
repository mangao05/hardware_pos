@extends('homepage')

@section('header')
    Booking
@endsection

@section('content')
<style scoped>
    .border {
    border: 1px solid #ddd;
    }

    .reservation-cell {
        color: black;
        font-weight: bold;
        text-align: center;
        padding: 5px;
    }

    .reservation-block {
        color: white;
        font-weight: bold;
        text-align: center;
        margin: 2px 0;
        display: inline-block;
        width: 90%;
    }

    #calendar_book tbody td {
        height: 40px; /* Adjust height as needed */
        vertical-align: middle; /* Ensure content aligns vertically in the center */
    }

    /* Styling for reservation cells */
    .bg-color-4 {
        background-color: #8b008b; /* Dark magenta background for reservations */
        color: white; /* White text */
        border-radius: 4px; /* Rounded corners */
        text-align: center; /* Center text horizontally */
        font-size: 12px; /* Font size for uniformity */
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 2px;
        text-align: left;
        vertical-align: top;
    }

    thead th {
        background-color: #f4f4f4;
        font-weight: bold;
    }

    /* Make the table scrollable */
    #calendar_book {
        overflow-x: auto;
        display: block;
        white-space: nowrap; /* Prevent wrapping for cells */
        position: relative;
    }

    /* Sticky first column for both header and body */
    #calendar_book tbody td:first-child,
    #calendar_book thead th:first-child {
        position: sticky;
        left: 0;
        
        background-color: #f8f9fa; /* Optional: Background color for the sticky column */
        border-right: 1px solid #ddd; /* Optional: Add a border to separate it */
        white-space: nowrap;
    }

    /* Fix sticky positioning for header */
    #calendar_book thead th:first-child {
        z-index: 3; /* Higher z-index for header */
        top: 0;
        position: sticky;
    }

    /* Ensure rowspan cells in the first column behave correctly */
    #calendar_book tbody td[rowspan] {
        position: sticky;
        left: 0;
        z-index: 2;
        background-color: #f8f9fa; /* Match background for rowspan cells */
    }

    /* Weekend styling */
    #calendar_book thead th.weekend {
        background-color: #ffe6e6; /* Highlight weekends */
    }

    /* Ensure cells are aligned correctly */
    #calendar_book tbody td {
        vertical-align: middle;
        text-align: center;
        height: 25px;
        border: 1px solid #ccc;
    }

    #calendar_book th, #calendar_book td {
        width: 150px; /* Adjust as needed */
        min-width: 65px;
        max-width: 150px;
        text-align: center;
        vertical-align: middle;
    }
    .sticky-left {
        position: -webkit-sticky; /* For Safari */
        position: sticky;
        left: 0;
        background-color: #fff;
        z-index: 2;
        border-right: 1px solid #ddd;
    }


    /* Modal Header Styling */
    .modal-color {
        background-color: #012866;
    }

    .modal-padding {
        padding: 1rem;
    }

    .text-white {
        color: #fff;
    }

    /* Form Labels */
    .form-label {
        font-weight: bold;
        font-size: 0.9rem;
    }

    /* Room List Dropdown */
    .room_list {
        font-size: 0.9rem;
    }

    /* Small Text Styling */
    small.text-danger {
        font-size: 0.75rem;
    }

    /* Form Inputs */
    input.form-control,
    select.form-control,
    textarea.form-control {
        font-size: 0.9rem;
    }

    /* Buttons */
    .btn-primary {
        background-color: #012866;
        border-color: #012866;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .card{
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Soft shadow */
    }

    #view_summary_modal{
        padding-left: 0px !important
    }

    .summary_label{
        text-align: left;
        font-size: 14px
    }

    .table-custome-align{
        text-align: left;
        font-size: 14px
    }


</style>
<div class="container-fluid">
    <div class="row mb-2 justify-content-end">
        <div class="col-2 text-end">
            <select name="" class="form-control category_list"></select>
        </div>
        <div class="col-2 text-end">
            <input class="form-control" type="month" id="datePicker" />
        </div>
        <div class="col-1 text-end" style="padding-right:2px">
            <button class="btn button-success w-100" style="margin-top: 3px;" onclick="walk_in()">Walk In</button>
        </div>
        <div class="col-1 text-end" style="padding-left:2px">
            <button class="btn button-success w-100" style="margin-top: 3px;" onclick="add_booking_modal()">Add New</button>
        </div>

        @include('modal.booking.add_booking')
        @include('modal.booking.edit_booking')
        @include('modal.booking.view_summary')
        @include('modal.booking.walk_in')
    </div>
    <div class="row">
        <div class="col-12" style="overflow: auto"> 
            <table class="" id="calendar_book">
                <thead>
                    
                </thead>
                <tbody>
                   
                </tbody>
            </table>
        </div>
    </div>
</div>
    

@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.8/dayjs.min.js"></script>
    <script src="{{ asset('js/features/booking-js/booking_action.js') }}"></script>
    <script src="{{ asset('js/features/booking.js') }}"></script>
    <script src="{{ asset('js/helper/app_helper.js') }}"></script>
    <script src="{{ asset('js/features/booking-js/summary.js') }}"></script>
    <script src="{{ asset('js/features/booking-js/walk_in.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    
@endsection
