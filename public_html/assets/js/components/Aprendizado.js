import { showMessage, sendRequest } from "../utils.js";

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

  card.addEventListener("click", (e) => abrirAprendizado(e));

  const aprendizados = document.getElementById("aprendizados");
  aprendizados.append(card);
};

/**
 * Dispara uma requisição dos aprendizados e imprime todos na tela
 */
export const listarAprendizados = () => {
  solicitarAprendizados()
    .then((aprendizados) => {
      console.log("> Listando todos os aprendizados!");

      removerPrevia();
      aprendizados.map((aprendizado) => addAprendizadoCard(aprendizado));
      mostrarMais();
    })
    .catch((err) => {
      console.error(err);
    });
};

/**
 * Função que faz a requisição dos aprendizados e imprime a prévia
 */
export const listarPreviaAprendizados = () => {
  solicitarAprendizados()
    .then((aprendizados) => {
      console.log("> Listando Prévia!");
      gerarPreviaAprendizados(aprendizados);
      mostrarMenos();
      atualizarResultados(aprendizados.length);
    })
    .catch((err) => {
      console.error(err);
    });
};

/**
 * Lista os 3 primeiros aprendizados e  adiciona o card restante
 * @param {*} aprendizados Lista JSON com todos os aprendizados
 */
const gerarPreviaAprendizados = (aprendizados) => {
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

const removerPrevia = () => {
  const previa = document.querySelector(".ensino .mostrar-mais");
  previa.remove();
  document.querySelector(".ensino .avaliacoes").innerHTML = "";
  mostrarMenos();
};

const mostrarMenos = () => {
  const btnMostraMenos = document.querySelector(".ensino .mostrar-tudo");
  btnMostraMenos.innerHTML =
    "Mostrar mais <i class='fas fa-angle-down' aria-hidden='true'></i>";

  btnMostraMenos.removeEventListener("click", listarPreviaAprendizados);
  btnMostraMenos.addEventListener("click", listarAprendizados);
};
const mostrarMais = () => {
  const btnMostrarMais = document.querySelector(".ensino .mostrar-tudo");
  btnMostrarMais.innerHTML =
    "Mostrar menos <i class='fas fa-angle-up' aria-hidden='true'></i>";

  btnMostrarMais.removeEventListener("click", listarAprendizados);
  btnMostrarMais.addEventListener("click", listarPreviaAprendizados);
};

const atualizarResultados = (quantidade) => {
  const resultados = document.querySelector(".reuniao-info");

  const contador = resultados.querySelector(`.ensino .quantidade`);

  contador.innerHTML = quantidade;
};

export default () => {
  listarPreviaAprendizados();
};
