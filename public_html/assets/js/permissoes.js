import { sendRequest, showMessage } from "./utils.js";

// const addCoordenador = document.getElementById("add-coordenador");
// addCoordenador.addEventListener("click", (e) => abrirNovoCoordenador());

// const abrirNovoCoordenador = (e) => {
//   const modalCoordenador = document.getElementById("modal-coordenador");
//   modalCoordenador.classList.toggle("is-active");
// };

const btnSalvarCoordenador = document.querySelector(".salvar-coordenador");
btnSalvarCoordenador.addEventListener("click", (e) => salvarCoordenador(e));

const abrirCoordenador = (element) => {
  let curso = element.currentTarget.getAttribute("data-curso");
  console.log("Abrindo Coordenador!");

  const modalCoordenador = document.getElementById("modal-coordenador");
  modalCoordenador.classList.toggle("is-active");
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
      modal.classList.toggle("is-active");
    });
    const bgModal = modal.querySelector(".modal-background");
    bgModal.addEventListener("click", (evnt) => {
      modal.classList.toggle("is-active");
    });
  });
};

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
  };

  bulmahead("coordenador", "coordenador-menu", api, onSelect, 200);
};

const salvarCoordenador = (e) => {
  console.log("> Salvando Coordenador");

  let dados = pegarDados();

  console.log(dados);

  if (dados.acao !== "Coordenadores/alterarSenha" && dados.matricula == null) {
    showMessage(
      "Confira seus dados!",
      "É necessário informar o novo coordenador.",
      "warning",
      4000
    );
    return false;
  }

  if (dados.curso != "") {
    sendRequest(dados)
      .then((response) => {
        console.log(response);
        showMessage(
          "Deu certo!",
          "O coordenador foi alterado com sucesso!",
          "success",
          4000
        );
      })
      .catch((err) => {
        console.error(err);
        showMessage(
          "Houve um erro",
          "Não foi possível salvar o coordenador, verifique os dados.",
          "warning",
          400
        );
      });
  } else {
    showMessage(
      "Confira seus dados!",
      "Existe algum erro no formulário!",
      "warning",
      4000
    );
  }
};

const pegarDados = () => {
  const coordenadorNovo = document
    .getElementById("coordenador")
    .getAttribute("data-coordenador");
  const senha = document.getElementById("senha-coordenador").value;
  const curso = document
    .getElementById("modal-coordenador")
    .getAttribute("data-curso");

  const coordenadorAtual =
    document
      .getElementById("modal-coordenador")
      .getAttribute("data-coordenador") || "";

  let dados = {
    curso: curso,
    senha: senha,
  };

  // Caso de trocar a senha
  dados.acao = "Coordenadores/alterarSenha";

  // Caso não exista coordenador atual
  if (coordenadorAtual === "") {
    dados = {
      curso: curso,
      matricula: coordenadorNovo,
      senha: senha,
    };
    dados.acao = "Coordenadores/adicionar";
  }

  // Caso o novo id seja diferente do antigo
  if (coordenadorNovo !== coordenadorAtual && coordenadorAtual !== "") {
    dados = {
      curso: curso,
      matricula: coordenadorNovo,
      senha: senha,
    };
    dados.acao = "Coordenadores/atualizarCoordenador";
  }
  return dados;
};

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
  let codigoCoordenador = curso.coordenador.codigo || "";

  card.setAttribute("data-coordenador", codigoCoordenador);

  card.innerHTML += `
    <p class="gray-text">${curso.nome}</p>
    <p class="">${texto}</p>
  `;

  card.addEventListener("click", (e) => abrirCoordenador(e));

  const coordenadoresDiv = document.getElementById("coordenadores");
  coordenadoresDiv.appendChild(card);
};

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

solicitarCursos();
autocompleteCoordenador();
closeModal();
