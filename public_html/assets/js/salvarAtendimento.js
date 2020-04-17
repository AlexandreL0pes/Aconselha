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
        return resp.json();
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

autocompleteAluno();
