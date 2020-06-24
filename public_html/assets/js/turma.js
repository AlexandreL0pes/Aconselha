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

    dados = { acao: "Perfis/listarPerfisRelevantes", turma: turma };

    sendRequest(dados)
      .then((response) => {
        console.log(response);
        apresentarPrincipaisAvaliacoes(response);
      })
      .catch((err) => {
        console.error(err);
      });

    dados = { acao: "Turmas/obterMedidasDisciplinares", turma: turma };

    sendRequest(dados)
      .then((response) => {
        console.log(response);
        apresentarMedidas(response);
      })
      .catch((err) => {
        console.error(err);
      });
  }
};

const apresentarPrincipaisAvaliacoes = (dados) => {
  console.log(dados[0]);
  if (dados[0].nome !== undefined) {
    const avaliacoes = document.getElementById("avaliacoes");
    dados.map((perfil) => {
      avaliacoes.appendChild(gerarChip(perfil));
    });
  } else {
    const avaliacoes = document.getElementById("avaliacoes");
    avaliacoes.innerHTML = "Nenhuma avaliação foi encontrada";
  }
};

const gerarChip = (perfil) => {
  const chip = document.createElement("span");
  chip.classList.add("chip");

  if (perfil.tipo === "1") {
    chip.classList.add("positivo");
  } else {
    chip.classList.add("negativo");
  }

  chip.innerText = perfil.nome;

  return chip;
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

const apresentarMedidas = (medidas) => {
  if (medidas[0].cod_medida) {
    const lista_medidas = document.querySelector(".lista-medidas");
    medidas.map((medida) => {
      lista_medidas.appendChild(gerarMedida(medida));
    });
  }
};

const gerarMedida = (medida) => {
  const medidaDiv = document.createElement("div");
  medidaDiv.classList.add("medida");
  medidaDiv.setAttribute("cod-medida", medida.cod_medida);

  const meses = [
    "Jan",
    "Fev",
    "Mar",
    "Abr",
    "Mai",
    "Jun",
    "Jul",
    "Ago",
    "Set",
    "Out",
    "Nov",
    "Dez",
  ];
  const data = new Date(medida.data);
  const dataFormatada = `${
    meses[data.getMonth()]
  } ${data.getFullYear().toString().substr(-2)}`;
  
  const content = `
  <div class="descricao">
    <p class="nome">${medida.aluno.nome}</p>
    <p class="tipo-medida">${medida.descricao}</p>
  </div>
  <span class="data">${dataFormatada}</span>
  `;

  medidaDiv.innerHTML = content;

  return medidaDiv;
};

obterCoef();
obterInfoTurma();
