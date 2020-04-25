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
      modal.classList.toggle("is-active");

      if (idAluno) {
        localStorage.setItem("avaliacaoAtual", idAluno);
      }
    });
  });

  // Atualizar o contador de avaliações restantes
  atualizarAvaliacoesPendentes();
};

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

  const selectedChips = modal.querySelectorAll(".chip.selected");

  console.log(selectedChips);
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

    // const url = "https://randomuser.me/api/?results="; // Get 10 random users
    //   fetch(url)
    //     .then((resp) => {
    //       console.log(resp.status);
    //       return resp.json();
    //     }) // Transform the data into json
    //     .then(function (data) {
    //       console.log(data);
    //     });

    //   const url = 'https://randomuser.me/api';
    //   // The data we are going to send in our request
    //   let data = {
    //       name: 'Sara',
    //   }
    //   // Create our request constructor with all the parameters we need
    //   var request = new Request(url, {
    //       method: 'POST',
    //       body: JSON.stringify(data),
    //       headers: new Headers()
    //   });

    //   fetch(request)
    //   .then(function(resp) {
    //       // Handle response we get from the API
    //       console.log(resp);
    //   })
  });
};

// fecharAvaliacao();
atualizarAvaliacoesPendentes();
openModal();
closeModal();
selectPerfil();
salvarAvaliacao();
concluirAvaliacao();
