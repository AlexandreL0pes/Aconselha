import { sendRequest } from "./utils.js";

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

const obterCoef = () => {
  const turma = localStorage.getItem("turmaAtual") || "20201.03INI10I.3A";

  if (turma !== null) {
    const dados = {
      acao: "Turmas/obterQuantidadeConficienteGeral",
      turma: turma,
    };

    sendRequest(dados)
      .then((response) => {
        console.log(response);
        gerarGraficoCoef(response.alto, response.medio, response.baixo);
      })
      .catch((err) => {
        console.error(err);
      });
  }
};

obterCoef();
