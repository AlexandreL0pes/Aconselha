const listener = () => {
  const btnModalEnsino = document.querySelector("#btnEnsino");
  btnModalEnsino.addEventListener("click", (e) => abrirNovoEnsino());

  const btnModalExperiencia = document.querySelector("#btnExperiencia");
  btnModalExperiencia.addEventListener("click", (e) => abrirNovoExperiencia());

  const btnSalvarExperiencia = document.getElementById("salvar-ensino");
  btnSalvarExperiencia.addEventListener("click", (e) => salvarAprendizado(e));
};

/**
 * Adiciona a classe is-active para o modal selecionado
 */
const abrirNovoEnsino = (e) => {
  const modalEnsino = document.querySelector("#avaliacao-aprendizado");
  modalEnsino.classList.toggle("is-active");
};

/**
 * Adiciona a classe is-active para o modal selecionado
 */
const abrirNovoExperiencia = (e) => {
  const modalExperiencia = document.querySelector("#avaliacao-experiencia");
  modalExperiencia.classList.toggle("is-active");
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
 * Esconde o modal, remove o aluno do modal atual e apaga os perfis selecionados
 * @param {DOM Element} modal Modal de Avaliação Diagnóstica
 */
const fecharAvaliacao = (modal) => {
  modal.classList.toggle("is-active");
  if (localStorage.getItem("encaminhamento")) {
    localStorage.removeItem("encaminhamento");
  }
};

const fecharAprendizado = () => {
  const modal = document.getElementById("avaliacao-aprendizado");
  modal.classList.toggle("is-active");

  if (localStorage.getItem("aprendizado")) {
    localStorage.removeItem("aprendizado");
  }

  document.querySelector("#ensino-estudantes-selecionados").textContent = "";
  const disciplina = document.querySelector("#ensino-disciplina");
  disciplina.setAttribute("data-disciplina", "");
  disciplina.value = "";
  document.getElementById("ensino-descricao").value = "";
};

const fecharExperiencia = () => {
  const modal = document.getElementById("avaliacao-experiencia");
  modal.classList.toggle("is-active");
  if (localStorage.getItem("experiencia")) {
    localStorage.setItem("experiencia", "");
  }

  document.getElementById("avaliacao-experiencia").value = "";
  document.getElementById("experiencia-categoria").value;

  document.getElementById("experiencia-disciplinas-selecionadas").textContent =
    "";
  document.getElementById("experiencia-descricao").value = "";
};

const salvarAprendizado = (e) => {
  console.log(">> Apertou");
  let dados = pegarDadosAprendizado();
  if (
    dados.disciplina != "" &&
    dados.estudantes.length > 0 &&
    dados.descricao != "" &&
    dados.reuniao != ""
  ) {
    console.log(dados);
    sendRequest(dados)
      .then((response) => {
        console.log(response);
        fecharAprendizado();
        showMessage("Deu certo!", "A avaliação já foi salva!", "success", 4000);
      })
      .catch((err) => {
        console.error(err);
        showMessage(
          "Ops, deu errado!",
          "Não foi possível salvar a avaliação.",
          "error",
          5000
        );
      });
  } else {
    showMessage(
      "Confira seus dados!",
      "Existe algum erro nos dados informados.",
      "warning",
      5000
    );
  }
};

const pegarDadosAprendizado = (params) => {
  const disciplina = document
    .querySelector("#ensino-disciplina")
    .getAttribute("data-disciplina");

  const estudantesChips = document.querySelectorAll(
    "#ensino-estudantes-selecionados > div.chip"
  );

  let estudantes = [];
  estudantesChips.forEach((estudanteChip) => {
    estudantes.push(estudanteChip.getAttribute("data-aluno-id"));
  });

  const descricao = document.getElementById("ensino-descricao").value;

  const reuniao = localStorage.getItem("conselhoAtual") || "";
  const aprendizado = localStorage.getItem("aprendizado") || "";

  let dados = {
    acao: "Aprendizados/cadastrar",
    reuniao: reuniao,
    disciplina: disciplina,
    estudantes: estudantes,
    descricao: descricao,
  };

  if (aprendizado !== "") {
    (dados.acao = "Apredizados/alterar"), (dados.aprendizado = aprendizado);
  }
  return dados;
};

/**
 *
 * @param {string} nome Texto adicionado ao elemento
 * @param {string} id Identificação do elemento
 * @param {string} local Id do elemento DOM onde o chip será inserido
 */
const addChip = (nome, id, local) => {
  let chip = document.createElement("div");
  chip.classList.add("chip", "item");
  chip.setAttribute("data-aluno-id", id);

  chip.innerHTML += `
    <span class="chip-text">${nome}</span>
    <span class="chip-close">&times;</span>
  `;
  chip.addEventListener("click", (event) => delChip(event));
  const professoresSelecionados = document.getElementById(local);
  professoresSelecionados.insertAdjacentElement("afterbegin", chip);
};

/**
 * Adiciona auscultador nos chips de professor para a exclusão
 * @params {} null
 */
const deleteProfessor = () => {
  const chips = document.querySelectorAll(
    ".professores-selecionados > .chip > .chip-close"
  );

  chips.forEach((chip) => {
    chip.addEventListener("click", (event) => delChip(event));
  });
};

/**
 * Efetua a remoção de um elemento do DOM
 * @param {string} event DOM Element
 */
const delChip = (event) => {
  console.log("> Removendo o elemento!");
  event.target.parentElement.remove();
};

/**
 * Adiciona auscultador para disparar as sugestões de DISCIPLINAS no modal de Experiência
 * @params {} null
 */
let autocompleteExperienciaDisciplinas = () => {
  var api = function (inputValue) {
    return fetch(
      "https://cdn.rawgit.com/mshafrir/2646763/raw/8b0dbb93521f5d6889502305335104218454c2bf/states_titlecase.json"
    )
      .then(function (resp) {
        return [
          { label: "Matemática", value: "1459180" },
          { label: "Língua Portuguesa", value: "97312" },
          { label: "História", value: "417530" },
          { label: "Artes", value: "914402" },
          { label: "Geografia", value: "81183" },
          { label: "Filosofia", value: "41917" },
        ];
        // return resp.json();
      })
      .then(function (states) {
        return states.filter(function (state) {
          return state.label.startsWith(inputValue);
        });
      })
      .then(function (filtered) {
        return filtered.map(function (state) {
          return { label: state.label, value: state.value };
        });
      })
      .then(function (transformed) {
        return transformed.slice(0, 5);
      });
  };

  var onSelect = function (state) {
    console.log("> O brabo tem nome - Experiencia Estudantes");
    console.log(state);

    addChip(state.label, state.value, "experiencia-disciplinas-selecionadas");

    const input = document.querySelector("#experiencia-disciplinas");
    input.value = "";
  };

  bulmahead(
    "experiencia-disciplinas",
    "experiencia-disciplinas-menu",
    api,
    onSelect,
    200
  );
};

/**
 * Adiciona auscultador para disparar as sugestões de ESTUDANTES no modal de ENSINO
 * @params {} null
 */
let autocompleteEnsinoEstudantes = () => {
  var api = function (inputValue) {
    return fetch(
      "https://cdn.rawgit.com/mshafrir/2646763/raw/8b0dbb93521f5d6889502305335104218454c2bf/states_titlecase.json"
    )
      .then(function (resp) {
        return [
          { label: "Adriano Braga", value: "1459180" },
          { label: "Lucas Faria", value: "97312" },
          { label: "Rangel Rigo", value: "417530" },
          { label: "Marcos Morais", value: "914402" },
          { label: "Jaqueline Ribeiro", value: "81183" },
          { label: "Ramayane Braga", value: "41917" },
        ];
        // return resp.json();
      })
      .then(function (states) {
        return states.filter(function (state) {
          return state.label.startsWith(inputValue);
        });
      })
      .then(function (filtered) {
        return filtered.map(function (state) {
          return { label: state.label, value: state.value };
        });
      })
      .then(function (transformed) {
        return transformed.slice(0, 5);
      });
  };

  var onSelect = function (state) {
    console.log("> O brabo tem nome - estudantes");
    console.log(state);

    addChip(state.label, state.value, "ensino-estudantes-selecionados");

    const input = document.querySelector("#ensino-estudantes");
    input.value = "";
  };

  bulmahead("ensino-estudantes", "ensino-estudantes-menu", api, onSelect, 200);
};

/**
 * Adiciona auscultador para disparar as sugestões de disciplinas no modal de ENSINO
 * @params {} null
 */
let autocompleteEnsinoDisciplina = () => {
  // Quando tiver fazendo request pro server, utilizar essa função
  var api = function (inputValue) {
    return fetch(
      "https://cdn.rawgit.com/mshafrir/2646763/raw/8b0dbb93521f5d6889502305335104218454c2bf/states_titlecase.json"
    )
      .then(function (resp) {
        return [
          { label: "Matemática", value: "3" },
          { label: "Artes", value: "2" },
          { label: "História", value: "1" },
        ];
        // return resp.json();
      })
      .then(function (states) {
        return states.filter(function (state) {
          return state.label.startsWith(inputValue);
        });
      })
      .then(function (filtered) {
        return filtered.map(function (state) {
          return { label: state.label, value: state.value };
        });
      })
      .then(function (transformed) {
        return transformed.slice(0, 5);
      });
  };

  var onSelect = function (state) {
    console.log("> O brabo tem nome - Disciplina");
    console.log(state);

    const input = document.querySelector("#ensino-disciplina");
    input.setAttribute("data-disciplina", state.value);
  };

  bulmahead("ensino-disciplina", "ensino-disciplina-menu", api, onSelect, 200);
};

/**
 * Dispara requisição assíncrona para obtenção dos aprendizados
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
        "Não foi possível obter os aprendizados!",
        "error",
        5000
      );
      console.error(err);
    });
};

/**
 * Gera e adiciona um card aprendizado a partir do JSON especificado 
 * @param {JSON} aprendizado 
 */
const addAprendizadoCard = (aprendizado) => {
  let card = document.createElement("div");

  card.classList.add("card-avaliacao", "ensino");
  card.setAttribute("data-aprendizado", aprendizado.aprendizado);
  card.innerHTML = `
  <p class="subtitulo is-9" name="">Ensino-Aprendizado</p>
  <p class="subtitulo is-7">${aprendizado.disciplina.nome}</p>
  <div class="perfis">
    <div class="chip">
      <span class="chip-text">${aprendizado.estudantes.length} ${
    aprendizado.estudantes.length > 1 ? "alunos" : "aluno"
  }</span>
    </div>
  </div>
  `;

  card.addEventListener("click", (event) => {
    console.log(event);
  });
  const avaliacoes = document.querySelector(".avaliacoes");
  avaliacoes.append(card);
};

/**
 * Faz a requisição dos aprendizados, impressão dos card's e atualiza a qtd de Avaliações 
 */
const listarAprendizados = () => {
  solicitarAprendizados()
    .then((aprendizados) => {
      aprendizados.map((aprendizado) => addAprendizadoCard(aprendizado));
      atualizarAvaliacoes();
    })
    .catch((err) => {
      console.error(err);
    });
};

/**
 * Dispara requisição assíncrona para obtenção das experiencias
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
        "Ops, deu errado!",
        "Não conseguimos obter as experiências da reunião atual.",
        "error",
        5000
      );
      return false;
    });
};

/**
 * Gera e adiciona um card experiencia a partir do JSON especificado 
 * @param {JSON} experiencia 
 */
const addExperienciaCard = (experiencia) => {
  let card = document.createElement("div");
  card.classList.add("card-avaliacao", "experiencia");
  card.setAttribute("data-experiencia", experiencia.experiencia);
  card.innerHTML = `
  <p class="subtitulo is-9">Experiências</p>
  <p class="subtitulo is-7">${experiencia.titulo}</p>
  <div class="perfis">
    <div class="chip">
      <span class="chip-text">${experiencia.classificacao}</span>
    </div>
  </div>
  `;
  card.addEventListener("click", (event) => {
    console.log(event);
  });

  const avaliacoes = document.querySelector(".avaliacoes");
  avaliacoes.append(card);
};

/**
 * Faz a requisição das experiencias, impressão dos card's e atualiza a qtd de Avaliações 
 */
const listarExperiencias = () => {
  atualizarAvaliacoes();
  solicitarExperiencias()
    .then((experiencias) => {
      experiencias.map((experiencia) => addExperienciaCard(experiencia));
      atualizarAvaliacoes();
    })
    .catch((err) => {
      console.error(err);
    });
};

/**
 * Contabiliza a quatidade de avaliações listadas na página
 */
const atualizarAvaliacoes = () => {
  const cards = document.querySelectorAll(".card-avaliacao");
  const qtdAvaliacoes = document.querySelector("#qtdAvaliacoes");

  if (cards.length == 0) {
    qtdAvaliacoes.innerHTML = "Nenhum encaminhamento foi cadastrado";
  } else {
    qtdAvaliacoes.innerHTML =
      "Existem " + cards.length + " encaminhamentos salvos";
  }
};

listarAprendizados();
listarExperiencias();

deleteProfessor();
autocompleteEnsinoDisciplina();
autocompleteEnsinoEstudantes();
autocompleteExperienciaDisciplinas();
closeModal();
listener();
