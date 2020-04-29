const listener = () => {
  const btnModalEnsino = document.querySelector("#btnEnsino");
  btnModalEnsino.addEventListener("click", (e) => abrirModalEnsino());

  const btnModalExperiencia = document.querySelector("#btnExperiencia");
  btnModalExperiencia.addEventListener("click", (e) => abrirModalExperiencia());
};

/**
 * Adiciona a classe is-active para o modal selecionado
 */
const abrirModalEnsino = (e) => {
  const modalEnsino = document.querySelector("#avaliacao-aprendizado");
  modalEnsino.classList.toggle("is-active");
};

/**
 * Adiciona a classe is-active para o modal selecionado
 */
const abrirModalExperiencia = (e) => {
  const modalExperiencia = document.querySelector("#avaliacao-experiencia");
  modalExperiencia.classList.toggle("is-active");
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
          { label: "alexandre Lopes", value: "2" },
          { label: "Alexandre", value: "2" },
          { label: "Alexandre", value: "2" },
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
  };

  bulmahead("ensino-disciplina", "ensino-disciplina-menu", api, onSelect, 200);
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

    addChip(state.label, state.value, "ensino-estudante-selecionados");

    const input = document.querySelector("#ensino-estudante");
    input.value = "";
  };

  bulmahead("ensino-estudantes", "ensino-estudantes-menu", api, onSelect, 200);
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

  const professoresSelecionados = document.querySelector(
    // ".chips.estudantes-selecionados"
    "#" + local
  );
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

deleteProfessor();
autocompleteEnsinoDisciplina();
autocompleteEnsinoEstudantes();
autocompleteExperienciaDisciplinas();
listener();
