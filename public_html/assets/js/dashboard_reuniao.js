import { showMessage, sendRequest } from "./utils.js";

import Aprendizado, {
  listarPreviaAprendizados,
  listarAprendizados,
} from "./components/Aprendizado.js";

import Experiencia, {
  listarExperiencias,
  listarPreviaExperiencias,
} from "./components/Experiencia.js";

import Diagnostica, {listarPreviaDiagnosticas, listarDiagnosticas} from './components/Diagnostica.js';

const listener = () => {
  const atendimentos = document.getElementById("abrirAtendimento");
  atendimentos.addEventListener("click", (e) => abrirAtendimentos());

  const memoria = document.getElementById("abrirMemoria");
  memoria.addEventListener("click", (event) => abrirMemoria());

  Aprendizado();
  Experiencia();
  Diagnostica();
  
  closeModal();

  // listarPreviaDiagnosticas();
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
 * Abre uma nova aba com a página de memória
 */
const abrirMemoria = () => {
  if (localStorage.getItem("conselhoAtual")) {
    window.open("./memoria.html", "_blank");
  } else {
    showMessage(
      "Houve um erro!",
      "Selecione um conselho antes de prosseguir.",
      "error",
      5000
    );
  }
};

/**
 * Redireciona para a página de atendimentos pedagógicos
 */
const abrirAtendimentos = () => {
  if (localStorage.getItem("conselhoAtual")) {
    window.location = "./atendimentos.html";
  } else {
    showMessage(
      "Houve um erro!",
      "Selecione um conselho antes de prosseguir",
      "error",
      5000
    );
  }
};


/**
 * Gera um chip com o texto informado
 * @param {*} nome Texto adicionado ao chip
 */
const gerarChips = (nome, tipo = null) => {
  const chip = document.createElement("div");
  chip.classList.add("chip");
  if (tipo != null) {
    tipo = tipo === "1" ? "positivo" : "negativo";
    chip.classList.add(tipo);
  }

  const span = document.createElement("span");
  span.classList.add("chip-text");
  span.innerHTML = nome;
  chip.appendChild(span);
  return chip;
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
};

/**
 * Fecha o modal de Aprendizado
 */
const fecharEnsino = () => {
  const modalEnsino = document.getElementById("visualizar-ensino");
  modalEnsino.classList.toggle("is-active");
};

const atualizarResultados = (avaliacao, quantidade) => {
  const resultados = document.querySelector(".reuniao-info");

  const contador = resultados.querySelector(`.${avaliacao} .quantidade`);

  contador.innerHTML = quantidade;
};

listener();
