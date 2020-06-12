import { sendRequest, showMessage } from "./utils.js";

// const addCoordenador = document.getElementById("add-coordenador");
// addCoordenador.addEventListener("click", (e) => abrirNovoCoordenador());

// const abrirNovoCoordenador = (e) => {
//   const modalCoordenador = document.getElementById("modal-coordenador");
//   modalCoordenador.classList.toggle("is-active");
// };

const btnSalvarCoordenador = document.querySelector(".salvar-coordenador");
btnSalvarCoordenador.addEventListener("click", (e) => salvarCoordenador(e));

/**
 * Abre o modal com as informações do coordenador
 * @param {DOM element} element Card de coordenador clicado
 */
const abrirCoordenador = (element) => {
  let curso = element.currentTarget.getAttribute("data-curso");
  let coordenadorAtual = element.currentTarget.getAttribute("data-coordenador");

  localStorage.setItem("cursoAtual", curso);
  localStorage.setItem("coordenadorAtual", coordenadorAtual);

  console.log("Abrindo Coordenador!");

  const modalCoordenador = document.getElementById("modal-coordenador");
  modalCoordenador.classList.toggle("is-active");

  const dados = {
    acao: "Coordenadores/selecionarCoordenador",
    curso: curso,
  };

  sendRequest(dados)
    .then((response) => {
      console.log(response);
      preencherCoordenador(response.coordenador, response.curso);
    })
    .catch((err) => {
      console.error(err);
    });
};

const fecharCoordenador = () => {
  console.log("> Fechando Coordenador");
  const modal = document.getElementById("modal-coordenador");
  modal.classList.toggle("is-active");

  localStorage.removeItem("cursoAtual");
  localStorage.removeItem("coordenadorAtual");
};

const preencherCoordenador = (coordenador, curso) => {
  if (coordenador.length != 0) {
    const coordenadorInput = document.getElementById("coordenador");
    coordenadorInput.value = coordenador.nome;
    coordenadorInput.setAttribute("data-coordenador", coordenador.pessoa);

    document.getElementById("email-coordenador").value = coordenador.login;
  }
  document.getElementById("coordenacao-curso").innerHTML = curso.nome;
};

/**
 * Abre o modal com as informações do representante
 * @param {DOM element} element Card de representante clicado
 */
const abrirRepresentante = (element) => {
  const modalRepresentante = document.getElementById("representante");
  modalRepresentante.classList.toggle("is-active");

  let turmaAtual = element.currentTarget.getAttribute("data-turma");

  localStorage.setItem("turmaAtual", turmaAtual);
};

const fecharRepresentante = () => {};

/**
 * Abre o modal com as informações do conselheiro
 * @param {DOM element} element Card de conselheiro clicado
 */
const abrirConselheiro = (element) => {
  console.log("> Abrindo conselheiro!");
  const modalConselheiro = document.getElementById("conselheiro");
  modalConselheiro.classList.toggle("is-active");

  let turmaAtual = element.currentTarget.getAttribute("data-turma");
  localStorage.setItem("turmaAtual", turmaAtual);
  console.log(turmaAtual);
};

const fecharRepresentante = () => {};

/**
 * Listener para o fechamento do modal
 * @param {*} params
 */
const closeModal = (params) => {
  const modalCoordenador = document.getElementById("modal-coordenador");
  const modalConselheiro = document.getElementById("modal-conselheiro");
  const modalRepresentante = document.getElementById("modal-representante");

  let closeBtn = modalCoordenador.querySelector(".modal-close-btn");
  closeBtn.addEventListener("click", (event) => fecharCoordenador);
  let bgModal = modalCoordenador.querySelector(".modal-background");
  bgModal.addEventListener("click", (event) => fecharCoordenador);

  let closeBtn = modalConselheiro.querySelector(".modal-close-btn");
  closeBtn.addEventListener("click", (event) => fecharConselheiro);
  let bgModal = modalConselheiro.querySelector(".modal-background");
  bgModal.addEventListener("click", (event) => fecharConselheiro);

  let closeBtn = modalRepresentante.querySelector(".modal-close-btn");
  closeBtn.addEventListener("click", (event) => fecharRepresentante);
  let bgModal = modalRepresentante.querySelector(".modal-background");
  bgModal.addEventListener("click", (event) => fecharRepresentante);
};

