const abrirTurmasEvent = () => {
  const items = document.querySelectorAll(".turmas");

  items.forEach((item) =>
    item.addEventListener("click", (element) => {
      window.location.href = "./reunioes.html";
    })
  );
};

const abrirAlunosEvent = () => {
  const items = document.querySelectorAll(".estudantes");

  items.forEach((item) =>
    item.addEventListener("click", (element) => {
      window.location.href = "./estudantes.html";
    })
  );
};

const abrirReunioesEvent = () => {
  const items = document.querySelectorAll(".reunioes");

  items.forEach((item) =>
    item.addEventListener("click", (element) => {
      window.location.href = "./reunioes.html";
    })
  );
};

const abrirConfiguracoes = () => {
  const items = document.querySelectorAll(".config");

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
