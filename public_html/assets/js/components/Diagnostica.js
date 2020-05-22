import { showMessage, sendRequest } from "../utils.js";

/**
 * Solicita e apresenta todas as avaliações diagnósticas
 */
const solicitarDiagnosticas = async () => {
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
    acao: "Diagnosticas/listarDiagnosticasRelevantes",
    reuniao: reuniao,
  };

  return await sendRequest(dados)
    .then((response) => {
      return response;
    })
    .catch((err) => {
      showMessage(
        "Ops, deu errado",
        "Não foi possível acessar as avaliações diagnósticas!",
        "error",
        5000
      );
      console.error(err);
    });
};

/**
 * Cria e adiciona o card de diagnóstica na tela
 * @param {*} diagnostica JSON Object com uma avaliação diagnóstica
 */
const addDiagnosticaCard = (diagnostica) => {
  let card = document.createElement("div");
  // Verificação do tipo do card
  const classeTipo = diagnostica.tipo === "true" ? "positiva" : "negativa";

  card.classList.add("avaliacao", "diagnostica", classeTipo);
  card.setAttribute("data-diagnostica", diagnostica.diagnostica);

  let label = document.createElement("p");
  label.classList.add("titulo-avaliacao", "gray-text");
  label.innerHTML = "Diagnóstica";
  card.appendChild(label);

  let titulo = document.createElement("p");
  titulo.classList.add("titulo-avaliacao");
  titulo.innerHTML = diagnostica.aluno.nome;
  card.appendChild(titulo);
  card.appendChild(gerarProfessoresChip(diagnostica.professores));

  card.addEventListener("click", abrirDiagnostica);

  const diagnosticas = document.getElementById("diagnosticas");
  diagnosticas.append(card);
};

/**
 * Gerar os chips com os professores informados
 * @param {*} professores JSON Object com professores
 */
