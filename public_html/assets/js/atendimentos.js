/**
 * Listener para botão novo encaminhamento
 */
const addEncaminhamento = document.getElementById("add-encaminhamento");
addEncaminhamento.addEventListener("click", (e) => {
  abrirNovoEncaminhamento();
});

/**
 * Listener para cards de encaminhamentos
 */
const cardsEncaminhamentos = document.querySelectorAll(".card-encaminhamento");
cardsEncaminhamentos.forEach((card) => {
  card.addEventListener("click", (event) => abrirEncaminhamento(event));
});




/**
 * Abre o modal para a vizualização
 * @param {DOM Element} element
 */
const abrirEncaminhamento = (element) => {
  let encaminhamento = element.currentTarget.getAttribute(
    "data-encaminhamento"
  );

  const modalEncaminhamento = document.getElementById("encaminhamento");

  if (encaminhamento) {
    localStorage.setItem("encaminhamento", encaminhamento);
    modalEncaminhamento.classList.toggle("is-active");

    // TODO: Adicionar aqui a função para preencher o modal com os dados do encaminhamento
  } else {
    showMessage(
      "Houve um erro!",
      "Não foi possível abrir o encaminhamento.",
      "warning",
      5000
    );
  }
};

/**
 * Esconde o modal, remove o aluno do modal atual e apaga os perfis selecionados
 * @param {DOM Element} modal Modal de Avaliação Diagnóstica
 */
const fecharAvaliacao = (modal) => {
  modal.classList.toggle("is-active");
  if (localStorage.getItem("encaminhamento")) {
    localStorage.removeItem("encaminhamento");
  }
};

/**
 * Adiciona a classe is-active para o modal selecionado
 */
const abrirNovoEncaminhamento = (e) => {
  const modalEncaminhamento = document.querySelector("#encaminhamento");
  modalEncaminhamento.classList.toggle("is-active");
};



/**
 * Listener para o fechamento do modal
 * @param {*} params
 */
const closeModal = (params) => {
  const modals = document.querySelectorAll(".modal");
  modals.forEach((modal) => {
    const closeBtn = modal.querySelector(".modal-close-btn");
    closeBtn.addEventListener("click", (event) => {
      modal.classList.toggle("is-active");
      if (localStorage.getItem("encaminhamento")) {
        localStorage.removeItem("encaminhamento");
      }
    });
    const bgModal = modal.querySelector(".modal-background");
    bgModal.addEventListener("click", (event) => {
      modal.classList.toggle("is-active");
      if (localStorage.getItem("encaminhamento")) {
        localStorage.removeItem("encaminhamento");
      }
    });
  });
};

/**
 * Listener para fechar o modal
 */
closeModal();