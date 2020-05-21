const abrirTurmasEvent = () => {
  const items = document.querySelectorAll("aside .turmas");

  items.forEach((item) =>
    item.addEventListener("click", (element) => {
      window.location.href = "./turmas.html";
    })
  );
};

const abrirAlunosEvent = () => {
  const items = document.querySelectorAll("aside .estudantes");

  items.forEach((item) =>
    item.addEventListener("click", (element) => {
      window.location.href = "./estudantes.html";
    })
  );
};

const abrirReunioesEvent = () => {
  const items = document.querySelectorAll("aside .reunioes");

  items.forEach((item) =>
    item.addEventListener("click", (element) => {
      window.location.href = "./reunioes.html";
    })
  );
};

const abrirConfiguracoes = () => {
  const items = document.querySelectorAll("aside .config");

  items.forEach((item) =>
    item.addEventListener("click", (element) => {
      window.location.href = "./config.html";
    })
  );
};

export default () => {
  //   console.log("SIDE BAR");
  abrirTurmasEvent();
  abrirAlunosEvent();
  abrirReunioesEvent();
  abrirConfiguracoes();
};
