import { sendRequest, showMessage, getCookie } from "./utils.js";

const solicitarReunioes = () => {
  const token = getCookie("token");
  const dados = {
    acao: "Professores/listarTurmasReuniao",
    token: token,
  };

  sendRequest(dados)
    .then((response) => {
      console.log(response);
      if (response.length > 0) {
        document.getElementById("reunioes").innerHTML = "";
        response.map((reuniao) => addReuniaoCard(reuniao));
      } else {
        const reunioesDiv = document.getElementById("reunioes");
        const msg = document.createElement("div");
        msg.classList.add("nenhum-resultado");
        msg.innerHTML = "Nenhum conselho de classe em andamento!";

        reunioesDiv.appendChild(msg);
      }
    })
    .catch((err) => {
      console.error(err);
    });
};

const addReuniaoCard = (reuniao) => {
  let card = document.createElement("div");
  let classCurso = "";

  if (reuniao.curso === "Informática para Internet") {
    classCurso = "is-info";
  } else if (reuniao.curso == "Meio Ambiente") {
    classCurso = "is-amb";
  } else {
    classCurso = "is-agro";
  }

  card.classList.add("cardbox", "card-turma", classCurso);
  card.setAttribute("data-turma", reuniao.codigo);
  card.setAttribute("data-turmaconselho", reuniao.reuniao);

  card.innerHTML = `
    <p class="subtitulo is-8 gray-text">${reuniao.curso}</p>
    <p class="subtitulo is-7">${reuniao.nome}</p>
    <p class="subtitulo is-9 gray-text">${reuniao.codigo}</p>
    `;

  card.addEventListener("click", (event) => abrirReuniao(event));

  const reunioesDiv = document.getElementById("reunioes");

  reunioesDiv.appendChild(card);
};

const abrirReuniao = () => {
  let reuniao =
    event.target.getAttribute("data-turmaconselho") ||
    event.target.parentElement.getAttribute("data-turmaconselho");

  let turma =
    event.target.getAttribute("data-turma") ||
    event.target.parentElement.getAttribute("data-turma");

  localStorage.setItem("conselhoAtual", reuniao);
  localStorage.setItem("turmaAtual", turma);

  window.location.href = "./dashboard.html";
};

const solicitarTurmas = () => {
  const professor = localStorage.getItem("professor") || "121415";
  const dados = {
    acao: "Professores/obterTurmasProfessor",
    professor: professor,
  };

  sendRequest(dados)
    .then((response) => {
      if (response.length > 0) {
        document.getElementById("turmas").innerHTML = "";
        response.map((turma) => addTurmaCard(turma));
      } else {
        const turmaDiv = document.getElementById("turmas");
        const msg = document.createElement("div");
        msg.classList.add("nenhum-resultado");
        msg.innerHTML = "Nenhuma turma foi encontrada!";
        turmaDiv.appendChild(msg);
      }
    })
    .catch((err) => {
      console.error(err);
    });
};

const addTurmaCard = (turma) => {
  console.log("> Imprimindo né");
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
  card.addEventListener("click", abrirTurma);

  card.innerHTML += `
    <p class="subtitulo is-8 gray-text">${turma.curso}</p>
    <p class="subtitulo is-7">${turma.nome}</p>
    <p class="subtitulo is-9 gray-text">${turma.codigo}</p>
    `;
  const turmasDiv = document.getElementById("turmas");
  turmasDiv.appendChild(card);
};

const abrirTurma = (params) => {
	console.log("> Abrindo a turma né");
	
	let turma = event.target.getAttribute("data-turma") || event.target.parentElement.getAttribute("data-turma");

	localStorage.setItem("turmaAtual", turma);
	localStorage.removeItem("conselhoAtual");

	window.location.href = "./turma.html";
};

solicitarTurmas();
solicitarReunioes();
