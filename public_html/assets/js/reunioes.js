import { sendRequest, showMessage, getCookie } from "./utils.js";

const listener = () => {
  const btnIniciarConselho = document.querySelector("#iniciarConselho");
  btnIniciarConselho.addEventListener("click", iniciarConselhos);

  const cardsTurmas = document.querySelectorAll(".turmas>div.cardbox");
  cardsTurmas.forEach(function (card) {
    card.addEventListener("click", (e) => selecionarTurmas(e));
  });
};

/**
 * Listener
 * Redireciona o usuário para a página da reunião, correpondente ao card clicado
 */
let abrirReuniao = () => {
  let idReuniao =
    event.target.getAttribute("data-turmaconselho") ||
    event.target.parentElement.getAttribute("data-turmaconselho");

  let idTurma =
    event.target.getAttribute("data-turma") ||
    event.target.parentElement.getAttribute("data-turma");

  localStorage.setItem("conselhoAtual", idReuniao);
  localStorage.setItem("turmaAtual", idTurma);

  window.location.href = "./dashboard.html";
};

/**
 * Listener
 * Seleciona as turmas para iniciar o conselho
 */
let selecionarTurmas = () => {
  let item = null;

  if (event.currentTarget.classList.contains("cardbox")) {
    item = event.currentTarget;
  }
  if (event.currentTarget.parentNode.classList.contains("cardbox")) {
    item = event.currentTarget.parentNode;
  }
  item.classList.toggle("selected");
  habilitarBotao();
};

/**
 * Function
 * Habilitar botão quando existem turmas selecionadas
 */
let habilitarBotao = () => {
  const botao = document.querySelector("#iniciarConselho");
  const turmasSelecionadas = document.querySelectorAll(
    ".turmas>div.cardbox.selected"
  );
  const qtdTurmas = document.querySelector("#qtdTurmas");

  if (turmasSelecionadas.length > 0) {
    botao.disabled = false;
    qtdTurmas.innerHTML = turmasSelecionadas.length;
  } else {
    botao.disabled = true;
    qtdTurmas.innerHTML = "Nenhuma";
  }
};

/**
 * Obtem e envia os dados para cadastrar a reunião
 */
const iniciarConselhos = () => {
  const turmasSelecionadas = document.querySelectorAll(
    ".turmas>div.cardbox.selected"
  );

  let codigoTurmas = [];
  for (let i of turmasSelecionadas) {
    codigoTurmas.push(i.getAttribute("data-turma"));
  }

  if (codigoTurmas.length > 0) {
    const dados = {
      acao: "Reunioes/cadastrar",
      turmas: codigoTurmas,
    };
    sendRequest(dados)
      .then((response) => {
        console.log(response);
        showMessage(
          "Deu certo!",
          "Os conselhos foram iniciados!",
          "success",
          5000
        );
      })
      .catch((err) => {
        console.error(err);
      });
  }
  showMessage(
    "Houve um erro!",
    "Agora",
    "Não foi possível alterar o trabalho.",
    "success",
    4000
  );
};

const solicitarReunioes = () => {
  const curso = localStorage.getItem("curso");

  const dados = { acao: "Reunioes/listarReunioesAndamento" };

  if (curso != null) {
    dados.curso = curso;
  }

  sendRequest(dados)
    .then((response) => {
      if (response.length > 0) {
        response.forEach((reuniao) => addReuniaoCard(reuniao));
      } else {
        const reunioesDiv = document.getElementById("reunioes");
        const msg = document.createElement("div");
        msg.classList.add("nenhum-resultado");
        msg.innerHTML = "Nenhum conselho de classe foi iniciado!";

        reunioesDiv.appendChild(msg);
      }
    })
    .catch((err) => {
      console.error(err);
    });
};

const addReuniaoCard = (reuniao) => {
  let card = document.createElement("div");

  let classCurso = "";
  if (reuniao.curso === "Informática para Internet") {
    classCurso = "is-info";
  } else if (reuniao.curso === "Meio Ambiente") {
    classCurso = "is-amb";
  } else {
    classCurso = "is-agro";
  }

  card.classList.add("cardbox", "card-turma", classCurso);
  card.setAttribute("data-turma", reuniao.codigo);
  card.setAttribute("data-turmaconselho", reuniao.reuniao);

  card.innerHTML += `
    <p class="subtitulo is-8 gray-text">${reuniao.curso}</p>
    <p class="subtitulo is-7">${reuniao.nome}</p>
    <p class="subtitulo is-9 gray-text">${reuniao.codigo}</p>
  `;

  card.addEventListener("click", (event) => abrirReuniao(event));

  const reunioesDiv = document.getElementById("reunioes");

  reunioesDiv.appendChild(card);
};

const obterCurso = () => {
  const token = getCookie("token");

  const dados = { acao: "Coordenadores/obterCurso", token: token };

  sendRequest(dados).then((response) => {
    // console.log(response);
    if (response.curso) {
      localStorage.setItem("curso", response.curso);
    }
  });
};

const solicitarTurmas = () => {
  const curso = localStorage.getItem("curso");

  const dados = { acao: "Reunioes/reunioesNaoIniciadas" };

  if (curso != null) {
    dados.curso = curso;
  }

  sendRequest(dados)
    .then((response) => {
      if (response.length > 0) {
        response.map((turma) => addTurmaCard(turma));
      }else {
        const reunioesDiv = document.getElementById("turmas");
        const msg = document.createElement("div");
        msg.classList.add("nenhum-resultado");
        msg.innerHTML = "Nenhum outro conselho pode ser iniciado!";

        reunioesDiv.appendChild(msg);
      }
    })
    .catch((err) => {
      console.error(err);
    });
};

const addTurmaCard = (turma) => {
  let card = document.createElement("div");

  let classCurso = "";
  if (turma.curso === "Informática para Internet") {
    classCurso = "is-info";
  } else if (turma.curso === "Meio Ambiente") {
    classCurso = "is-amb";
  } else {
    classCurso = "is-agro";
  }

  card.classList.add("cardbox", "card-turma", classCurso);
  card.setAttribute("data-turma", turma.codigo);
  card.addEventListener("click", selecionarTurmas);

  card.innerHTML += `
    <p class="subtitulo is-8 gray-text">${turma.curso}</p>
    <p class="subtitulo is-7">${turma.nome}</p>
    <p class="subtitulo is-9 gray-text">${turma.codigo}</p>
  `;

  const turmasDiv = document.getElementById("turmas");

  turmasDiv.appendChild(card);
};

obterCurso();
solicitarReunioes();
solicitarTurmas();
listener();
// abrirReuniao();
iniciarConselhos();
