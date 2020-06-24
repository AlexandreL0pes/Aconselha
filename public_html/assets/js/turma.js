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

const obterInfoTurma = () => {
  const turma = localStorage.getItem("turmaAtual");

  if (turma) {
    let dados = { acao: "Turmas/informacoesTurma", turma: turma };

    sendRequest(dados)
      .then((response) => {
        console.log(response);
        apresentarInfoTurma(response);
      })
      .catch((err) => {
        console.error(object);
      });

    dados = { acao: "Turmas/obterEstatistica", turma: turma };
    sendRequest(dados)
      .then((response) => {
        console.log(response);
        apresentarEstatisticasTurma(response);
      })
      .catch((err) => {
        console.error(err);
      });
  }
};

const apresentarEstatisticasTurma = (estatistica) => {
  const estatisticaTurma = document.getElementById("estatistica-turma");

  estatisticaTurma.querySelector(".coef-geral .resultado").innerHTML =
    estatistica.coeficiente_geral;
  estatisticaTurma.querySelector(".experiencia .resultado").innerHTML =
    estatistica.aprendizados;
  estatisticaTurma.querySelector(".aprendizado .resultado").innerHTML =
    estatistica.experiencias;
  estatisticaTurma.querySelector(".medidas .resultado").innerHTML =
    estatistica.medidas_disciplinares;
};

const apresentarInfoTurma = (turma) => {
  const turmaInfo = document.querySelector(".overview .info .turma-info");

  turmaInfo.querySelector("#nome").innerHTML = turma.nome;
  turmaInfo.querySelector("#curso").innerHTML = turma.curso;

  turmaInfo.querySelector("#codigo").innerHTML = turma.codigo;

  if (turma.lideres.conselheiro.length > 0) {
    turmaInfo.querySelector(".conselheiro").innerHTML =
      turma.lideres.conselheiro.nome;
  }

  if (turma.lideres.representante.length > 0) {
    turmaInfo.querySelector(".representante").innerHTML =
      turma.lideres.representante.nome;
  }
  if (turma.lideres.vice.length > 0) {
    turmaInfo.querySelector(".vice-representante").innerHTML =
      turma.lideres.vice.nome;
  }
};
obterCoef();
obterInfoTurma();
