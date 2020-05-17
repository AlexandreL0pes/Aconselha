import { showMessage, sendRequest } from "./utils.js";

const listener = () => {
  const atendimentos = document.getElementById("abrirAtendimento");
  atendimentos.addEventListener("click", (e) => abrirAtendimentos());

  const memoria = document.getElementById("abrirMemoria");
  memoria.addEventListener("click", (event) => abrirMemoria());

  const mostrarMaisAprendizado = document.querySelector(
    ".ensino .mostrar-tudo"
  );
  mostrarMaisAprendizado.addEventListener("click", listarAprendizados);
  closeModal();

  listarPreviaAprendizados();
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

/**
 * Tira a classe active do modal
 * @param {*} modal Modal DOM Element
 */
const fecharAvaliacao = (modal) => {
  modal.classList.toggle("is-active");
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
 * Dispara uma requisição assíncrona para a obtenção de aprendizados
 */
const solicitarAprendizados = async () => {
  const reuniao = localStorage.getItem("conselhoAtual") || "";

  if (reuniao === "") {
    showMessage(
      "Ops, deu errado!",
      "Não foi possível identificar a reunião atual!",
      "error",
      5000
    );
    return false;
  }

  let dados = {
    acao: "Aprendizados/listarAprendizadosReuniao",
    reuniao: reuniao,
  };

  return await sendRequest(dados)
    .then((response) => {
      return response;
    })
    .catch((err) => {
      showMessage(
        "Ops, deu errado!",
        "Não foi possível acessar os aprendizados.",
        "error",
        5000
      );
      console.error(err);
    });
};

const addAprendizadoCard = (aprendizado) => {
  let card = document.createElement("div");
  card.classList.add("avaliacao", "ensino");
  card.setAttribute("data-aprendizado", aprendizado.aprendizado);
  let qtdEstudantes = aprendizado.estudantes.length;
  let aluno = qtdEstudantes > 1 ? "alunos" : "aluno";
  card.innerHTML = `
    <p class="tipo-avaliacao gray-text" name="">
      Ensino-Aprendizado
    </p>
    <p class="titulo-avaliacao">${aprendizado.disciplina.nome}</p>
    <div class="chips">
      <div class="chip">
        <span class="chip-text">${qtdEstudantes} ${aluno}</span>
      </div>
    </div>
  `;

  card.addEventListener("click", (e) => console.log("Clicou Aprendizado!"));

  const aprendizados = document.getElementById("aprendizados");
  aprendizados.append(card);
};

/**
 * Dispara uma requisição dos aprendizados e imprime todos na tela
 */
const listarAprendizados = () => {
  solicitarAprendizados()
    .then((aprendizados) => {
      console.log("> Listando todos os aprendizados!");

      removerPrevia("ensino");
      aprendizados.map((aprendizado) => addAprendizadoCard(aprendizado));
      mostrarMais("ensino");
    })
    .catch((err) => {
      console.error(err);
    });
};

/**
 * Função que faz a requisição dos aprendizados e imprime a prévia
 */
const listarPreviaAprendizados = () => {
  solicitarAprendizados()
    .then((aprendizados) => {
      console.log("> Listando Prévia!");
      gerarPrevia(aprendizados);
      mostrarMenos("ensino");
    })
    .catch((err) => {
      console.error(err);
    });
};

/**
 * Lista os 3 primeiros aprendizados e  adiciona o card restante
 * @param {*} aprendizados Lista JSON com todos os aprendizados
 */
const gerarPrevia = (aprendizados) => {
  const qtdPrevia = 3;
  if (aprendizados.length == 0 || aprendizados === undefined) {
    throw new Error("Não existem aprendizados!");
  }
  if (aprendizados.length > qtdPrevia) {
    document.getElementById("aprendizados").innerHTML = "";
    for (let index = 0; index < qtdPrevia; index++) {
      addAprendizadoCard(aprendizados[index]);
    }
    const qtdRestante = aprendizados.length - qtdPrevia;
    let restante = document.createElement("div");
    restante.classList.add("mostrar-mais");
    restante.innerHTML = `
      <p class="quantidade">
        + <span class="numero-quantidade">${qtdRestante}</span>
      </p>`;

    restante.addEventListener("click", listarAprendizados);
    const divAprendizados = document.getElementById("aprendizados");
    divAprendizados.append(restante);
  }
};

/**
 * Remove o card de prévia
 * @param {*} avaliacao String com o id do elemento onde o card está
 */
const removerPrevia = (avaliacao) => {
  const element = document.querySelector("." + avaliacao);
  const previa = element.querySelector(".mostrar-mais");
  previa.remove();

  mostrarMenos(avaliacao);
};

/**
 * Muda o texto e os eventos do botão para mostrar menos
 * @param {*} avaliacao Classe do elemento que engloba a avaliaão
 */
const mostrarMenos = (avaliacao) => {
  console.log("> Mostrando menos!");
  const element = document.querySelector("." + avaliacao);
  const btnMostraMenos = element.querySelector(".mostrar-tudo");
  btnMostraMenos.innerHTML =
    "Mostrar Mais <i class='fas fa-angle-down' aria-hidden='true'></i>";
  btnMostraMenos.removeEventListener("click", listarPreviaAprendizados);
  btnMostraMenos.addEventListener("click", listarAprendizados);
};

/**
 * Muda o texto e os eventos do botão para mostrar mais
 * @param {*} avaliacao Classe do elemento que engloba a avaliaão
 */
const mostrarMais = (avaliacao) => {
  console.log("> Mostrando mais!");
  const element = document.querySelector("." + avaliacao);
  const btnMostrarMais = element.querySelector(".mostrar-tudo");
  btnMostrarMais.innerHTML =
    "Mostrar menos <i class='fas fa-angle-up' aria-hidden='true'></i>";
  btnMostrarMais.removeEventListener("click", listarAprendizados);
  btnMostrarMais.addEventListener("click", listarPreviaAprendizados);
};

listener();
