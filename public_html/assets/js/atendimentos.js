/**
 * Listener para botão novo encaminhamento
 */
const addEncaminhamento = document.getElementById("add-encaminhamento");
addEncaminhamento.addEventListener("click", (e) => {
  abrirNovoEncaminhamento();
});

/**
 * Listener para cards de encaminhamentos
 */
const cardsEncaminhamentos = document.querySelectorAll(".card-encaminhamento");
cardsEncaminhamentos.forEach((card) => {
  card.addEventListener("click", (event) => abrirEncaminhamento(event));
});

/**
 * Listener para salvar Encaminhamento
 */
const btnSalvarEncaminhamento = document.querySelector(
  ".salvar-encaminhamento"
);
btnSalvarEncaminhamento.addEventListener("click", (e) =>
  salvarEncaminhamento(e)
);

/**
 * Abre o modal para a vizualização
 * @param {DOM Element} element
 */
const abrirEncaminhamento = (element) => {
  let encaminhamento = element.currentTarget.getAttribute(
    "data-encaminhamento"
  );

  const modalEncaminhamento = document.getElementById("encaminhamento");

  if (encaminhamento) {
    localStorage.setItem("encaminhamento", encaminhamento);
    modalEncaminhamento.classList.toggle("is-active");

    const dados = {
      acao: "Atendimentos/selecionarAtendimento",
      avaliacao: encaminhamento,
    };
    sendRequest(dados)
      .then((response) => {
        console.log(response);
        preencherEncaminhamento(response);
      })
      .catch((err) => {
        console.error(err);
      });
  } else {
    showMessage(
      "Houve um erro!",
      "Não foi possível abrir o encaminhamento.",
      "warning",
      5000
    );
  }
};

/**
 * Efetua o preenchimento do modal com os parâmetros
 * @param {JSON} dados Dados sobre o encaminhamento
 */
const preencherEncaminhamento = (dados) => {
  dados.professores.map((professor) => {
    addChip(professor.nome, professor.id);
  });
  const estudante = document.querySelector("#aluno");
  estudante.value = dados.estudante.nome;
  estudante.setAttribute("data-aluno", dados.estudante.id);

  document.getElementById("queixa").value = dados.queixa;
  document.getElementById("intervencao").value = dados.intervencao;
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
  document.querySelector(".professores-selecionados").textContent = "";
  
  const estudante = document.querySelector("#aluno");
  estudante.setAttribute("data-aluno","");
  estudante.value = "";
  document.getElementById("queixa").value = "";
  document.getElementById("intervencao").value;
};

/**
 * Adiciona a classe is-active para o modal selecionado
 */
