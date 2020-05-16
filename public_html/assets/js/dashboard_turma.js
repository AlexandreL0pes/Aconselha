import { showMessage } from "./utils.js";

const listener = () => {
  closeModal();
  abrirAtendimentos();
  abrirMemoria();
};
/**
 * Listener para o fechamento do modal
 * @param {*} params
 */
const closeModal = (params) => {
  const modals = document.querySelectorAll(".modal");
  modals.forEach((modal) => {
    const closeBtn = modal.querySelector(".modal-close-btn");
    closeBtn.addEventListener("click", (evnt) => {
      fecharAvaliacao(modal);
    });
    const bgModal = modal.querySelector(".modal-background");
    bgModal.addEventListener("click", (evnt) => {
      fecharAvaliacao(modal);
    });
  });
};

const fecharAvaliacao = (modal) => {
  modal.classList.toggle("is-active");
};

const abrirMemoria = () => {
  const memoria = document.getElementById("abrirMemoria");
  memoria.addEventListener("click", (event) => {
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
  });
};

const abrirAtendimentos = () => {
  const atendimentos = document.getElementById("abrirAtendimento");

  atendimentos.addEventListener("click", (event) => {
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
  });
};
listener();
