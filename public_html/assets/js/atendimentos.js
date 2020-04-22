const cardEncaminhamentosListener = () => {
  const encaminhamentos = document.querySelectorAll(".card-encaminhamento");

  encaminhamentos.forEach((card) => {
    card.addEventListener(
      "click",
      (event) => {
        abrirEncaminhamento(event);
      },
      false
    );
  });
};

const abrirEncaminhamento = (element) => {
  let encaminhamento = element.currentTarget.getAttribute(
    "data-encaminhamento"
  );

  if (encaminhamento) {
    localStorage.setItem('encaminhamento', encaminhamento);
    window.location = './salvarAtendimento.html?encaminhamento=' + encaminhamento;
} else {
    showMessage(
      "Houve um erro!",
      "Não foi possível abrir o encaminhamento.",
      "warning",
      5000
    );
  }
};

cardEncaminhamentosListener();
