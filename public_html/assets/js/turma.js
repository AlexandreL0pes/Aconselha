const gerarGraficoCoef = (qtdAlto, qtdMedio, qtdBaixo) => {
  var ctx = document.getElementById("coef-geral").getContext("2d");
  var myChart = new Chart(ctx, {
    type: "doughnut",
    responsive: true,
    data: {
      datasets: [
        {
          data: [qtdAlto, qtdMedio, qtdBaixo],
          backgroundColor: ["#28ac47", "#3d21f1", "#f22f2f"],
        },
      ],
      labels: ["Alto", "Médio", "Baixo"],
    },

    options: {
      legend: {
        display: false,
      },
      cutoutPercentage: 70,
    },
  });
};

gerarGraficoCoef(1,0,0);