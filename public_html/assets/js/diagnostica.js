/**
 * Listener
 * Muda o estilo do card de avaliação
 */

const changeStyleCard = (dataAluno) => {
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
        localStorage.setItem("avaliacaoAtual", idAluno);
      }
      modal.classList.toggle("is-active");
      preencherModal(modal, localStorage.getItem("avaliacaoAtual"));
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

const salvarAvaliacao = () => {
  const modals = document.querySelectorAll(".modal");

  modals.forEach((modal) => {
    const btnsalvarAvaliacao = modal.querySelector(
      ".modal-card-foot > .save-avaliation"
    );
    btnsalvarAvaliacao.addEventListener("click", (params) => {
      console.log(localStorage.getItem("avaliacaoAtual"));

      changeStyleCard(localStorage.getItem("avaliacaoAtual"));
      fecharAvaliacao(modal);

      showMessage(
        "Deu certo!",
        "A avaliação diagnóstica foi salva!",
        "success"
      );
    });
  });
};

/**
 * Esconde o modal, remove o aluno do modal atual e apaga os perfis selecionados
 * @param {DOM Element} modal Modal de Avaliação Diagnóstica
 */
const fecharAvaliacao = (modal) => {
  modal.classList.toggle("is-active");
  if (localStorage.getItem("avaliacaoAtual")) {
    localStorage.removeItem("avaliacaoAtual");
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

    const base = window.location.origin;
    const url = window.location.pathname.split("/");
    const baseUrl = `${base}/${url[1]}/api.php`;

    console.log(baseUrl);


    const dados = {
      acao: "Teste/teste",
      dados: [
        { name: "Alexandre12", age: 21, acao: "Teste/teste" },
        { name: "Alexandre123", age: 21, acao: "Teste/teste" },
        { name: "Alexandre124", age: 21, acao: "Teste/teste" },
        { name: "Alexandre126", age: 21, acao: "Teste/teste" },
      ]
    };

    fetch(baseUrl, {
      method: "POST",
      body: JSON.stringify(dados),
    })
      .then((response) => {
        if (!response.ok)
          throw new Error(
            "Houve um erro durante a execução: " + response.status
          );
        return response.text();
      })
      .then((response) => {
        console.log(response);
      })
      .catch((error) => {
        console.error(error);
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
salvarAvaliacao();
concluirAvaliacao();
