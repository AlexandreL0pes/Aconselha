import { showMessage, sendRequest } from "../utils.js";

/**
 * Dispara um requisição assíncrona para a obtenção de experiências
 */
const solicitarExperiencias = async () => {
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
    acao: "Experiencias/listarExperienciasReuniao",
    reuniao: reuniao,
  };

  return await sendRequest(dados)
    .then((response) => {
      return response;
    })
    .catch((err) => {
      showMessage(
        "Ops, deu Errado",
        "Não foi possível acessar os aprendizados.",
        "error",
        5000
      );
      console.error(err);
    });
};

/**
 * Solicita e apresenta as experiências na página
 */
export const listarExperiencias = () => {
  solicitarExperiencias()
    .then((experiencias) => {
      console.log("> Listando todas as experiências!");
      removerPrevia();
      experiencias.map((experiencia) => addExperienciaCard(experiencia));
      mostrarMais();
    })
    .catch((err) => {
      console.error(err);
    });
};

/**
 * Solicita e apresenta a prévia de experiência na página
 */
export const listarPreviaExperiencias = () => {
  solicitarExperiencias()
    .then((experiencias) => {
      console.log("> Listando Prévia!");
      gerarPreviaExperiencia(experiencias);
      mostrarMenos();
      atualizarResultados(experiencias.length);
    })
    .catch((err) => {
      console.error(err);
    });
};

/**
 * Lista os 3 primeiro aprendizados e adiciona o card restante
 * @param {*} experiencias JSON Object com todos os aprendizados
 */
const gerarPreviaExperiencia = (experiencias) => {
  const QTD_PREVIA = 3;

  if (experiencias.length == 0 || experiencias === undefined) {
    throw new Error("Não existem experiências!");
  }

  if (experiencias.length > QTD_PREVIA) {
    document.getElementById("experiencias").innerHTML = "";

    for (let i = 0; i < QTD_PREVIA; i++) {
      addExperienciaCard(experiencias[i]);
    }

    const qtdRestante = experiencias.length - QTD_PREVIA;
    let restante = document.createElement("div");
    restante.classList.add("mostrar-mais");
    restante.innerHTML = `
      <p class="quantidade">
        + <span class="numero-quantidade">${qtdRestante}</span>
      </p>
      `;

    restante.addEventListener("click", listarExperiencias);
    const divExperiencias = document.getElementById("experiencias");
    divExperiencias.append(restante);
  } else {
    document.getElementById("experiencias").innerHTML = "";
    experiencias.map((experiencia) => addExperienciaCard(experiencia));
  }
};

/**
 * Gera um card de uma experiência
 * @param {*} experiencia JSON Object contendo as informações de uma experiencia
 */
const addExperienciaCard = (experiencia) => {
  let card = document.createElement("div");
  card.classList.add("avaliacao", "experiencia");
  card.setAttribute("data-experiencia", experiencia.experiencia);

  let label = document.createElement("p");
  label.classList.add("titulo-avaliacao", "gray-text");
  label.innerHTML = "Experiência";
  card.appendChild(label);

  let titulo = document.createElement("p");
  titulo.classList.add("titulo-avaliacao", "gray-text");
  titulo.innerHTML = experiencia.titulo;
  card.appendChild(titulo);

  card.appendChild(gerarDisciplinasChip(experiencia.disciplinas));

  card.addEventListener("click", (e) => abrirExperiencia(e));

  const experiencias = document.getElementById("experiencias");
  experiencias.append(card);
};

/**
 * Gera chips contendo as disciplinas da experiência
 * @param {*} disciplinas JSON Object contendo as disciplinas de uma experiência'
 */
const gerarDisciplinasChip = (disciplinas) => {
  const QTD_PREVIA = 2;

  const chips = document.createElement("div");
  chips.classList.add("chips");

  if (disciplinas.length == 0) {
    const chip = gerarChips("Nenhuma disciplina");
    chips.appendChild(chip);
  } else if (disciplinas.length <= QTD_PREVIA) {
    disciplinas.map((disciplina) => {
      const chip = gerarChips(disciplina.nome);
      chips.appendChild(chip);
    });
  } else if (disciplinas.length > QTD_PREVIA) {
    for (let index = 0; index < QTD_PREVIA; index++) {
      const chip = gerarChips(disciplinas[index].nome);
      chips.appendChild(chip);
    }
    const chip = gerarChips(`+${disciplinas.length - QTD_PREVIA}`);
    chips.appendChild(chip);
  }

  return chips;
};

