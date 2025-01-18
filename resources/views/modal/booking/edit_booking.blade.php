<!-- Modal -->
<div class="modal fade" id="edit_booking" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header text-white modal-color modal-padding">
                <h5 class="modal-title" id="exampleModalLabel">Reservation Info Edit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7 border-end">
                        <div class="row mb-3">
                            <div class="col">
                                <h6 class="fw-bold">Booking Details (<span class="text-capitalize" id="booking_status"></span>)</h6>
                            </div>
                        </div>
        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div>
                                    <label for="daterange" class="form-label">Check-in Date:</label>
                                    <input type="text" name="daterange" id="edit_daterange" class="form-control" placeholder="Select date range" disabled />
                                </div>
                                <div>
                                    <label for="room" class="form-label">Category:</label>
                                    <select class="form-control room_list category_list" id="edit_category" data-live-search="true" disabled>
                                        
                                    </select>
                                    <small id="room_error" class="text-danger"></small>
                                </div>
                                <div class="select_room_div" style="display: none">
                                    <label for="room" class="form-label">Room:</label>
                                    <select class="form-control room_list room_list_data room_list_selection room_list_data" id="edit_room_list_selection" data-live-search="true">
                                        
                                    </select>
                                    <small id="room_error" class="text-danger"></small>
                                </div>
                                
                            </div>
        
                            <div class="col-md-6">
                                <label for="daterange" class="form-label">Room List:</label>

                                <div class="row">
                                    <div class="col">
                                        <span id="edit_current_room">

                                        </span>
                                    </div>
                                    <div class="col-md-6">
                                        Guest:<input type="number" class="form-control">
                                    </div>
                                    
                                    
                                </div>
                                <hr>
                                <div>
                                    <small>
                                        <ul id="edit_room_list_display" style="margin-left: 17px;">

                                        </ul>
                                    </small>
                                    
                                </div>

                            </div>
                        </div>
        
                        <hr class="my-1">
        
                        <div class="row g-3">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name:</label>
                                    <input type="text" class="form-control text-capitalize" id="edit_name" placeholder="Enter full name" disabled>
                                    <small id="name_error" class="text-danger"></small>
                                </div>
        
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address:</label>
                                    <input type="text" class="form-control text-capitalize" id="edit_address" placeholder="Enter address" disabled>
                                    <small id="address_error" class="text-danger"></small>
                                </div>
        
                                <div class="mb-3">
                                    <label for="nationality" class="form-label">Nationality:</label>
                                    <select id="edit_nationality" class="nationality_list form-control" disabled>
                                        <option value="" selected hidden>-- Select Nationality --</option>
                                    </select>
                                    <small id="nationality_error" class="text-danger"></small>
                                </div>
                            </div>
        
                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email:</label>
                                    <input type="email" class="form-control" id="edit_email" placeholder="Enter email" disabled>
                                    <small id="email_error" class="text-danger"></small>
                                </div>
        
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone:</label>
                                    <input type="number" class="form-control" id="edit_phone" placeholder="Enter phone number" disabled>
                                    <small id="phone_error" class="text-danger"></small>
                                </div>
        
                                <div class="mb-3">
                                    <label for="bookingType" class="form-label">Type:</label>
                                    <select name="" id="edit_bookingType" class="form-control" disabled>
                                        <option value="" selected hidden>-- Select Type --</option>
                                        <option value="walk-in">WALK IN</option>
                                        <option value="online">ONLINE</option>
                                    </select>
                                    <small id="bookingType_error" class="text-danger"></small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-1">
        
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks:</label>
                            <textarea class="form-control" id="edit_remarks" cols="30" rows="4" placeholder="Enter remarks or additional information" disabled></textarea>
                            <small id="remarks_error" class="text-danger"></small>
                        </div>
                        <input type="hidden" id="edit_reservation_id">
                        <input type="hidden" id="edit_room_id">
                        <input type="hidden" id="reservation_room_details_id">
                    </div>
                    <div class="col-md-5">
                        <div class="row mb-3">
                            <div class="col">
                                <h6 class="fw-bold">History Logs</h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                               
                                <table class="table" id="history_logs" style="font-size: 12px">
                                    <thead>
                                        <th>Type</th>
                                        <th>User</th>
                                        <th>Details</th>
                                        <th>Date</th>
                                    </thead>
                                    <tbody>
                                        {{-- <tr>
                                            <td>Book</td>
                                            <td>Juan Dela Cruz</td>
                                            <td>Check-In</td>
                                            <td>Jan 20, 2025 2:45PM</td>
                                        </tr>
                                        <tr>
                                            <td>Add Item</td>
                                            <td>Juan Dela Cruz</td>
                                            <td>Added Cottage Guest</td>
                                            <td>Jan 20, 2025 5:30PM</td>
                                        </tr> --}}
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer modal-color modal-padding">
                <button 
                    type="button" 
                    class="btn btn-success btn_view_summary" 
                    data-reservation_details=""
                    onclick="view_summary()"
                >
                    <img src="{{ asset('img/icon/log-out.png') }}" class="icon-style" alt="">View Summary
                </button>
                <button 
                    type="button" 
                    style="display: none"
                    class="btn btn-success btn_checkin" 
                    onclick="change_status('checkin')">
                    <img src="{{ asset('img/icon/log-out.png') }}" class="icon-style" alt="">Check-In
                </button>

                <button type="button" class="btn btn-danger btn_edit btn_early_check_out" style="display: none" onclick=""><img src="{{ asset('img/icon/log-out.png') }}" class="icon-style" alt="">Early Check-out</button>
                <button type="button" class="btn button-success btn_edit btn_check_out" style="display: none" onclick="change_status('checkout')"><img src="{{ asset('img/icon/left.png') }}" class="icon-style" alt="">Check-out</button>
                {{-- <button type="button" class="btn btn-info" onclick="">Check-In</button> --}}
                <button type="button" class="btn button-success btn_edit" onclick="extend_book()"><img src="{{ asset('img/icon/add.png') }}" class="icon-style" alt="">Extend</button>
                <button type="button" class="btn button-success btn_edit" onclick="add_room()"><img src="{{ asset('img/icon/plus.png') }}" class="icon-style" alt="">Add New Room</button>
                <button type="button" class="btn button-success btn_edit" onclick="transfer_room()"><img src="{{ asset('img/icon/transfer.png') }}" class="icon-style" alt="">Transfer</button>
                <button type="button" class="btn btn-danger btn_edit" onclick="cancelReservation()"><img src="{{ asset('img/icon/file.png') }}" class="icon-style" alt="">Void</button>
                {{-- <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><img src="{{ asset('img/icon/close.png') }}" class="icon-style" alt="">Close</button> --}}
                {{-- <button type="button" class="btn button-success" onclick="">Payment</button>--}}
                <button type="button" class="btn btn-success btn_edit" onclick="update_booking()"><img src="{{ asset('img/icon/diskette.png') }}" class="icon-style" alt="">Update</button> 
            </div>
        </div>
    </div>
</div>
