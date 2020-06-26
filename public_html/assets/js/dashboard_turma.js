import { sendRequest } from "./utils.js";

const listener = () => {
  const btnFiltrarAlto = document.getElementById("filtrarAlto");
  btnFiltrarAlto.addEventListener("click", filtrarAlto);

  const btnFiltrarMedio = document.getElementById("filtrarMedio");
  btnFiltrarMedio.addEventListener("click", filtrarMedio);

  const btnFiltrarBaixo = document.getElementById("filtrarBaixo");
  btnFiltrarBaixo.addEventListener("click", filtrarBaixo);

  const btnRemoverFiltro = document.getElementById("removerFiltro");
  btnRemoverFiltro.addEventListener("click", removerFiltro);
};

const obterEstatisticaTurma = () => {
  const turma = localStorage.getItem("turmaAtual");
  if (turma !== null) {
    const dados = {
      acao: "Turmas/obterEstatistica",
      turma: turma,
    };

    sendRequest(dados)
      .then((response) => {
        apresentarEstatisticasTurma(response);
      })
      .catch((err) => {
        console.error(err);
      });
  }
};

const apresentarEstatisticasTurma = (estatistica) => {
  const estatisticaTurma = document.getElementById("estatistica-turma");

  estatisticaTurma.querySelector(".coef-geral .resultado").innerHTML =
    estatistica.coeficiente_geral;
  estatisticaTurma.querySelector(".experiencia .resultado").innerHTML =
    estatistica.aprendizados;
  estatisticaTurma.querySelector(".aprendizado .resultado").innerHTML =
    estatistica.experiencias;
  estatisticaTurma.querySelector(".medidas .resultado").innerHTML =
    estatistica.medidas_disciplinares;
};

const obterPrincipaisAvaliacoes = () => {
  const turma = localStorage.getItem("turmaAtual");
  if (turma !== null) {
    const dados = { acao: "Perfis/listarPerfisRelevantes", turma: turma };

    sendRequest(dados)
      .then((response) => {
        apresentarPrincipaisAvaliacoes(response);
      })
      .catch((err) => {
        console.error(err);
      });
  }
};

const apresentarPrincipaisAvaliacoes = (dados) => {
  console.log(dados[0]);
  if (dados[0].nome !== undefined) {
    const avaliacoes = document.getElementById("avaliacoes");
    dados.map((perfil) => {
      avaliacoes.appendChild(gerarChip(perfil));
    });
  } else {
    const avaliacoes = document.getElementById("avaliacoes");
    avaliacoes.innerHTML = "Nenhuma avaliação foi encontrada";
  }
};

const gerarChip = (perfil) => {
  const chip = document.createElement("span");
  chip.classList.add("chip");

  if (perfil.tipo === "1") {
    chip.classList.add("positivo");
  } else {
    chip.classList.add("negativo");
  }

  chip.innerText = perfil.nome;

  return chip;
};

obterEstatisticaTurma();
obterPrincipaisAvaliacoes();
