import { sendRequest, showMessage } from "./utils.js";

const listener = () => {
  const btnIniciarConselho = document.querySelector("#iniciarConselho");
  btnIniciarConselho.addEventListener("click", iniciarConselhos);
};

/**
 * Listener
 * Redireciona o usuário para a página da reunião, correpondente ao card clicado
 */
let abrirReuniao = () => {
  const cardsReuniao = document.querySelectorAll(
    ".reunioes>div.cardbox, .reunioes>div.cardbox *"
  );
  cardsReuniao.forEach(function (card) {
    card.addEventListener("click", function (event) {
      let idReuniao =
        event.target.getAttribute("data-turmaconselho") ||
        event.target.parentElement.getAttribute("data-turmaconselho");

      let idTurma =
        event.target.getAttribute("data-turma") ||
        event.target.parentElement.getAttribute("data-turma");

      localStorage.setItem("conselhoAtual", idReuniao);
      localStorage.setItem("turmaAtual", idTurma);

      window.location = "./dashboard.html";
    });
  });
};

/**
 * Listener
 * Seleciona as turmas para iniciar o conselho
 */
let selecionarTurmas = () => {
  const cardsTurmas = document.querySelectorAll(".turmas>div.cardbox");
  cardsTurmas.forEach(function (card) {
    card.addEventListener("click", function (event) {
      let item = null;

      if (event.currentTarget.classList.contains("cardbox")) {
        item = event.currentTarget;
      }
      if (event.currentTarget.parentNode.classList.contains("cardbox")) {
        item = event.currentTarget.parentNode;
      }
      item.classList.toggle("selected");
      habilitarBotao();
    });
  });
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
    codigoTurmas.push(i.getAttribute("data-cod-turma"));
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

listener();
abrirReuniao();
selecionarTurmas();
iniciarConselhos();