/**
 * Listener para listar as sugestões de coordenadores
 */
let autocompleteCoordenador = () => {
  var api = function (inputValue) {
    const dados = { acao: "Servidores/listarServidores" };

    return sendRequest(dados)
      .then((servidores) => {
        return servidores.filter((servidor) => {
          return servidor.nome
            .toLowerCase()
            .startsWith(inputValue.toLowerCase());
        });
      })
      .then((filtrado) => {
        return filtrado.map((servidor) => {
          return { label: servidor.nome, value: servidor.codigo };
        });
      })
      .then((transformado) => {
        return transformado.slice(0, 5);
      });
  };

  var onSelect = function (state) {
    console.log("> O brabo tem nome");
    console.log(state);

    const input = document.querySelector("#coordenador");
    input.setAttribute("data-coordenador", state.value);
    document.getElementById("email-coordenador").value = "";
  };

  bulmahead("coordenador", "coordenador-menu", api, onSelect, 200);
};

/**
 * Dispara a requisição para salvar o coordenador
 * @param {*} e
 */
const salvarCoordenador = (e) => {
  console.log("> Salvando Coordenador");

  let dados = pegarDados();

  console.log(dados);

  if (dados.coordenador == null) {
    showMessage(
      "Confira seus dados!",
      "É necessário informar o novo coordenador",
      "warning",
      4000
    );
    return false;
  }

  sendRequest(dados)
    .then((response) => {
      console.log(response);
      showMessage(
        "Deu certo!",
        "As alterações foram realizadas com sucesso!",
        "success",
        4000
      );
    })
    .catch((err) => {
      console.error(err);
      showMessage(
        "Houve um erro",
        "Não foi possível salvar as alterações, verifique os dados.",
        "warning",
        400
      );
    });
};

/**
 * Função para obtenção dos dados do coordenador
 */
const pegarDados = () => {
  const coordenadorNovo = document
    .getElementById("coordenador")
    .getAttribute("data-coordenador");
  const senha = document.getElementById("senha-coordenador").value;
  const curso = localStorage.getItem("cursoAtual");
  const email = document.getElementById("email-coordenador").value;

  const coordenadorAtual = localStorage.getItem("coordenadorAtual") || "";

  let dados = {
    curso: curso,
    email: email,
    senha: senha,
  };

  // Caso de trocar a senha
  dados.acao = "Coordenadores/alterarSenha";

  console.log(coordenadorAtual);
  // Caso não exista coordenador atual
  if (coordenadorAtual === "") {
    dados = {
      curso: curso,
      coordenador: coordenadorNovo,
      email: email,
      senha: senha,
    };
    dados.acao = "Coordenadores/cadastrar";
  }

  // Caso o novo id seja diferente do antigo
  console.log("CN", coordenadorNovo);
  console.log("CA", coordenadorAtual);

  if (coordenadorNovo !== coordenadorAtual && coordenadorAtual !== "") {
    dados = {
      curso: curso,
      coordenador: coordenadorNovo,
      email: email,
      senha: senha,
    };
    dados.acao = "Coordenadores/atualizarCoordenador";
  }
  return dados;
};

/**
 * Adiciona um card de curso na tela
 * @param {JSON} curso
 */
const addCursoCard = (curso) => {
  let card = document.createElement("div");

  let classCurso = "";
  if (curso.nome === "Informática para Internet") {
    classCurso = "is-info";
  } else if (curso.nome === "Meio Ambiente") {
    classCurso = "is-amb";
  } else {
    classCurso = "is-agro";
  }

  card.classList.add("cardbox", "card-coordenador", classCurso);
  card.setAttribute("data-curso", curso.codigo);

  let texto = curso.coordenador.nome || "";
  let codigoCoordenador = curso.coordenador.pessoa || "";

  card.setAttribute("data-coordenador", codigoCoordenador);

  card.innerHTML += `
    <p class="gray-text">${curso.nome}</p>
    <p class="nome">${texto}</p>
  `;

  card.addEventListener("click", (e) => abrirCoordenador(e));

  const coordenadoresDiv = document.getElementById("coordenadores");
  coordenadoresDiv.appendChild(card);
};

