import { sendRequest, getCookie } from "../../assets/js/utils.js";

const requisitarCurso = async () => {
  const token = getCookie("token");

  const dados = { acao: "Coordenadores/obterCurso", token: token };

  const response = await sendRequest(dados);
  if (response.curso) {
    return response.curso;
  }
  return null;
};

const solicitarTurmas = async () => {
  let curso = await requisitarCurso();

  const dados = { acao: "Turmas/listarTurmas" };

  if (curso != null) {
    dados.curso = curso;
  }

  sendRequest(dados)
    .then((response) => {
      if (response.length > 0) {
        document.getElementById("turmas").innerHTML = "";
        response.map((turma) => addTurmaCard(turma));
      } else {
        document.getElementById("turmas").innerHTML = "";
        const reunioesDiv = document.getElementById("turmas");
        const msg = document.createElement("div");
        msg.classList.add("nenhum-resultado");
        msg.innerHTML = "Nenhum outro conselho pode ser iniciado!";

        reunioesDiv.appendChild(msg);
      }
    })
    .catch((err) => {
      console.error(err);
    });
};

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

  card.classList.add("cardbox", "card-turma", classCurso);
  card.setAttribute("data-turma", turma.codigo);

  card.addEventListener("click", (e) => abrirTurma(e));

  card.innerHTML += `
      <p class="subtitulo is-8 gray-text">${turma.curso}</p>
      <p class="subtitulo is-7">${turma.nome}</p>
      <p class="subtitulo is-9 gray-text">${turma.codigo}</p>
    `;

  const turmasDiv = document.getElementById("turmas");

  turmasDiv.appendChild(card);
};

const abrirTurma = (element) => {
  let item = null;

  if (element.currentTarget.classList.contains("cardbox")) {
    item = element.currentTarget;
  }
  if (element.currentTarget.parentNode.classList.contains("cardbox")) {
    item = element.currentTarget.parentNode;
  }
  const turma = item.getAttribute("data-turma");
  localStorage.setItem("turmaAtual", turma);
  window.location.href = "./turma.php";
};

solicitarTurmas();
