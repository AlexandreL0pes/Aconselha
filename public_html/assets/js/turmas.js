import { sendRequest, showMessage, getCookie } from "./utils.js";

const solicitarReunioes = () => {
  const professor = localStorage.getItem("professor") || "121415";

  const dados = {
    acao: "Professores/listarTurmasReuniao",
    professor: professor,
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
  console.log("> Imprimindo né");

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
solicitarReunioes();