/**
 * Gera um chip com o texto informado
 * @param {*} nome Texto adicionado ao chip
 */
const gerarChips = (nome, tipo = null) => {
  const chip = document.createElement("div");
  chip.classList.add("chip");
  if (tipo != null) {
    tipo = tipo === "1" ? "positivo" : "negativo";
    chip.classList.add(tipo);
  }

  const span = document.createElement("span");
  span.classList.add("chip-text");
  span.innerHTML = nome;
  chip.appendChild(span);
  return chip;
};

const removerPrevia = () => {
  const previa = document.querySelector(".experiencia .mostrar-mais");
  previa.remove();
  document.querySelector(".experiencia .avaliacoes").innerHTML = "";
  mostrarMenos();
};

const mostrarMenos = () => {
  const btnMostrarMenos = document.querySelector(".experiencia .mostrar-tudo");
  btnMostrarMenos.innerHTML =
    "Mostrar mais <i class='fas fa-angle-down' aria-hidden='true'></i>";

  btnMostrarMenos.removeEventListener("click", listarPreviaExperiencias);
  btnMostrarMenos.addEventListener("click", listarExperiencias);
};

const mostrarMais = () => {
  const btnMostrarMais = document.querySelector(".experiencia .mostrar-tudo");
  btnMostrarMais.innerHTML =
    "Mostrar menos <i class='fas fa-angle-up' aria-hidden='true'></i>";

  btnMostrarMais.removeEventListener("click", listarExperiencias);
  btnMostrarMais.addEventListener("click", listarPreviaExperiencias);
};

const atualizarResultados = (quantidade) => {
  const resultados = document.querySelector(".reuniao-info");

  const contador = resultados.querySelector(".experiencia .quantidade");

  contador.innerHTML = quantidade;
};

/**
 * Seleciona a avaliação clicada e abre seu respectivo modal
 * @param {*} element Elemento clicado
 */
const abrirExperiencia = (element) => {
  console.log("> Abrindo a Experiencia!");
  let experiencia = element.currentTarget.getAttribute("data-experiencia");

  console.log(experiencia);

  if (experiencia) {
    localStorage.setItem("experiencia", experiencia);

    const dados = {
      acao: "Experiencias/selecionar",
      experiencia: experiencia,
    };

    sendRequest(dados)
      .then((response) => {
        console.log(response);
        preencherExperiencia(response);
      })
      .catch((err) => {
        console.error(err);
      });
  } else {
    showMessage(
      "Houve um erro!",
      "Não foi possível abrir a experiência!",
      "error",
      5000
    );
  }
};

/**
 * Preenche o modal de visualização com base da experiencia retornada
 * @param {*} diagnostica JSON Object com diagnóstica selecionada
 */
const preencherExperiencia = (experiencia) => {
  const modalExperiencia = document.getElementById("visualizar-experiencia");

  const titulo = modalExperiencia.querySelector(".modal-card-title");
  const categoria = modalExperiencia.querySelector(
    ".modal-experiencia .info .chip-categoria"
  );
  const observacao = modalExperiencia.querySelector(
    ".modal-experiencia .info .observacao"
  );

  const disciplinasChip = modalExperiencia.querySelector(".disciplinas .chips");
  modalExperiencia.classList.toggle("is-active");

  console.log(experiencia);
  titulo.innerHTML = experiencia.titulo;

  let classeClassificacao = "";
  if (experiencia.classificacao.nome === "Pontos Positivos") {
    classeClassificacao = "positivo";
  } else if (experiencia.classificacao.nome === "Pontos Negativos") {
    classeClassificacao = "negativo";
  } else {
    classeClassificacao = "nenhum";
  }

  categoria.classList.add(classeClassificacao);
  categoria.innerHTML = experiencia.classificacao.nome;
  
  observacao.innerHTML = experiencia.descricao;

  experiencia.disciplinas.map((disciplina) => {
    disciplinasChip.appendChild(gerarChips(disciplina.nome));
  });
};
export default () => {
  listarPreviaExperiencias();
};
