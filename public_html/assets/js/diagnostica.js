import { sendRequest, showMessage, getCookie } from "./utils.js";

const listener = () => {
  const btnSalvarDiagnostica = document.getElementById("salvar-diagnostica");
  btnSalvarDiagnostica.addEventListener("click", (e) => salvarDiagnostica(e));

  const addDiagnostica = document.querySelectorAll(".alunos>div.cardbox");
  addDiagnostica.forEach((card) =>
    card.addEventListener("click", abrirNovaDiagnostica)
  );

  const perfis = document.querySelectorAll(".perfil-aluno > .perfis > .chip");
  perfis.forEach((perfil) =>
    perfil.addEventListener("click", (e) => selecionarPerfil(e))
  );

  const modals = document.querySelectorAll(".modal");
  modals.forEach((modal) => {
    const closeBtn = modal.querySelector(".modal-close-btn");
    closeBtn.addEventListener("click", (event) => fecharAvaliacao());

    const bgModal = modal.querySelector(".modal-background");
    bgModal.addEventListener("click", (event) => fecharAvaliacao());
  });
};

/**
 * Abre um nova avaliação diagnóstica
 * @param {*} element Elemento clicado
 */
const abrirNovaDiagnostica = (element) => {
  const modal = document.getElementById("avaliacao-diagnostica");
  let idAluno =
    element.target.getAttribute("data-aluno") ||
    element.target.parentElement.getAttribute("data-aluno");

  if (idAluno) {
    localStorage.setItem("aluno", idAluno);
  }
  modal.classList.toggle("is-active");
  preencherModal(modal, localStorage.getItem("aluno"));
};

/**
 * Requisita os dados da avaliação no banco e preenche os perfis
 * @param {*} element
 */
const abrirDiagnostica = (element) => {
  const modal = document.getElementById("avaliacao-diagnostica");

  const diagnostica = element.currentTarget.getAttribute("data-diagnostica");

  let aluno = event.currentTarget.getAttribute("data-aluno") || "";

  if (aluno) {
    localStorage.setItem("aluno", aluno);
  }

  if (diagnostica) {
    localStorage.setItem("diagnostica", diagnostica);
    modal.classList.toggle("is-active");

    const dados = {
      acao: "Diagnosticas/selecionarDiagnostica",
      diagnostica: diagnostica,
    };

    sendRequest(dados)
      .then((response) => {
        console.log(response.perfis);
        preencherPerfis(response.perfis);
      })
      .catch((err) => {
        console.error(err);
      });
  } else {
    showMessage(
      "Houve um erro!",
      "Não foi possível abrir a avaliação.",
      "warning",
      5000
    );
  }
};

/**
 * Adiciona a classe selected nos chips de acordo com os perfis informados
 * @param {JSON} perfis Perfis de uma avaliação diagnóstica
 */
const preencherPerfis = (perfis) => {
  const perfisChips = document.querySelectorAll("div.perfis > div.chip");
  perfisChips.forEach((perfilChip) => {
    const perfilId = perfilChip.getAttribute("data-perfil-id");
    const resultado = perfis.find((el) => el.id == perfilId);
    if (resultado) {
      perfilChip.classList.toggle("selected");
    }
  });
};

/**
 * Adiciona a classe selected ao chip
 * @param {Element} perfil Chip clicado
 */
const selecionarPerfil = (perfil) => {
  perfil.currentTarget.classList.toggle("selected");
};

/**
 * Dispara um requisição para salvar/alterar a avaliação diagnóstica
 */
