import { showMessage, sendRequest } from "./utils.js";

const listener = () => {
  const atendimentos = document.getElementById("abrirAtendimento");
  atendimentos.addEventListener("click", (e) => abrirAtendimentos());

  const memoria = document.getElementById("abrirMemoria");
  memoria.addEventListener("click", (event) => abrirMemoria());

  closeModal();

  listarPreviaAprendizados();
  listarPreviaExperiencias();
  listarPreviaDiagnosticas();
};

/**
 * Listener para o fechamento do modal
 * @param {*} params
 */
const closeModal = (params) => {
  const modalEnsino = document.getElementById("visualizar-ensino");
  const modalExperiencia = document.getElementById("visualizar-experiencia");
  const modalDiagnostica = document.getElementById("visualizar-diagnostica");

  let closeBtn = modalEnsino.querySelector(".modal-close-btn");
  closeBtn.addEventListener("click", fecharEnsino);
  let bgModal = modalEnsino.querySelector(".modal-background");
  bgModal.addEventListener("click", fecharEnsino);

  closeBtn = modalExperiencia.querySelector(".modal-close-btn");
  closeBtn.addEventListener("click", fecharExperiencia);
  bgModal = modalExperiencia.querySelector(".modal-background");
  bgModal.addEventListener("click", fecharExperiencia);

  closeBtn = modalDiagnostica.querySelector(".modal-close-btn");
  closeBtn.addEventListener("click", fecharDiagnostica);
  bgModal = modalDiagnostica.querySelector(".modal-background");
  bgModal.addEventListener("click", fecharDiagnostica);
};

/**
 * Tira a classe active do modal
 * @param {*} modal Modal DOM Element
 */
const fecharAvaliacao = (modal) => {
  modal.classList.toggle("is-active");
};

/**
 * Abre uma nova aba com a página de memória
 */
const abrirMemoria = () => {
  if (localStorage.getItem("conselhoAtual")) {
    window.open("./memoria.html", "_blank");
  } else {
    showMessage(
      "Houve um erro!",
      "Selecione um conselho antes de prosseguir.",
      "error",
      5000
    );
  }
};

/**
 * Redireciona para a página de atendimentos pedagógicos
 */
const abrirAtendimentos = () => {
  if (localStorage.getItem("conselhoAtual")) {
    window.location = "./atendimentos.html";
  } else {
    showMessage(
      "Houve um erro!",
      "Selecione um conselho antes de prosseguir",
      "error",
      5000
    );
  }
};

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
const listarAprendizados = () => {
  solicitarAprendizados()
    .then((aprendizados) => {
      console.log("> Listando todos os aprendizados!");

      removerPrevia("ensino");
      aprendizados.map((aprendizado) => addAprendizadoCard(aprendizado));
      mostrarMais("ensino");
    })
    .catch((err) => {
      console.error(err);
    });
};

/**
 * Função que faz a requisição dos aprendizados e imprime a prévia
 */
const listarPreviaAprendizados = () => {
  solicitarAprendizados()
    .then((aprendizados) => {
      console.log("> Listando Prévia!");
      gerarPrevia(aprendizados);
      mostrarMenos("ensino");
    })
    .catch((err) => {
      console.error(err);
    });
};

/**
 * Lista os 3 primeiros aprendizados e  adiciona o card restante
 * @param {*} aprendizados Lista JSON com todos os aprendizados
 */
