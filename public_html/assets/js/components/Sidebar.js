import { setCookie } from "../utils.js";

const abrirTurmasEvent = () => {
  const items = document.querySelectorAll("aside .turmas");

  items.forEach((item) =>
    item.addEventListener("click", (element) => {
      window.location.href = "./turmas.php";
    })
  );
};

const abrirAlunosEvent = () => {
  const items = document.querySelectorAll("aside .estudantes");

  items.forEach((item) =>
    item.addEventListener("click", (element) => {
      window.location.href = "./estudantes.php";
    })
  );
};

const abrirReunioesEvent = () => {
  const items = document.querySelectorAll("aside .reunioes");

  items.forEach((item) =>
    item.addEventListener("click", (element) => {
      window.location.href = "./reunioes.php";
    })
  );
};

const abrirConfiguracoes = () => {
  const items = document.querySelectorAll("aside .config");

  items.forEach((item) =>
    item.addEventListener("click", (element) => {
      window.location.href = "./permissoes.php";
    })
  );
};

const sair = () => {
  const items = document.querySelectorAll("aside .item-sair");

  items.forEach((item) =>
    item.addEventListener("click", (element) => {
      setCookie("token", "", 0);
      window.location.href = "../login.html";
      localStorage.clear();
    })
  );
};

export default () => {
  //   console.log("SIDE BAR");
  abrirTurmasEvent();
  abrirAlunosEvent();
  abrirReunioesEvent();
  abrirConfiguracoes();
  sair();
};
