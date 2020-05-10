const listener = () => {
  const btnSalvarDiagnostica = document.getElementById("salvar-diagnostica");
  btnSalvarDiagnostica.addEventListener("click", (e) => salvarDiagnostica(e));
};

/**
 * Listener
 * Muda o estilo do card de avaliação
 */
const concluirCard = (dataAluno) => {
  const card = document.querySelector(
    `.card-avaliacao[data-aluno="${dataAluno}"]`
  );

  card.classList.add("concluido");

  const titulo = card.querySelector("p:first-child");
  titulo.innerHTML = "Concluída";
};

/**
 * Listener
 * Abre o modal para avaliação
 */
const openModal = () => {
  const cardsAvaliacao = document.querySelectorAll(".alunos>div.cardbox");
  const modal = document.querySelector("#avaliacao-diagnostica");

  cardsAvaliacao.forEach((card) => {
    card.addEventListener("click", (event) => {
      let idAluno =
        event.target.getAttribute("data-aluno") ||
        event.target.parentElement.getAttribute("data-aluno");

      if (idAluno) {
        localStorage.setItem("aluno", idAluno);
      }
      modal.classList.toggle("is-active");
      preencherModal(modal, localStorage.getItem("aluno"));
    });
  });
};

//Listener
const closeModal = (params) => {
  const modals = document.querySelectorAll(".modal");
  modals.forEach((modal) => {
    const closeBtn = modal.querySelector(".modal-close-btn");
    closeBtn.addEventListener("click", (event) => {
      fecharAvaliacao(modal);
    });
    const bgModal = modal.querySelector(".modal-background");
    bgModal.addEventListener("click", (event) => {
      fecharAvaliacao(modal);
    });
  });
};

// Listener
const selectPerfil = () => {
  const perfis = document.querySelectorAll(".perfil-aluno > .perfis > .chip");
  perfis.forEach((perfil) => {
    perfil.addEventListener("click", (element) => {
      perfil.classList.toggle("selected");
    });
  });
};

const salvarDiagnostica = () => {
  console.log(localStorage.getItem("aluno"));

  let dados = pegarDados();

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
        concluirCard(localStorage.getItem("aluno"));
        fecharAvaliacao();

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

const pegarDados = () => {
  const perfisSelecionados = document.querySelectorAll(".chip.selected");
  let perfis = [];
  perfisSelecionados.forEach((perfilSelecionado) => {
    perfis.push(perfilSelecionado.getAttribute("data-perfil-id"));
  });

  // TODO: Pegar o ID do Professor que estará logado
  const professor = localStorage.getItem("professor") || 10;
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
  // Atualizar o contador de avaliações restantes
  atualizarAvaliacoesPendentes();

  const selectedChips = modal.querySelectorAll(".chip.selected");

  selectedChips.forEach((chip) => {
    chip.classList.remove("selected");
  });
};

const atualizarAvaliacoesPendentes = () => {
  const cards = document.querySelectorAll(".card-avaliacao");
  const selectedCards = document.querySelectorAll(".card-avaliacao.concluido");
  const qtdAvaliacoes = document.querySelector("#qtdAvaliacoes");
  qtdAvaliacoes.innerHTML = cards.length - selectedCards.length;
};

const concluirAvaliacao = () => {
  const btnSalvar = document.querySelector(".btnSalvar");
  btnSalvar.addEventListener("click", (event) => {
    console.log("O botão foi pressionado!");

    const dados = {
      acao: "Teste/teste",
      nome: "2017103202030090",
      dados: [
        { name: "Alexandre12", age: 21, acao: "Teste/teste" },
        { name: "Alexandre123", age: 21, acao: "Teste/teste" },
        { name: "Alexandre124", age: 21, acao: "Teste/teste" },
        { name: "Alexandre126", age: 21, acao: "Teste/teste" },
      ],
    };

    sendRequest(dados)
      .then((data) => {
        console.log(data);
      })
      .catch((error) => {
        console.log(error);
      });
  });
};

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

// fecharAvaliacao();
atualizarAvaliacoesPendentes();
openModal();
closeModal();
selectPerfil();
concluirAvaliacao();
listener();
