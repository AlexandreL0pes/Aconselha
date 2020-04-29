


let autocompleteDisciplina = () => {
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

let autocompleteEstudantes = () => {
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

    addChip(state.label, state.value);

    const input = document.querySelector("#ensino-estudante");
    input.value = "";

  };

  bulmahead("ensino-estudante", "ensino-estudante-menu", api, onSelect, 200);
};

/**
 *
 * @param {string} nome Texto adicionado ao elemento
 * @param {string} id Identificação do elemento
 */
const addChip = (nome, id) => {
//   <div class="chip estudante" data-estudante-id="1459180">
//   <span class="chip-text">Alexandre Lopes</span>
//   <span class="chip-close">×</span>
// </div>

  let chip = document.createElement("div");
  chip.classList.add("chip","item");
  chip.setAttribute("data-aluno-id", id);

  chip.innerHTML += `
    <span class="chip-text">${nome}</span>
    <span class="chip-close">&times;</span>
  `;
  chip.addEventListener("click", (event) => delChip(event));

  const professoresSelecionados = document.querySelector(
    ".chips.estudantes-selecionados"
  );
  professoresSelecionados.insertAdjacentElement("afterbegin", chip);
};

/**
 * Adiciona auscultador nos chips de professor
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

deleteProfessor();
autocompleteDisciplina();
autocompleteEstudantes();