const gerarProfessoresChip = (professores) => {
  const QTD_PREVIA = 1;

  const chips = document.createElement("div");
  chips.classList.add("chips");

  if (professores.length <= QTD_PREVIA) {
    professores.map((professor) => {
      const chip = gerarChips(professor.nome);
      chips.appendChild(chip);
    });
  } else if (professores.length > QTD_PREVIA) {
    for (let index = 0; index < QTD_PREVIA; index++) {
      const chip = gerarChips(professores[index].nome);
      chips.appendChild(chip);
    }

    const chip = gerarChips(`+${professores.length - QTD_PREVIA}`);
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

/**
 *  Adiciona na tela a prévia das avaliações diagnósticas
 * @param {*} diagnosticas JSON Object
 */
const gerarPreviaDiagnostica = (diagnosticas) => {
  const QTD_PREVIA = 3;

  if (diagnosticas.length == 0 || experiencias === undefined) {
    throw new Error("Não existem diagnósticas!");
  }

  if (diagnosticas.length > QTD_PREVIA) {
    document.getElementById("diagnosticas").innerHTML = "";

    for (let index = 0; index < QTD_PREVIA; index++) {
      addDiagnosticaCard(diagnosticas[index]);
    }

    const qtdRestante = diagnosticas.length - QTD_PREVIA;
    let restante = document.createElement("div");
    restante.classList.add("mostrar-mais");
    restante.innerHTML = `
        <p class="quantidade">
          + <span class="numero-quantidade">${qtdRestante}</span>
        </p>
        `;

    restante.addEventListener("click", listarDiagnosticas);
    const divDiagnosticas = document.getElementById("diagnosticas");
    divDiagnosticas.append(restante);
  }
};

/**
 * Remove o card de prévia
 * @param {*} avaliacao String com o id do elemento onde o card está
 */
const removerPrevia = (avaliacao) => {
  const previa = document.querySelector(".diagnostica .mostrar-mais");
  previa.remove();
  document.querySelector(".diagnostica .avaliacoes").innerHTML = "";
  mostrarMenos();
};

/**
 * Muda o texto e os eventos do botão para mostrar menos
 * @param {*} avaliacao Classe do elemento que engloba a avaliaão
 */
const mostrarMenos = () => {
  const btnMostrarMenos = document.querySelector(".diagnostica .mostrar-tudo");
  btnMostrarMenos.innerHTML =
    "Mostrar mais <i class='fas fa-angle-down' aria-hidden='true'></i>";

  btnMostrarMenos.removeEventListener("click", listarPreviaDiagnosticas);
  btnMostrarMenos.addEventListener("click", listarDiagnosticas);
};

/**
 * Muda o texto e os eventos do botão para mostrar mais
 * @param {*} avaliacao Classe do elemento que engloba a avaliaão
 */
const mostrarMais = (avaliacao) => {
  console.log("> Mostrando mais!");
  const btnMostrarMais = document.querySelector(".diagnostica .mostrar-tudo");
  btnMostrarMais.innerHTML =
    "Mostrar menos <i class='fas fa-angle-up' aria-hidden='true'></i>";

  btnMostrarMais.removeEventListener("click", listarDiagnosticas);
  btnMostrarMais.addEventListener("click", listarPreviaDiagnosticas);
};

/**
 * Seleciona a avaliação clicada e abre seu respectivo modal
 * @param {*} element Elemento clicado
 */
const abrirDiagnostica = (element) => {
  let diagnostica = element.currentTarget.getAttribute("data-diagnostica");
  console.log("Diagnostica", diagnostica);
  const modalDiagnostica = document.getElementById("visualizar-diagnostica");

  if (diagnostica) {
    if (!localStorage.getItem("diagnosticasRelevantes")) {
      throw new Error("As diagnósticas não foram encontradas!");
    }

    const diagnosticasRelevantes = JSON.parse(
      localStorage.getItem("diagnosticasRelevantes")
    );

    const diagnosticaSelecionada = diagnosticasRelevantes.find(
      (element) => element.diagnostica == diagnostica
    );

    console.log(diagnosticaSelecionada);
    preencherDiagnostica(diagnosticaSelecionada);
  }
};

/**
 * Preenche o modal de visualização com base da diagnóstica retornada
 * @param {*} diagnostica JSON Object com diagnóstica selecionada
 */
const preencherDiagnostica = (diagnostica) => {
  const modalDiagnostica = document.getElementById("visualizar-diagnostica");
  const professoresChips = modalDiagnostica.querySelector(
    ".professores .chips"
  );
  const perfisChips = modalDiagnostica.querySelector(".perfis .chips");

  modalDiagnostica.classList.toggle("is-active");
  const classeTipo = diagnostica.tipo === "true" ? "positiva" : "negativa";
  const modalCard = modalDiagnostica.querySelector(".modal-card");

  const titulo = modalDiagnostica.querySelector(".modal-card-title");
  titulo.innerHTML = diagnostica.aluno.nome;
  modalDiagnostica.classList.add(classeTipo);

  diagnostica.professores.map((professor) => {
    professoresChips.appendChild(gerarChips(professor.nome));
  });

  diagnostica.perfis.map((perfil) => {
    perfisChips.appendChild(gerarChips(perfil.nome, perfil.tipo));
  });
};

const atualizarResultados = (avaliacao, quantidade) => {
  const resultados = document.querySelector(".reuniao-info");

  const contador = resultados.querySelector(`.${avaliacao} .quantidade`);

  contador.innerHTML = quantidade;
};

/**
 * Solicita e apresenta as avaliações diagnósticas na página
 */
export const listarDiagnosticas = () => {
  solicitarDiagnosticas()
    .then((diagnosticas) => {
      console.log("Listando todas as diagnósticas");
      removerPrevia();
      diagnosticas.map((diagnostica) => addDiagnosticaCard(diagnostica));
      mostrarMais();

      localStorage.setItem(
        "diagnosticasRelevantes",
        JSON.stringify(diagnosticas)
      );
    })
    .catch((err) => {
      console.error(err);
    });
};

/**
 * Solicita e lista a prévia das avaliações diagnósticas
 */
export const listarPreviaDiagnosticas = () => {
  solicitarDiagnosticas()
    .then((diagnosticas) => {
      console.log("> Listando Prévia Diagnóstica!");
      gerarPreviaDiagnostica(diagnosticas);
      mostrarMenos();

      // Contabiliza as ocorrências negativas e positivas
      const negativas = diagnosticas.filter((value) => value.tipo === "false");
      const positivas = diagnosticas.filter((value) => value.tipo === "true");
      atualizarResultados("positivos", positivas.length);
      atualizarResultados("negativos", negativas.length);

      localStorage.setItem(
        "diagnosticasRelevantes",
        JSON.stringify(diagnosticas)
      );
    })
    .catch((err) => {
      console.error(err);
    });
};

export default () => {
  listarPreviaDiagnosticas();
};
