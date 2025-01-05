<!-- Modal -->
<div class="modal fade" id="add_booking" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header text-white modal-color modal-padding">
                <h5 class="modal-title" id="exampleModalLabel">Reservation Info</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col">
                        <h6 class="fw-bold">Booking Details</h6>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="daterange" class="form-label">Check-in Date:</label>
                        <input type="text" name="daterange" id="daterange" class="form-control" placeholder="Select date range" />
                    </div>

                    <div class="col-md-6">
                        <label for="room" class="form-label">Category:</label>
                        <select class="form-control room_list category_list" id="category" data-live-search="true">
                            
                        </select>
                        <small id="room_error" class="text-danger"></small>
                        <div class="row room_list_data pt-2">
                            
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name:</label>
                            <input type="text" class="form-control text-capitalize" id="name" placeholder="Enter full name">
                            <small id="name_error" class="text-danger"></small>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address:</label>
                            <input type="text" class="form-control text-capitalize" id="address" placeholder="Enter address">
                            <small id="address_error" class="text-danger"></small>
                        </div>

                        <div class="mb-3">
                            <label for="nationality" class="form-label">Nationality:</label>
                            <select id="nationality" class="nationality_list form-control">
                                <option value="" selected hidden>-- Select Nationality --</option>
                            </select>
                            <small id="nationality_error" class="text-danger"></small>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control" id="email" placeholder="Enter email">
                            <small id="email_error" class="text-danger"></small>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone:</label>
                            <input type="number" class="form-control" id="phone" placeholder="Enter phone number">
                            <small id="phone_error" class="text-danger"></small>
                        </div>

                        <div class="mb-3">
                            <label for="bookingType" class="form-label">Type:</label>
                            <select name="" id="bookingType" class="form-control">
                                <option value="" selected hidden>-- Select Type --</option>
                                <option value="Walk-in">WALK IN</option>
                                <option value="No Walk-in">NO WALK IN</option>
                            </select>
                            <small id="bookingType_error" class="text-danger"></small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="mb-3">
                    <label for="remarks" class="form-label">Remarks:</label>
                    <textarea class="form-control" id="remarks" cols="30" rows="4" placeholder="Enter remarks or additional information"></textarea>
                    <small id="remarks_error" class="text-danger"></small>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer modal-color modal-padding">
                <button type="button" class="btn btn-secondary" onclick="close_add_bookig()" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="add_booking()">Create</button>
                {{-- <button type="button" class="btn btn-primary" id="getCheckedRooms">Create</button> --}}
            </div>
        </div>
    </div>
</div>
