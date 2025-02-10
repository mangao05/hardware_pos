$(document).ready(() => {
  var currentdate = new Date().toISOString().split('T')[0];
  for (var year = 2025; year <= 2030; year++) {
    $("#yearSelect").append(`<option value="${year}">${year}</option>`);
  }

  loadReport(`/api/reports/rooms-status?date=${currentdate}`)
});



let chart; // Global chart variable

function selectFilter() {
  var type = $("#filterSelect").val();

  if (type === "per_year") {
      $("#yearSelect").show(); 
      $("#daterange").hide(); 
  } else if(type === "date_range") {
      $("#yearSelect").hide(); 
      $("#daterange").show(); 
  }else{
      $("#yearSelect").hide(); 
      $("#daterange").hide(); 
  }
}

function selectYear(){
  var year = $('#yearSelect').val()
  
  loadReport(`api/reports/rooms-status?year=${year}`)
}


async function loadReport(myUrl) {
  const res = await get_data(myUrl);
  
  const sales_summary = res.data.sales_summary
  
  drawCharts(sales_summary)
  $('#report_room').empty();
    res.data.room_statuses.forEach(element => {
    const percent = (element.occupied / element.total) * 100 || 0; // Handle divide-by-zero

    const row = `
      <div class="col-2 p-1">
        <div class="card" style="box-shadow: 10px 10px 15px rgba(0, 0, 0, 0.3);">
          <div class="card-header" style="background:#205099;color:white">
            <strong><small>${element.category_name}</small></strong>
          </div>
          <small>
          <div class="card-body">
            <div class="row">
              <div class="col">Vacant</div>
              <div class="col-4 text-end" style="font-size:14px"><strong class="text-danger">${element.available}</strong></div>
            </div>
            <div class="row">
              <div class="col">Occupied</div>
              <div class="col-4 text-end" style="font-size:14px"><strong class="text-success">${element.occupied}</strong></div>
            </div>
            <div class="row">
              <div class="col">Out of Service</div>
              <div class="col-4 text-end" style="font-size:14px"><strong class="text-default">${element.out_of_service}</strong></div>
            </div>
            <hr>
            <div class="row" style="background:linen">
              <div class="col">Total</div>
              <div class="col-4 text-end" style="font-size:14px">${element.total}</div>
            </div>
            
            <div class="row" style="background:#205099;color:white;font-weight:bold">
              <div class="col">Percentage</div>
              <div class="col-5 text-end" style="font-size:14px">${percent.toFixed(2)}%</div>
            </div>
          </div>
          </small>
        </div>
      </div>
    `;

    $('#report_room').append(row);
  });
}


document.addEventListener("DOMContentLoaded", function () {
  google.charts.load('current', {packages: ['corechart']});
  // google.charts.setOnLoadCallback(drawCharts);
});

function drawCharts(sales_summary) {
  drawChart(sales_summary);
  drawChartRoom();
}

function drawChartRoom() {
    if (!google.visualization) {
      console.error("Google Charts failed to load.");
      return;
    }

  // JSON data (your room dataset)
    const roomData = [
      { "room_id": 9, "total_bookings": 1, "total_sales": 7000 },
      { "room_id": 22, "total_bookings": 1, "total_sales": 6400 },
      { "room_id": 23, "total_bookings": 1, "total_sales": 2500 },
      { "room_id": 24, "total_bookings": 1, "total_sales": 1000 },
      { "room_id": 25, "total_bookings": 1, "total_sales": 1500 },
      { "room_id": 26, "total_bookings": 1, "total_sales": 1000 },
      { "room_id": 27, "total_bookings": 1, "total_sales": 14000 },
      { "room_id": 28, "total_bookings": 1, "total_sales": 15000 },
      { "room_id": 29, "total_bookings": 1, "total_sales": 6400 },
      { "room_id": 30, "total_bookings": 1, "total_sales": 7000 },
      { "room_id": 31, "total_bookings": 1, "total_sales": 3000 },
      { "room_id": 32, "total_bookings": 1, "total_sales": 3000 },
      { "room_id": 33, "total_bookings": 1, "total_sales": 2000 },
    ];

    // Convert JSON to Google Charts format
    const chartData = [['Room ID', 'Total Sales']];
    roomData.forEach(room => {
      chartData.push([`Room ${room.room_id}`, room.total_sales]);
    });

    const data = google.visualization.arrayToDataTable(chartData);

    const options = {
      title: 'Room Sales',
      backgroundColor: 'transparent',
      legend: { position: 'bottom', textStyle: { color: 'black' } }
    };

    const chart = new google.visualization.BarChart(document.getElementById('myChart_room'));
    chart.draw(data, options);
}

function drawChart(sales_summary) {
  var tag = []
  if (!google.visualization) {
    console.error("Google Charts failed to load.");
    return;
  }
  
  
  const chartData = [['User', 'Sales']]; // Header row
  sales_summary.forEach(item => {
    tag.push(item.sales)
    chartData.push([item.user_name, item.sales]);
  });

  sum = tag.reduce((acc, val) => acc + val, 0);

  if (sum === 0) {
    document.getElementById('myChart').innerHTML = '<div style="text-align: center; font-size: 16px; color: gray;">No records available</div>';
    return;
  }

  
  console.log(sum);
  

  const data = google.visualization.arrayToDataTable(chartData);

  const options = {
    title: 'Sales Summary',
    is3D: true,
    backgroundColor: 'transparent', 
    legend: { position: 'bottom', textStyle: { color: 'black' } },
    titleTextStyle: { color: 'black' }
  };

  const chart = new google.visualization.PieChart(document.getElementById('myChart'));
  chart.draw(data, options);

  // Click Event Listener for Modal
  google.visualization.events.addListener(chart, 'select', function () {
    const selectedItem = chart.getSelection()[0];
    if (selectedItem && selectedItem.row !== null) {
      const userName = chartData[selectedItem.row + 1][0]; 
      const sales = chartData[selectedItem.row + 1][1]; 
      
      // Update modal content
      document.getElementById('modalTitle').innerText = `Details for ${userName}`;
      // document.getElementById('modalBody').innerHTML = `<p><strong>Sales:</strong> ${sales}</p>`;

      // Show Bootstrap Modal
      var myModal = new bootstrap.Modal(document.getElementById('chartModal'));
      myModal.show();
    }
  });
}


$(function() {
  $('input[name="daterange"]').daterangepicker({
      opens: 'left'
  }, function(start, end, label) {
      start_book = start.format('YYYY-MM-DD')
      end_book = end.format('YYYY-MM-DD')
      
      loadReport(`/api/reports/rooms-status?from=${start.format('MM-DD-YYYY')}&to=${end.format('MM-DD-YYYY')}`)
  });
});


function printDiv(divId) {
  const printContents = document.getElementById(divId).innerHTML;
  const originalContents = document.body.innerHTML;

  // Temporarily change the page content to only the div's content
  document.body.innerHTML = printContents;

  // Show elements with class 'rules_regulation' (if any)
  $('.rules_regulation').show();
  $('.customers_copy').show()

  // Log before printing
  console.log("Attempting to print...");

  // Add event listeners for beforeprint and afterprint
  window.addEventListener("beforeprint", function() {
      console.log("Print dialog is opening...");
  });

  window.addEventListener("afterprint", function() {
      console.log("Print dialog has closed.");
      // You can infer the action here (e.g., print was completed or canceled)
      document.body.innerHTML = originalContents;
      window.location.reload();
  });

  // Trigger the print dialog
  window.print();
}
