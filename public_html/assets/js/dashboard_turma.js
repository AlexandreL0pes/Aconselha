import { sendRequest } from "./utils.js";

const listener = () => {
  const btnFiltrarAlto = document.getElementById("filtrarAlto");
  btnFiltrarAlto.addEventListener("click", filtrarAlto);

  const btnFiltrarMedio = document.getElementById("filtrarMedio");
  btnFiltrarMedio.addEventListener("click", filtrarMedio);

  const btnFiltrarBaixo = document.getElementById("filtrarBaixo");
  btnFiltrarBaixo.addEventListener("click", filtrarBaixo);

  const btnRemoverFiltro = document.getElementById("removerFiltro");
  btnRemoverFiltro.addEventListener("click", removerFiltro);
};

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
  const turma = localStorage.getItem("turmaAtual");

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

const obterEstatisticaTurma = () => {
  const turma = localStorage.getItem("turmaAtual");
  if (turma !== null) {
    const dados = {
      acao: "Turmas/obterEstatistica",
      turma: turma,
    };

    sendRequest(dados)
      .then((response) => {
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

const obterPrincipaisAvaliacoes = () => {
  const turma = localStorage.getItem("turmaAtual");
  if (turma !== null) {
    const dados = { acao: "Perfis/listarPerfisRelevantes", turma: turma };

    sendRequest(dados)
      .then((response) => {
        apresentarPrincipaisAvaliacoes(response);
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

const obterMedidas = () => {
  const turma = localStorage.getItem("turmaAtual");

  if (turma) {
    const dados = { acao: "Turmas/obterMedidasDisciplinares", turma: turma };
    sendRequest(dados)
      .then((response) => {
        apresentarMedidas(response);
      })
      .catch((err) => {
        console.error(err);
      });
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
  medidaDiv.setAttribute("data-cod-medida", medida.cod_medida);

  medidaDiv.addEventListener("click", (element) => abrirMedida(element));
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

const abrirMedida = (element) => {
  console.log("Apertou o aprendizado!");

  let medida = element.currentTarget.getAttribute("data-cod-medida");

  const modalMedida = document.getElementById("visualizar-medida");

  if (medida) {
    localStorage.setItem("medida", medida);
    modalMedida.classList.toggle("is-active");

    const dados = {
      acao: "MedidasDisciplinares/selecionar",
      medida_disciplinar: medida,
    };

    sendRequest(dados)
      .then((response) => {
        console.log(response);
        preencherMedida(response);
      })
      .catch((err) => {
        console.error(err);
      });
  }
};

const preencherMedida = (medida) => {
  const modal = document.getElementById("visualizar-medida");
  modal.querySelector(".info-m .nome").innerHTML = medida.aluno.nome;
  modal.querySelector(".info-m .matricula").innerHTML = medida.aluno.matricula;
  const data = new Date(medida.data);

  const meses = [
    "Janeiro",
    "Fevereiro",
    "Março",
    "Abril",
    "Maio",
    "Junho",
    "Julho",
    "Agosto",
    "Setembro",
    "Outubro",
    "Novembro",
    "Dezembro",
  ];

  const dataFormatada = ` ${data.getDay()} ${
    meses[data.getMonth()]
  } ${data.getFullYear()}`;
  modal.querySelector(".info-m .data").innerHTML = dataFormatada;

  modal.querySelector(".info-medida .tipo-medida p").innerHTML =
    medida.descricao;
  modal.querySelector(".info-medida .observacao").innerHTML = medida.observacao;
};

const closeModal = (params) => {
  const modalMedida = document.getElementById("visualizar-medida");

  let closeBtn = modalMedida.querySelector(".modal-close-btn");
  closeBtn.addEventListener("click", (evnt) => {
    fecharMedida();
  });
  let bgModal = modalMedida.querySelector(".modal-background");
  bgModal.addEventListener("click", (evnt) => {
    fecharMedida();
  });
};

const fecharMedida = () => {
  const modal = document.getElementById("visualizar-medida");
  modal.classList.toggle("is-active");

  if (localStorage.getItem("medida")) {
    localStorage.removeItem("medida");
  }

  modal.querySelector(".info-m .nome").innerHTML = "";
  modal.querySelector(".info-m .matricula").innerHTML = "";

  modal.querySelector(".info-m .data").innerHTML = "";

  modal.querySelector(".info-medida .tipo-medida p").innerHTML = "";
  modal.querySelector(".info-medida .observacao").innerHTML = "";
};

obterEstatisticaTurma();
obterPrincipaisAvaliacoes();
obterMedidas();
obterCoef();