const salvarDiagnostica = () => {
  let dados = pegarDados();
  console.log(dados);
  if (
    dados.perfis.length > 0 &&
    dados.reuniao !== "" &&
    dados.professor !== "" &&
    dados.estudante !== ""
  ) {
    console.log(dados);

    sendRequest(dados)
      .then((response) => {
        console.log(response);
        concluirCard(localStorage.getItem("aluno"), response.diagnostica);
        fecharAvaliacao();
        console.log(response.diagnostica);
        showMessage(
          "Deu certo!",
          "A avaliação diagnóstica foi salva!",
          "success"
        );
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
      "Pode existir algum erro nos dados informados!",
      "warning",
      5000
    );
  }
};

/**
 * Altera o estilo do card de avaliação para concluído
 */
const concluirCard = (dataAluno, diagnostica) => {
  const card = document.querySelector(
    `.card-avaliacao[data-aluno="${dataAluno}"]`
  );

  card.removeEventListener("click", abrirNovaDiagnostica);
  card.addEventListener("click", abrirDiagnostica);
  card.classList.add("concluido");
  card.setAttribute("data-diagnostica", diagnostica);
  const titulo = card.querySelector("p:first-child");
  titulo.innerHTML = "Concluída";
};

/**
 * Obtem os dados necessários para efetuar o cadastro/alteração
 */
const pegarDados = () => {
  const perfisSelecionados = document.querySelectorAll(".chip.selected");
  let perfis = [];
  perfisSelecionados.forEach((perfilSelecionado) => {
    perfis.push(perfilSelecionado.getAttribute("data-perfil-id"));
  });

  // TODO: Pegar o ID do Professor que estará logado
  const professor = localStorage.getItem("professor") || 26873;
  const estudante = localStorage.getItem("aluno") || "";
  const diagnostica = localStorage.getItem("diagnostica") || "";
  const reuniao = localStorage.getItem("conselhoAtual");

  let dados = {
    acao: "Diagnosticas/cadastrar",
    reuniao: reuniao,
    estudante: estudante,
    professor: professor,
    perfis: perfis,
  };

  if (diagnostica !== "") {
    (dados.acao = "Diagnosticas/alterar"), (dados.diagnostica = diagnostica);
  }

  return dados;
};

/**
 * Esconde o modal, remove o aluno do modal atual e apaga os perfis selecionados
 * @param {DOM Element} modal Modal de Avaliação Diagnóstica
 */
const fecharAvaliacao = () => {
  const modal = document.getElementById("avaliacao-diagnostica");

  modal.classList.toggle("is-active");
  if (localStorage.getItem("aluno")) {
    localStorage.removeItem("aluno");
  }

  if (localStorage.getItem("diagnostica")) {
    localStorage.removeItem("diagnostica");
  }
  // Atualizar o contador de avaliações restantes
  atualizarAvaliacoesPendentes();

  const selectedChips = modal.querySelectorAll(".chip.selected");

  selectedChips.forEach((chip) => {
    chip.classList.remove("selected");
  });
};

/**
 * Atualiza o contador de avaliações pendentes
 */
const atualizarAvaliacoesPendentes = () => {
  const cards = document.querySelectorAll(".card-avaliacao");
  const selectedCards = document.querySelectorAll(".card-avaliacao.concluido");
  const qtdAvaliacoes = document.querySelector("#qtdAvaliacoes");
  qtdAvaliacoes.innerHTML = cards.length - selectedCards.length;
};

/**
 * Preenche a identificação da avaliação diagnóstica com base no card de avaliação
 * @param {*} modal Id do modal em que os dados serão colocados
 * @param {*} idAluno Identificação do aluno clicado
 */
const preencherModal = (modal, idAluno) => {
  const cardAluno = document.querySelector(
    `.card-avaliacao[data-aluno="${idAluno}"]`
  );

  const aluno = {
    nome: cardAluno.querySelector('p[name="nome"]').innerHTML,
    curso: document.querySelector('span > span[name="curso"]').innerHTML,
    matricula: cardAluno.querySelector('p[name="matricula"]').innerHTML,
  };

  modal.querySelector('.info > p[name="nome"]').innerHTML = aluno.nome;
  modal.querySelector('.info > p[name="curso"]').innerHTML = aluno.curso;
  modal.querySelector('.info > p[name="matricula"]').innerHTML =
    aluno.matricula;
};

/**
 * Requisita as avaliações diagnósticas realizadas
 */
const solicitarDiagnosticas = () => {
  const reuniao = localStorage.getItem("conselhoAtual") || "";
  const token = getCookie("token");
  console.log("> Token aqui olha só");
  console.log(token);
  const dados = {
    acao: "Diagnosticas/listarDiagnosticasMatriculaReuniao",
    reuniao: reuniao,
    token: token
  };

  sendRequest(dados)
    .then((response) => {
      if (response.length > 0) {
        marcarDiagnosticas(response);
      }else {
        atualizarAvaliacoesPendentes();
      }
    })
    .catch((err) => {
      console.error(err);
      showMessage(
        "Houve um erro!",
        "Não foi possível obter as avaliações efetuadas!",
        "error",
        5000
      );
    });
};

/**
 * Percorre todos os cards listados e marca como concluido os tem a matricula retornada
 * @param {JSON} dados Lista de objetos com as matriculas e id das avaliações diagnósticas
 */
const marcarDiagnosticas = (dados) => {
  const avaliacoesCard = document.querySelectorAll(".card-avaliacao");

  avaliacoesCard.forEach((avaliacaoCard) => {
    const matriculaCard = avaliacaoCard.getAttribute("data-aluno");
    const resultado = dados.find((el) => el.matricula == matriculaCard);
    if (resultado) {
      console.log(">> Resultado Busca" + resultado.matricula);
      concluirCard(resultado.matricula, resultado.diagnostica);
    }
  });
};

/**
 * Requisita os perfis cadastrados no banco de dados
 */
const listarPerfis = () => {
  sendRequest({ acao: "Perfis/listarPerfis" })
    .then((response) => {
      adicionarPerfis(response);
    })
    .catch((err) => {
      console.error(err);
    });
};

/**
 * Cria os chips de perfil dentro do modal diagnóstica
 * @param {JSON} perfis
 */
const adicionarPerfis = (perfis) => {
  const opcoesPerfis = document.getElementById("opcoes-perfis");

  perfis.forEach((perfil) => {
    let perfilChip = document.createElement("div");
    perfilChip.classList.add("chip");
    perfilChip.setAttribute("data-perfil-id", perfil.id);
    perfilChip.appendChild(document.createTextNode(perfil.nome));
    perfilChip.addEventListener("click", selecionarPerfil);
    opcoesPerfis.appendChild(perfilChip);
  });
};

const obterInformacoesTurma = () => {
  const turma = localStorage.getItem("turmaAtual") || null;

  if (turma !== null) {
    const dados = { acao: "Turmas/informacoesTurma", turma: turma };

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
};

const solicitarEstudantes = () => {
  const turma = localStorage.getItem("turmaAtual");

  if (turma !== null) {
    const dados = { acao: "Turmas/listarEstudantes", turma: turma };

    sendRequest(dados)
      .then((response) => {
        console.log(response);
        response.map((estudante) => addEstudanteCard(estudante));
        solicitarDiagnosticas();
      })
      .catch((err) => {
        console.error(err);
      });
  }
};

/* 

<div class="cardbox card-avaliacao" data-aluno="2017103202030090">
  <p class="subtitulo is-8" name="">Pendente</p>
  <p class="subtitulo is-7" name="nome">Alexandre Lopes</p>
  <p class="subtitulo is-8" name="matricula">2017103202030090</p>
</div>

*/
const addEstudanteCard = (estudante) => {
  let card = document.createElement("div");
  card.classList.add("cardbox", "card-avaliacao");
  card.setAttribute("data-aluno", estudante.matricula);

  card.innerHTML += `
    <p class="subtitulo is-8" name="">Pendente</p>
    <p class="subtitulo is-7" name="nome">${estudante.nome}</p>
    <p class="subtitulo is-8" name="matricula">${estudante.matricula}</p>
    `;

  card.addEventListener("click", abrirNovaDiagnostica);

  const alunosDiv = document.getElementById("alunos");

  alunosDiv.appendChild(card);
};
solicitarEstudantes();
obterInformacoesTurma();
listarPerfis();
atualizarAvaliacoesPendentes();
listener();
