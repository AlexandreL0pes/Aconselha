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

let autocompleteAluno = () => {
  // Quando tiver fazendo request pro server, utilizar essa função
  var api = function (inputValue) {
    return fetch(
      "https://cdn.rawgit.com/mshafrir/2646763/raw/8b0dbb93521f5d6889502305335104218454c2bf/states_titlecase.json"
    )
      .then(function (resp) {
        return [
          { label: "Alexandre Lopes", value: "2" },
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
    console.log("> O brabo tem nome");
    console.log(state);

    // var selected = document.getElementById("selected");
    // selected.innerHTML =
    //   '{label: "' + state.label + '", value: "' + state.value + '"}';
    // selected.parentNode.style.display = "block";
  };

  bulmahead("prova", "prova-menu", api, onSelect, 200);
};

let autocompleteProfessor = () => {
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
    console.log("> O brabo tem nome");
    console.log(state);

    addChip(state.label, state.value);

    const input = document.querySelector("#professores");
    input.value = "";


    // var selected = document.getElementById("selected");
    // selected.innerHTML =
    //   '{label: "' + state.label + '", value: "' + state.value + '"}';
    // selected.parentNode.style.display = "block";
  };

  bulmahead("professores", "professores-menu", api, onSelect, 200);
};
/**
 *
 * @param {string} nome Texto adicionado ao elemento
 * @param {string} id Identificação do elemento
 */
const addChip = (nome, id) => {
  // let chip = `
  //   <div class="chip" data-professor-id="${id}">
  //     <span class="chip-text">${nome}</span>
  //     <span class="chip-close">&times;</span>
  //   </div>
  // `;

  let chip = document.createElement("div");
  chip.classList.add("chip");
  chip.setAttribute("data-professor-id", id);

  chip.innerHTML += `
    <span class="chip-text">${nome}</span>
    <span class="chip-close">&times;</span>
  `;
  chip.addEventListener("click", (event) => delChip(event));

  const professoresSelecionados = document.querySelector(
    ".chips.professores-selecionados"
  );
  professoresSelecionados.insertAdjacentElement("afterbegin", chip);
};

autocompleteAluno();
autocompleteProfessor();
deleteProfessor();
