import { sendRequest, showMessage, getCookie } from "./utils.js";
import { autenticarRepresentante } from "./components/Autenicacao.js";

// autenticarRepresentante();

const listener = () => {
  const btnModalEnsino = document.querySelector("#btnEnsino");
  btnModalEnsino.addEventListener("click", (e) => abrirNovoEnsino());

  const btnModalExperiencia = document.querySelector("#btnExperiencia");
  btnModalExperiencia.addEventListener("click", (e) => abrirNovoExperiencia());

  const btnSalvarEnsino = document.getElementById("salvar-ensino");
  btnSalvarEnsino.addEventListener("click", (e) => salvarAprendizado(e));

  const btnSalvarExperiencia = document.getElementById("salvar-experiencia");
  btnSalvarExperiencia.addEventListener("click", (e) => salvarExperiencia(e));

  const btnExcluirExperiencia = document.getElementById("excluir-experiencia");
  btnExcluirExperiencia.addEventListener("click", (e) => excluirExperiencia(e));

  const btnExcluirAprendizado = document.getElementById("excluir-ensino");
  btnExcluirAprendizado.addEventListener("click", (e) => excluirAprendizado(e));

  const btnFiltrarExperiencia = document.getElementById("filtrarExperiencia");
  btnFiltrarExperiencia.addEventListener("click", (e) => filtrarExperiencia());

  const btnFiltrarEnsino = document.getElementById("filtrarEnsino");
  btnFiltrarEnsino.addEventListener("click", (e) => filtrarEnsino());

  const btnRemoverFiltro = document.getElementById("removerFiltro");
  btnRemoverFiltro.addEventListener("click", (e) => removerFiltro());
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
  const modalAprendizado = document.getElementById("avaliacao-aprendizado");
  const modalExperiencia = document.getElementById("avaliacao-experiencia");

  let closeBtn = modalAprendizado.querySelector(".modal-close-btn");
  closeBtn.addEventListener("click", (evnt) => {
    fecharAprendizado();
  });
  let bgModal = modalAprendizado.querySelector(".modal-background");
  bgModal.addEventListener("click", (evnt) => {
    fecharAprendizado();
  });
  closeBtn = modalExperiencia.querySelector(".modal-close-btn");
  closeBtn.addEventListener("click", (evnt) => {
    fecharExperiencia();
  });
  bgModal = modalExperiencia.querySelector(".modal-background");
  bgModal.addEventListener("click", (evnt) => {
    fecharExperiencia();
  });
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
    localStorage.removeItem("experiencia");
  }

  document.getElementById("experiencia-titulo").value = "";
  document.getElementById("experiencia-categoria").value = "";

  document.getElementById("experiencia-disciplinas").value = "";
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
    dados.token != ""
  ) {
    console.log(dados);
    sendRequest(dados)
      .then((response) => {
        console.log(response);
        fecharAprendizado();
        listarAvaliacoes();
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

  const aprendizado = localStorage.getItem("aprendizado") || "";

  const token = getCookie("token");
  let dados = {
    acao: "Aprendizados/cadastrar",
    token: token,
    disciplina: disciplina,
    estudantes: estudantes,
    descricao: descricao,
  };

  if (aprendizado !== "") {
    (dados.acao = "Aprendizados/alterar"), (dados.aprendizado = aprendizado);
  }
  return dados;
};

const salvarExperiencia = () => {
  console.log(">> Apertou Experiencia");
  const dados = pegarDadosExperiencia();

  if (dados.token != "" && dados.titulo != "" && dados.classificacao != "") {
    console.log(dados);
    sendRequest(dados)
      .then((response) => {
        console.log(response);
        fecharExperiencia();
        listarAvaliacoes();
        showMessage(
          "Deu certo!",
          "A experiência já foi salva.",
          "success",
          4000
        );
      })
      .catch((err) => {
        console.error(err);
        showMessage(
          "Ops, deu errado!",
          "Não foi possível salvar a experiência",
          "error",
          5000
        );
      });
  } else {
    showMessage(
      "Confira seus dados!",
      "Pode existir algum erro nos dados informados.",
      "warning",
      5000
    );
  }
};

const pegarDadosExperiencia = (params) => {
  const titulo = document.getElementById("experiencia-titulo").value;
  const classificacao = document.getElementById("experiencia-categoria").value;
  const disciplinasChips = document.querySelectorAll(
    "#experiencia-disciplinas-selecionadas > div.chip"
  );

  let disciplinas = [];
  disciplinasChips.forEach((disciplinaChip) => {
    disciplinas.push(disciplinaChip.getAttribute("data-disciplina-id"));
  });

  console.log(disciplinas);
  const descricao = document.getElementById("experiencia-descricao").value;

  const experiencia = localStorage.getItem("experiencia") || "";

  const token = getCookie("token");
  let dados = {
    acao: "Experiencias/cadastrar",
    token: token,
    titulo: titulo,
    descricao: descricao,
    classificacao: classificacao,
    disciplinas: disciplinas,
  };

  if (experiencia !== "") {
    (dados.acao = "Experiencias/alterar"), (dados.experiencia = experiencia);
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
  const estudantesSelecionados = document.getElementById(local);
  estudantesSelecionados.insertAdjacentElement("afterbegin", chip);
};

/**
 *
 * @param {string} nome Texto adicionado ao elemento
 * @param {string} id Identificação do elemento
 * @param {string} local Id do elemento DOM onde o chip será inserido
 */
const addChipDisciplina = (nome, id, local) => {
  let chip = document.createElement("div");
  chip.classList.add("chip", "item");
  chip.setAttribute("data-disciplina-id", id);

  chip.innerHTML += `
    <span class="chip-text">${nome}</span>
    <span class="chip-close">&times;</span>
  `;
  chip.addEventListener("click", (event) => delChip(event));
  const disciplinasSelecionadas = document.getElementById(local);
  disciplinasSelecionadas.insertAdjacentElement("afterbegin", chip);
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
    const turma = localStorage.getItem("turmaAtual");
    let dados = { acao: "Disciplinas/listarDisciplinasTurma", turma: turma };

    return sendRequest(dados)
      .then((disciplinas) => {
        return disciplinas.filter((disciplina) => {
          return disciplina.nome
            .toLowerCase()
            .startsWith(inputValue.toLowerCase());
        });
      })
      .then((filtrado) => {
        return filtrado.map((disciplina) => {
          return { label: disciplina.nome, value: disciplina.codigo };
        });
      })
      .then((transformado) => {
        return transformado.slice(0, 5);
      });
  };

  var onSelect = function (state) {
    console.log("> O brabo tem nome - Experiencia Estudantes");
    console.log(state);

    addChipDisciplina(
      state.label,
      state.value,
      "experiencia-disciplinas-selecionadas"
    );

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
    const turma = localStorage.getItem("turmaAtual") || null;
    let dados = { acao: "Turmas/listarEstudantes", turma: turma };

    return sendRequest(dados)
      .then((estudantes) => {
        return estudantes.filter((estudante) => {
          return estudante.nome
            .toLowerCase()
            .startsWith(inputValue.toLowerCase());
        });
      })
      .then((filtrado) => {
        return filtrado.map((estudante) => {
          return { label: estudante.nome, value: estudante.matricula };
        });
      })
      .then((transformado) => {
        return transformado.slice(0, 5);
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
    const turma = localStorage.getItem("turmaAtual");
    let dados = { acao: "Disciplinas/listarDisciplinasTurma", turma: turma };

    return sendRequest(dados)
      .then((disciplinas) => {
        return disciplinas.filter((disciplina) => {
          return disciplina.nome
            .toLowerCase()
            .startsWith(inputValue.toLowerCase());
        });
      })
      .then((filtrado) => {
        return filtrado.map((disciplina) => {
          return { label: disciplina.nome, value: disciplina.codigo };
        });
      })
      .then((transformado) => {
        return transformado.slice(0, 5);
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
  // const reuniao = localStorage.getItem("conselhoAtual") || "";
  const token = getCookie("token");
  if (token === "") {
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
    token: token,
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
    abrirAprendizado(event);
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
  // const reuniao = localStorage.getItem("conselhoAtual") || "";
  const token = getCookie("token");
  if (token === "") {
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
    token: token,
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
    abrirExperiencia(event);
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
      console.log(experiencias);
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

const abrirAprendizado = (element) => {
  console.log("Apertou o aprendizado!");

  let aprendizado = element.currentTarget.getAttribute("data-aprendizado");

  console.log(aprendizado);

  const modalAprendizado = document.getElementById("avaliacao-aprendizado");

  if (aprendizado) {
    localStorage.setItem("aprendizado", aprendizado);
    modalAprendizado.classList.toggle("is-active");

    const dados = {
      acao: "Aprendizados/selecionar",
      aprendizado: aprendizado,
    };

    sendRequest(dados)
      .then((response) => {
        console.log(response);
        preencherAprendizado(response);
      })
      .catch((err) => {
        console.error(err);
      });
  } else {
    showMessage(
      "Houve um erro!",
      "Não foi possível abrir o aprendizado.",
      "warning",
      5000
    );
  }
};

const preencherAprendizado = (aprendizado) => {
  aprendizado.estudantes.map((estudante) => {
    addChip(
      estudante.nome,
      estudante.matricula,
      "ensino-estudantes-selecionados"
    );
  });
  const disciplina = document.querySelector("#ensino-disciplina");
  disciplina.value = aprendizado.disciplina.nome;
  disciplina.setAttribute("data-disciplina", aprendizado.disciplina.codigo);

  console.log("Preenchendo Aprendizado!");
  console.log(aprendizado);
  document.getElementById("ensino-descricao").value = aprendizado.observacao;
};

const abrirExperiencia = (element) => {
  console.log("Apertou a experiência!");
  let experiencia = element.currentTarget.getAttribute("data-experiencia");

  const modalExperiencia = document.getElementById("avaliacao-experiencia");

  if (experiencia) {
    localStorage.setItem("experiencia", experiencia);
    modalExperiencia.classList.toggle("is-active");

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
      "Não foi possível abrir a experiência.",
      "warning",
      5000
    );
  }
};

const preencherExperiencia = (experiencia) => {
  experiencia.disciplinas.map((disciplina) =>
    addChipDisciplina(
      disciplina.nome,
      disciplina.codigo,
      "experiencia-disciplinas-selecionadas"
    )
  );
  console.log("Preenchendo Experiência");
  console.log(experiencia);
  document.getElementById("experiencia-titulo").value = experiencia.titulo;
  document.getElementById("experiencia-categoria").value =
    experiencia.classificacao.id;
  document.getElementById("experiencia-descricao").value =
    experiencia.descricao;
};

const listarCategorias = () => {
  sendRequest({ acao: "Classificacoes/listar" })
    .then((response) => {
      preencherCategorias(response);
    })
    .catch((err) => {
      console.error(err);
    });
};

const preencherCategorias = (dados) => {
  const selectCategoria = document.getElementById("experiencia-categoria");
  dados.map((categoria) => {
    let option = document.createElement("option");
    option.setAttribute("value", categoria.idClassificacao);
    option.appendChild(document.createTextNode(categoria.nome));
    selectCategoria.appendChild(option);
  });
};
const listarAvaliacoes = () => {
  document.querySelector(".avaliacoes").innerHTML = "";
  listarAprendizados();
  listarExperiencias();
};

const filtrarEnsino = () => {
  document.querySelector(".avaliacoes").textContent = "";
  listarAprendizados();
};

const filtrarExperiencia = () => {
  document.querySelector(".avaliacoes").textContent = "";
  listarExperiencias();
};

const removerFiltro = () => {
  listarAvaliacoes();
};

const obterInformacoesTurma = () => {
  const token = getCookie("token");
  if (token !== null) {
    const dados = { acao: "Turmas/informacoesTurma", token: token };

    sendRequest(dados)
      .then((response) => {
        console.log(response);
        apresentarInformacoesTurma(response);
      })
      .catch((err) => {
        console.error(err);
        showMessage(
          "Houve um erro!",
          "Não foi possível acessar as informações da turma.",
          "error",
          4000
        );
      });
  }
};

const apresentarInformacoesTurma = (dados) => {
  const cardInfoTurma = document.querySelector(".turma-info");

  cardInfoTurma.querySelector("#nome").innerHTML = dados.nome;
  cardInfoTurma.querySelector("#curso").innerHTML = dados.curso;
  cardInfoTurma.querySelector("#codigo").innerHTML = dados.codigo;

  localStorage.setItem("turmaAtual", dados.codigo);
};

const excluirExperiencia = () => {
  const experiencia = localStorage.getItem("experiencia");

  if (experiencia) {
    const dados = { acao: "Experiencias/excluir", experiencia: experiencia };

    sendRequest(dados)
      .then((response) => {
        fecharExperiencia();
        showMessage(
          "Deu certo!",
          "A experiência foi excluída com sucesso.",
          "success"
        );
        listarAvaliacoes();
      })
      .catch((err) => {
        console.error(err);
        showMessage(
          "Houve um erro!",
          "Não foi possível excluir a experiência.",
          "error"
        );
        fecharExperiencia();
      });
  }
};

const excluirAprendizado = () => {
  const aprendizado = localStorage.getItem("aprendizado");

  if (aprendizado) {
    const dados = { acao: "Aprendizados/excluir", aprendizado: aprendizado };

    sendRequest(dados)
      .then((response) => {
        fecharAprendizado();
        showMessage(
          "Deu certo!",
          "A dificuldade de aprendizado foi excluída com sucesso.",
          "success"
        );
        listarAvaliacoes();
      })
      .catch((err) => {
        fecharAprendizado();
        showMessage(
          "Houve um erro!",
          "Não foi possível excluir a dificuldade de aprendizado",
          "error"
        );
      });
  }
};
obterInformacoesTurma();
listarAprendizados();
listarExperiencias();

listarCategorias();
deleteProfessor();
autocompleteEnsinoDisciplina();
autocompleteEnsinoEstudantes();
autocompleteExperienciaDisciplinas();
closeModal();
listener();
