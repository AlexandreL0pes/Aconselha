let eventos = () => {
  abrirReuniao();
  selecionarTurmas();
};

/**
 * Listener
 * Redireciona o usuário para a página da reunião, correpondente ao card clicado
 */
let abrirReuniao = () => {
  const cardsReuniao = document.querySelectorAll(
    ".reunioes>div.cardbox, .reunioes>div.cardbox *"
  );
  cardsReuniao.forEach(function (card) {
    card.addEventListener("click", function (event) {
      let idReuniao =
        event.target.getAttribute("data-turmaconselho") ||
        event.target.parentElement.getAttribute("data-turmaconselho");
      console.log(idReuniao);
      window.location = "./reuniao.html?idTurmaConselho=" + idReuniao;
    });
  });
};

/**
 * Listener
 * Seleciona as turmas para iniciar o conselho
 */
let selecionarTurmas = () => {
    const cardsTurmas = document.querySelectorAll(
        ".turmas>div.cardbox"
    );
    cardsTurmas.forEach(function (card) {
        card.addEventListener('click', function (event) {
            let item = null;
            
            if (event.currentTarget.classList.contains('cardbox')) {
                item = event.currentTarget;                
            }
            if (event.currentTarget.parentNode.classList.contains('cardbox')) {
                item = event.currentTarget.parentNode;
            }                        
            item.classList.toggle('selected');
            habilitarBotao();
        });
    });
};

/**
 * Function
 * Habilitar botão quando existem turmas selecionadas
 */
let habilitarBotao = () => {
    const botao = document.querySelector('#iniciarConselho');
    const turmasSelecionadas = document.querySelectorAll('.turmas>div.cardbox.selected');
    const qtdTurmas = document.querySelector('#qtdTurmas');
    
    if (turmasSelecionadas.length > 0) {
        botao.disabled = false;
        qtdTurmas.innerHTML = turmasSelecionadas.length;
    }else{
        botao.disabled = true;
        qtdTurmas.innerHTML = 'Nenhuma';
    }
};


eventos();
