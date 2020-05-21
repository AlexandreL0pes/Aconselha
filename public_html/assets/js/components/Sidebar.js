const abrirTurmasEvent = () => {
  console.log("Abriu as turmas");
  const items = document.querySelectorAll(".turmas");

  items.forEach();
};

const abrirAlunosEvent = () => {
  console.log("Abriu alunos");
};

const abrirReunioesEvent = () => {
  console.log("Abriu Reuniões");
};

const abrirConfiguracoes = () => {
  console.log("Abriu configurações");
};

export default () => {
  console.log("SIDE BAR");
  abrirTurmasEvent();
  abrirAlunosEvent();
  abrirReunioesEvent();
  abrirConfiguracoes();
};