const abrirNovoEncaminhamento = (e) => {
  const modalEncaminhamento = document.querySelector("#encaminhamento");
  modalEncaminhamento.classList.toggle("is-active");
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
 * Exibe a quantidade de encaminhamentos
 */
const atualizarEncaminhamentos = () => {
  const cards = document.querySelectorAll(".card-encaminhamento");
  const qtdAvaliacoes = document.querySelector("#qtdEncaminhamentos");

  if (cards.length == 0) {
    qtdAvaliacoes.innerHTML = "Nenhum encaminhamento foi cadastrado";
  } else {
    qtdAvaliacoes.innerHTML = "Existem " + cards.length + " encaminhamentos salvos";
  }
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

let autocompleteAluno = () => {
  // Quando tiver fazendo request pro server, utilizar essa função
  var api = function (inputValue) {
    return fetch(
      "https://cdn.rawgit.com/mshafrir/2646763/raw/8b0dbb93521f5d6889502305335104218454c2bf/states_titlecase.json"
    )
      .then(function (resp) {
        return [
          { label: "Alexandre Lopes", value: "1" },
          { label: "Alexandre", value: "2" },
          { label: "Alexandre", value: "3" },
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

    const input = document.querySelector("#aluno");
    input.setAttribute("data-aluno", state.value);
  };

  bulmahead("aluno", "aluno-menu", api, onSelect, 200);
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

/**
 *  Dispara a requisição para salvar o encaminhamento
 * @param {*} e
 */
const salvarEncaminhamento = (e) => {
  console.log("> Apertou");

  let dados = pegarDados();
  console.log(dados.professores.length);
  if (
    dados.estudante != "" &&
    dados.professores.length > 0 &&
    dados.queixa != "" &&
    dados.intervencao != "" &&
    dados.reuniao != ""
  ) {
    console.log(dados);

    sendRequest(dados)
      .then((response) => {
        console.log(response);
        fecharAvaliacao(document.getElementById("encaminhamento"));
        listarEncaminhamentos();
        showMessage(
          "Deu certo!",
          "O encaminhamento já foi salvo.",
          "success",
          4000
        );
      })
      .catch((err) => {
        console.error(err);
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

/**
 * Obtem todos os dados para cadastro de encaminhamento
 */
const pegarDados = () => {
  const estudante = document.querySelector("#aluno").getAttribute("data-aluno");
  const professoresChips = document.querySelectorAll(
    ".professores-selecionados > div.chip"
  );

  let professores = [];
  professoresChips.forEach((professorChip) => {
    professores.push(professorChip.getAttribute("data-professor-id"));
  });

  const queixa = document.getElementById("queixa").value;
  const intervencao = document.getElementById("intervencao").value;

  const reuniao = localStorage.getItem("conselhoAtual") || "";

  const encaminhamento = localStorage.getItem("encaminhamento") || "";

  let dados = {
    acao: "Atendimentos/cadastrar",
    reuniao: reuniao,
    estudante: estudante,
    professores: professores,
    queixa: queixa,
    intervencao: intervencao,
  };

  if (encaminhamento !== "") {
    (dados.acao = "Atendimentos/alterar"), (dados.avaliacao = encaminhamento);
  }

  return dados;
};

/**
 * Faz a requisição dos encaminhamento do conselho atual
* @param {} params 
 */
const listarEncaminhamentos = (params) => {
  const reuniao = localStorage.getItem("conselhoAtual") || "";
  const dados = {
    acao: "Atendimentos/listarAtendimentosReuniao",
    reuniao: reuniao,
  };

  sendRequest(dados)
    .then((response) => {
      if (!response.message) {
        response.forEach((encaminhamento) =>
          addEncaminhamentoCard(encaminhamento)
        );
        atualizarEncaminhamentos();
      }
    })
    .catch((err) => {
      console.error(err);
    });
};

/**
 * Gera os cards de encaminhamento 
 * @param {JSON} dados Json contendo os dados necessários para gerar o card encaminhamento
 */
const addEncaminhamentoCard = (dados) => {
  let card = document.createElement("div");

  card.classList.add("cardbox", "card-encaminhamento", "is-info");
  card.setAttribute("data-encaminhamento", dados.encaminhamento);

  card.innerHTML += `
    <p class="subtitulo gray-text is-8">${dados.aluno.curso}</p>
    <p class="subtitulo is-7">${dados.aluno.nome}</p>
    <p class="gray-text subtitulo is-7">${dados.intervencao}</p>
  `;
  card.addEventListener("click", (event) => abrirEncaminhamento(event));

  const encaminhamentos = document.querySelector(".encaminhamentos");

  encaminhamentos.appendChild(card);
};

/**
 * Requisita as ações de intervenção salvas no BD
 */
const listarAcoes = (params) => {
  sendRequest({acao:"Acoes/listarAcoes"}).then((response) => {
    preencherAcoes(response);
  }).catch((err) => {
    console.error(err);
  });
}

/**
 * Preenche o select com os dados informados
 * @param {*} dados Acoes de Intervenção 
 */
const preencherAcoes = (dados) => {
  const selectIntervencao = document.querySelector('select#intervencao');
  dados.forEach(acao => {
    let option = document.createElement('option');
    option.setAttribute("value", acao.id);
    option.appendChild(document.createTextNode(acao.nome));
    selectIntervencao.appendChild(option);
  });
}

listarAcoes();
listarEncaminhamentos();
autocompleteAluno();
autocompleteProfessor();
deleteProfessor();

closeModal();
