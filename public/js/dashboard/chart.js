var xValues = ["Italy", "France", "Spain", "USA", "Argentina"];
var yValues = [55, 49, 44, 24, 15];
var barColors = ["red", "green","blue","orange","brown"];

new Chart("myChart", {
  type: "bar",
  data: {
    labels: xValues,
    datasets: [{
      backgroundColor: barColors,
      data: yValues
    }]
  },
//   options: {...}
});


async function loadReport(){
  const myUrl = "/api/reports/rooms-status"
  const res = await get_data(myUrl);
  
  $('#report_room').empty()
  res.data.forEach(element => {
      console.log(element);
      const row = `
        <div class="col-2 p-1">
          <div class="card" style="box-shadow: 10px 10px 15px rgba(0, 0, 0, 0.3);">
            <div class="card-header">
              <strong>${element.category_name}</strong>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col">Vacant</div>
                <div class="col-3 text-end">${element.available}</div>
              </div>
              <div class="row">
                <div class="col">Occupied</div>
                <div class="col-3 text-end">${element.occupied}</div>
              </div>
              <div class="row">
                <div class="col">Out of Service</div>
                <div class="col-3 text-end">${element.out_of_service}</div>
              </div>
              <hr>
              <div class="row">
                <div class="col">Total</div>
                <div class="col-3 text-end">${element.total}</div>
              </div>
              
              <div class="row">
                <div class="col">Percentage</div>
                <div class="col-3 text-end">0</div>
              </div>
              
            </div>
          </div>

        </div>
      `

      $('#report_room').append(row)
  });
}


$(document).ready(() => {
    loadReport()
});