const gerarPrevia = (aprendizados) => {
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

/**
 * Remove o card de prévia
 * @param {*} avaliacao String com o id do elemento onde o card está
 */
const removerPrevia = (avaliacao) => {
  const element = document.querySelector("." + avaliacao);
  const previa = element.querySelector(".mostrar-mais");
  previa.remove();

  mostrarMenos(avaliacao);
};

/**
 * Muda o texto e os eventos do botão para mostrar menos
 * @param {*} avaliacao Classe do elemento que engloba a avaliaão
 */
const mostrarMenos = (avaliacao) => {
  console.log("> Mostrando menos!");
  const element = document.querySelector("." + avaliacao);
  const btnMostraMenos = element.querySelector(".mostrar-tudo");
  btnMostraMenos.innerHTML =
    "Mostrar mais <i class='fas fa-angle-down' aria-hidden='true'></i>";

  let previas = {
    experiencia: listarPreviaExperiencias,
    ensino: listarPreviaAprendizados,
    diagnostica: listarPreviaDiagnosticas,
  };

  let total = {
    experiencia: listarExperiencias,
    ensino: listarAprendizados,
    diagnostica: listarDiagnosticas,
  };
  btnMostraMenos.removeEventListener("click", previas[avaliacao]);
  btnMostraMenos.addEventListener("click", total[avaliacao]);
};

/**
 * Muda o texto e os eventos do botão para mostrar mais
 * @param {*} avaliacao Classe do elemento que engloba a avaliaão
 */
const mostrarMais = (avaliacao) => {
  console.log("> Mostrando mais!");
  const element = document.querySelector("." + avaliacao);
  const btnMostrarMais = element.querySelector(".mostrar-tudo");
  btnMostrarMais.innerHTML =
    "Mostrar menos <i class='fas fa-angle-up' aria-hidden='true'></i>";
  let previas = {
    experiencia: listarPreviaExperiencias,
    ensino: listarPreviaAprendizados,
    diagnostica: listarPreviaDiagnosticas,
  };

  let total = {
    experiencia: listarExperiencias,
    ensino: listarAprendizados,
    diagnostica: listarDiagnosticas,
  };
  btnMostrarMais.removeEventListener("click", total[avaliacao]);
  btnMostrarMais.addEventListener("click", previas[avaliacao]);
};

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

/**
 * Solicita e apresenta as experiências na página
 */
const listarExperiencias = () => {
  solicitarExperiencias()
    .then((experiencias) => {
      console.log("> Listando todas as experiências!");
      removerPrevia("experiencia");
      experiencias.map((experiencia) => addExperienciaCard(experiencia));
      mostrarMais("experiencia");
    })
    .catch((err) => {
      console.error(err);
    });
};

/**
 * Solicita e apresenta a prévia de experiência na página
 */
const listarPreviaExperiencias = () => {
  solicitarExperiencias()
    .then((experiencias) => {
      console.log("> Listando Prévia!");
      gerarPreviaExperiencia(experiencias);
      mostrarMenos("experiencia");
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
  }
};

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

const addDiagnosticaCard = (diagnostica) => {
  let card = document.createElement("div");
  // Verificação do tipo do card
  console.log(diagnostica);
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

const gerarProfessoresChip = (professores) => {
  const QTD_PREVIA = 2;

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
 * Solicita e apresenta as avaliações diagnósticas na página
 */
const listarDiagnosticas = () => {
  solicitarDiagnosticas()
    .then((diagnosticas) => {
      console.log("Listando todas as diagnósticas");
      removerPrevia("diagnostica");
      diagnosticas.map((diagnostica) => addDiagnosticaCard(diagnostica));
      mostrarMais("diagnostica");
      console.log(diagnosticas);

      localStorage.setItem(
        "diagnosticasRelevantes",
        JSON.stringify(diagnosticas)
      );
    })
    .catch((err) => {
      console.error(err);
    });
};

const listarPreviaDiagnosticas = () => {
  solicitarDiagnosticas()
    .then((diagnosticas) => {
      console.log("> Listando Prévia Diagnóstica!");
      gerarPreviaDiagnostica(diagnosticas);
      mostrarMenos("diagnostica");
      localStorage.setItem(
        "diagnosticasRelevantes",
        JSON.stringify(diagnosticas)
      );
    })
    .catch((err) => {
      console.error(err);
    });
};

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

const preencherDiagnostica = (diagnostica) => {
  const modalDiagnostica = document.getElementById("visualizar-diagnostica");
  const professoresChips = modalDiagnostica.querySelector(
    ".professores .chips"
  );
  const perfisChips = modalDiagnostica.querySelector(".perfis .chips");

  modalDiagnostica.classList.toggle("is-active");
  const classeTipo = diagnostica.tipo === "true" ? "positiva" : "negativa";
  const modalCard = modalDiagnostica.querySelector(".modal-card");

  modalDiagnostica.classList.add(classeTipo);
  console.log(classeTipo);

  diagnostica.professores.map((professor) => {
    professoresChips.appendChild(gerarChips(professor.nome));
  });

  diagnostica.perfis.map((perfil) => {
    perfisChips.appendChild(gerarChips(perfil.nome, perfil.tipo));
    console.log(perfil);
  });
};

const fecharDiagnostica = () => {
  const modalDiagnostica = document.getElementById("visualizar-diagnostica");
  modalDiagnostica.classList.toggle("is-active");
  const modalCard = modalDiagnostica.querySelector(".modal-card");
  modalDiagnostica.classList.remove("positiva", "negativa");

  const professoresChips = (modalDiagnostica.querySelector(
    ".professores .chips"
  ).innerHTML = "");
  const perfisChips = (modalDiagnostica.querySelector(
    ".perfis .chips"
  ).innerHTML = "");
};

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
    classeClassificacao = "";
  }

  categoria.classList.add(classeClassificacao);
  categoria.innerHTML = experiencia.classificacao.nome;

  observacao.innerHTML = experiencia.descricao;

  experiencia.disciplinas.map((disciplina) => {
    disciplinasChip.appendChild(gerarChips(disciplina.nome));
  });
};

const abrirAprendizado = (element) => {
  let aprendizado = element.currentTarget.getAttribute("data-aprendizado");

  console.log(aprendizado);

  if (aprendizado) {
    // localStorage.setItem("aprendizado")

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
      "Ops, deu errado!",
      "Não foi possível abrir a experiência!",
      "error",
      5000
    );
  }
};

const preencherAprendizado = (ensino) => {
  const modalAprendizado = document.getElementById("visualizar-ensino");

  const disciplina = modalAprendizado.querySelector(".modal-card-title");
  const observacao = modalAprendizado.querySelector(".info .observacao");

  const estudantesChips = modalAprendizado.querySelector(".estudantes .chips");

  modalAprendizado.classList.toggle("is-active");

  console.log(ensino);

  disciplina.innerHTML = ensino.disciplina.nome;
  observacao.innerHTML = ensino.observacao;
  estudantesChips.innerHTML = "";
  ensino.estudantes.map((estudante) =>
    estudantesChips.appendChild(gerarChips(estudante.nome))
  );

};

const fecharExperiencia = () => {
  const modalExperiencia = document.getElementById("visualizar-experiencia");
  modalExperiencia.classList.toggle("is-active");
};

const fecharEnsino = () => {
  const modalEnsino = document.getElementById("visualizar-ensino");
  modalEnsino.classList.toggle("is-active");
};
listener();
