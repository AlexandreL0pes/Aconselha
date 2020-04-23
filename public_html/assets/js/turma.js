const abrirMemoria = () => {
  const memoriaLink = document.querySelector(
    ".action-card.memoria, .action-card.memoria >* "
  );
  memoriaLink.addEventListener("click", function (params) {
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
  const atendimentosCard = document.querySelector(
    ".action-card.atendimentos, .action-card.atendimentos >* "
  );

  atendimentosCard.addEventListener("click", (event) => {
    if (localStorage.getItem("conselhoAtual")) {
      window.location = "./atendimentos.html";
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

abrirMemoria();
abrirAtendimentos();
