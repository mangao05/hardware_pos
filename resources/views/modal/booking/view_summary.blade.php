<div class="modal fade" id="view_summary_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="exampleModalLabel">Payment</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body summary-div">
      <div class="row">
        <div class="col-md-3 border">
          <div class="p-3">
            <strong>
              Room List
            </strong>
            <ul>
              <li>Room 1</li>
              <li>Room 2</li>
              <li>Room 3</li>
              <li>Room 4</li>
            </ul>
          </div>

          <div class="p-3">
            <strong>
              Add-Ons List
            </strong>
            <ul>
              <li>Add-ons1</li>
              <li>Add-ons2</li>
              <li>Add-ons3</li>
              <li>Add-ons4</li>
            </ul>
          </div>
            
        </div>
        <div class="col-md-6 border p-4">
          <div class="row border card">
            <div class="col" id="printableArea">
              <img src="{{ asset('img/pantukan_logo.png') }}" class="mt-3" style="width: 70px" alt="">
              <hr>
              <div class="row">
                <div class="col">
                  <small>
                    <strong>
                      Client Details
                    </strong>
                  </small>
                  <table>
                    <tbody>
                      <tr>
                        <td class="summary_label">Name:</td><td style="text-align: left">Khenneth S Alaiza</td>
                      </tr>
                      <tr>
                        <td class="summary_label">Email:</td><td style="text-align: left">khenneth.alaiza@gmail.com</td>
                      </tr>
                      <tr>
                        <td class="summary_label">Contact No:</td><td style="text-align: left">09171232958</td>
                      </tr>
                      <tr>
                        <td class="summary_label">Address:</td><td style="text-align: left">Tanza Cavite</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="col">
                  <small>
                    <strong>
                      Booking Details
                    </strong>
                    <table>
                      <tbody>
                        <tr>
                          <td class="summary_label">Checkin Date:</td><td style="text-align: left">Jan 23, 2025</td>
                        </tr>
                        <tr>
                          <td class="summary_label">Checkout Date:</td><td style="text-align: left">Jan 27, 2025</td>
                        </tr>
                        <tr>
                          <td class="summary_label">Total Pax:</td><td style="text-align: left">2</td>
                        </tr>
                        <tr>
                          <td class="summary_label">Total Guests(s):</td><td style="text-align: left">0</td>
                        </tr>
                      </tbody>
                    </table>
                  </small>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col">
                  <small>
                    <strong>
                      Order Details
                    </strong>
                    <table class="table">
                      <thead>
                        <th class="table-custome-align">Item Name</th>
                        <th class="table-custome-align">Category</th>
                        <th class="table-custome-align">Qty</th>
                        <th class="table-custome-align">Price</th>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="table-custome-align">Room1</td>
                          <td class="table-custome-align">Category1</td>
                          <td class="table-custome-align">2</td>
                          <td class="table-custome-align">P2000</td>
                        </tr>
                      </tbody>
                    </table>
                  </small>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col">
              test
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" onclick="{$('#view_summary_modal').modal('hide')}" data-bs-dismiss="modal">Close</button>
      <button type="button" onclick="printDiv('printableArea')" class="btn btn-primary">Save changes</button>
    </div>
  </div>
  </div>
</div>