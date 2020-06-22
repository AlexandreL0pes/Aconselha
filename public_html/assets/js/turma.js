var ctx = document.getElementById("myChart").getContext("2d");
var myChart = new Chart(ctx, {
  type: "doughnut",
  responsive: true,
  data: {
    datasets: [
      {
        data: [10, 20, 30],
        backgroundColor: ["#28ac47", "#3d21f1", "#f22f2f"],
      },
    ],
    labels: ["Alto", "Médio", "Baixo"],

    // These labels appear in the legend and in the tooltips when hovering different arcs
  },

  options: {
    legend: {
      display: false,
    },
    elements: {
        center: {
            text: "54"
        }
    },
    cutoutPercentage: 70
  },
});