/**
 * Requisição para obter os cursos
 */
const solicitarCursos = () => {
  const dados = { acao: "Cursos/listarCursos" };
  sendRequest(dados)
    .then((response) => {
      if (response.length > 0) {
        document.getElementById("coordenadores").innerHTML = "";
        response.map((curso) => addCursoCard(curso));
      } else {
        const coordenadoresDiv = documet.getElementById("coordenadores");
        const msg = document.createElement("div");
        msg.classList.add("nenhum-resultado");
        msg.innerHTML = "Nenhum curso foi encontrado!";

        coordenadoresDiv.append(msg);
      }
    })
    .catch((err) => {
      console.error(err);
    });
};

/**
 * Adiciona um card de turma na tela
 * @param {JSON} turma
 */
const addTurmaCard = (turma) => {
  let card = document.createElement("div");

  let classCurso = "";
  if (turma.curso === "Informática para Internet") {
    classCurso = "is-info";
  } else if (turma.curso === "Meio Ambiente") {
    classCurso = "is-amb";
  } else {
    classCurso = "is-agro";
  }

  let classCard = "";
  let evento = null;
  let divCards = "";
  let content = `
  <p class="subtitulo is-7 gray-text">${turma.curso}</p>
  <p class="subtitulo is-6">${turma.nome}</p>
`;
  if ("representantes" in turma) {
    classCard = "card-representantes";
    evento = abrirRepresentante;
    divCards = "representantes";
  }

  if ("conselheiro" in turma) {
    classCard = "card-conselheiro";
    evento = abrirConselheiro;
    divCards = "conselheiros";
    let conselheiro = turma.conselheiro.nome || "";
    content = `
      <p class="subtitulo is-8 gray-text">${turma.curso}</p>
      <p class="subtitulo is-7">${conselheiro}</p>
      <p class="subtitulo is-8 gray-text">${turma.nome}</p>
    `;
  }

  card.classList.add("cardbox", classCard, classCurso);
  card.setAttribute("data-turma", turma.codigo);

  card.innerHTML = content;

  card.addEventListener("click", (e) => evento(e));
  divCards = document.getElementById(divCards);
  divCards.appendChild(card);
};

/**
 * Requisição para obter os representantes
 */
const solicitarRepresentantes = () => {
  const dados = { acao: "Turmas/listarTurmasLideres" };

  sendRequest(dados)
    .then((response) => {
      if (response.length > 0) {
        document.getElementById("representantes").innerHTML = "";
        response.map((turma) => addTurmaCard(turma));
      } else {
        const representantesDiv = document.getElementById("representantes");
        const msg = document.createElement("div");
        msg.classList.add("nenhum-resultado");
        msg.innerHTML = "Nenhum curso foi encontrado!";

        representantesDiv.append(msg);
      }
    })
    .catch((err) => {
      console.error(err);
    });
};

/**
 * Requisição para obter os conselheiros
 */
const solicitarConselheiros = () => {
  const dados = { acao: "Turmas/listarTurmasConselheiros" };

  sendRequest(dados)
    .then((response) => {
      if (response.length > 0) {
        document.getElementById("conselheiros").innerHTML = "";
        response.map((conselheiro) => addTurmaCard(conselheiro));
      } else {
        const conselheirosDiv = document.getElementById("conselheiros");
        const msg = document.createElement("div");
        msg.classList.add("nenhum-resultado");
        msg.innerHTML = "Nenhum conselheiro foi encontrado!";

        conselheirosDiv.appendChild(msg);
      }
    })
    .catch((err) => {
      console.error(err);
    });
};

solicitarCursos();
solicitarConselheiros();
solicitarRepresentantes();
autocompleteCoordenador();
closeModal();
