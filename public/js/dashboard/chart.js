let chart; // Global chart variable

async function loadReport() {
  const myUrl = "/api/reports/rooms-status";
  const res = await get_data(myUrl);

  const x = [];
  const y = [];
  const colors = [];
  const colorPalette = ["#4CAF50", "#FF9800", "#2196F3", "#F44336"]; // Four different colors

  res.data.sales_summary.forEach((sales, index) => {
    x.push(sales.user_name);
    y.push(sales.sales);
    colors.push(colorPalette[index % colorPalette.length]); // Cycle through colors
  });

  // Update chart data dynamically
  updateChart(x, y, colors);

  $('#report_room').empty();
  res.data.room_statuses.forEach(element => {

    const percent = element.occupied/element.total * 100
    const row = `
      <div class="col-2 p-1">
        <div class="card" style="box-shadow: 10px 10px 15px rgba(0, 0, 0, 0.3);">
          <div class="card-header">
            <strong>${element.category_name}</strong>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col">Vacant</div>
              <div class="col-4 text-end" style="font-size:14px">${element.available}</div>
            </div>
            <div class="row">
              <div class="col">Occupied</div>
              <div class="col-4 text-end" style="font-size:14px">${element.occupied}</div>
            </div>
            <div class="row">
              <div class="col">Out of Service</div>
              <div class="col-4 text-end" style="font-size:14px">${element.out_of_service}</div>
            </div>
            <hr>
            <div class="row">
              <div class="col">Total</div>
              <div class="col-4 text-end" style="font-size:14px">${element.total}</div>
            </div>
            
            <div class="row">
              <div class="col">Percentage</div>
              <div class="col-4 text-end" style="font-size:14px">${percent}%</div>
            </div>
          </div>
        </div>
      </div>
    `;

    $('#report_room').append(row);
  });
}


function initializeChart() {
  
  const ctx = document.getElementById("myChart").getContext("2d");
  chart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: [], // Empty initially
      datasets: [{
        backgroundColor: [],
        data: []
      }]
    },
    options: {
      legend: {display: false},
        title: {
          display: true,
          text: "Sales Report"
        }
      }
  });
}

function updateChart(labels, data, colors) {
  if (chart) {
    chart.data.labels = labels;
    chart.data.datasets[0].data = data;
    chart.data.datasets[0].backgroundColor = colors;
    chart.update(); // Dynamically update the chart
  }
}

$(document).ready(() => {
  var rolesInputValue = JSON.parse(document.querySelector('input[name="roles_input"]').value);
  if (rolesInputValue.includes(12)) {
    initializeChart();
  } 
  loadReport(); // Load data and update the chart
});
