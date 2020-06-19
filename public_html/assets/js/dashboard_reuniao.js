import { showMessage, sendRequest } from "./utils.js";

import Aprendizado, {
  listarPreviaAprendizados,
  listarAprendizados,
} from "./components/Aprendizado.js";

import Experiencia, {
  listarExperiencias,
  listarPreviaExperiencias,
} from "./components/Experiencia.js";

import Diagnostica, {
  listarPreviaDiagnosticas,
  listarDiagnosticas,
} from "./components/Diagnostica.js";
import { autenticarCoordenador } from "./components/Autenicacao.js";

// autenticarCoordenador();

const listener = () => {
  Aprendizado();
  Experiencia();
  Diagnostica();

  closeModal();

  const btnFinalizarReuniao = document.getElementById("finalizarReuniao");
  btnFinalizarReuniao.addEventListener("click", finalizarReuniao);
};

/**
 * Listener para o fechamento do modal
 * @param {*} params
 */
const closeModal = (params) => {
  const modalEnsino = document.getElementById("visualizar-ensino");
  const modalExperiencia = document.getElementById("visualizar-experiencia");
  const modalDiagnostica = document.getElementById("visualizar-diagnostica");

  let closeBtn = modalEnsino.querySelector(".modal-close-btn");
  closeBtn.addEventListener("click", fecharEnsino);
  let bgModal = modalEnsino.querySelector(".modal-background");
  bgModal.addEventListener("click", fecharEnsino);

  closeBtn = modalExperiencia.querySelector(".modal-close-btn");
  closeBtn.addEventListener("click", fecharExperiencia);
  bgModal = modalExperiencia.querySelector(".modal-background");
  bgModal.addEventListener("click", fecharExperiencia);

  closeBtn = modalDiagnostica.querySelector(".modal-close-btn");
  closeBtn.addEventListener("click", fecharDiagnostica);
  bgModal = modalDiagnostica.querySelector(".modal-background");
  bgModal.addEventListener("click", fecharDiagnostica);
};

/**
 * Fecha o modal de diagnóstica
 */
const fecharDiagnostica = () => {
  const modalDiagnostica = document.getElementById("visualizar-diagnostica");
  modalDiagnostica.classList.toggle("is-active");
  const modalCard = modalDiagnostica.querySelector(".modal-card");
  modalDiagnostica.classList.remove("positiva", "negativa");

  const professoresChips = (modalDiagnostica.querySelector(
    ".professores .chips"
  ).innerHTML = "");
  const perfisChips = (modalDiagnostica.querySelector(
    ".perfis .chips"
  ).innerHTML = "");
};

/**
 * Fecha o modal de Experiência
 */
const fecharExperiencia = () => {
  const modalExperiencia = document.getElementById("visualizar-experiencia");
  modalExperiencia.classList.toggle("is-active");

  const disciplinasChips = (modalExperiencia.querySelector(
    ".disciplinas .chips"
  ).innerHTML = "");
};

/**
 * Fecha o modal de Aprendizado
 */
const fecharEnsino = () => {
  const modalEnsino = document.getElementById("visualizar-ensino");
  modalEnsino.classList.toggle("is-active");
};

const obterInformacoesTurma = () => {
  const turma = localStorage.getItem("turmaAtual") || null;

  if (turma !== null) {
    const dados = { acao: "Turmas/informacoesTurma", turma: turma };

    sendRequest(dados)
      .then((response) => {
        console.log(response);
        apresentarInformacoesTurma(response);
      })
      .catch((err) => {
        console.error(err);
        showMessage(
          "Houve um erro!",
          "Não foi possível acessar as informações da turma.",
          "error",
          4000
        );
      });
  }
};

const apresentarInformacoesTurma = (dados) => {
  const cardInfoTurma = document.querySelector(".turma-info");

  cardInfoTurma.querySelector("#nome").innerHTML = dados.nome;
  cardInfoTurma.querySelector("#curso").innerHTML = dados.curso;
  cardInfoTurma.querySelector("#codigo").innerHTML = dados.codigo;
};

const finalizarReuniao = () => {
  const reuniao = localStorage.getItem("conselhoAtual");

  if (reuniao) {
    const dados = {
      acao: "Reunioes/finalizarReuniao",
      reuniao: reuniao,
    };

    sendRequest(dados)
      .then((response) => {
        console.log(response);
        showMessage("Deu certo!", "A reunião foi encerrada.", "success");
        window.location.href = "./reunioes.php";
      })
      .catch((err) => {
        console.error(err);
        showMessage(
          "Houve um erro!",
          "Não foi possível encerrar a reunião.",
          "warning"
        );
      });
  }
};

obterInformacoesTurma();
listener();
