let events = () => {
    abrirReuniao();

};

/**
 * Listener
 * Redireciona o usuário para a página da reunião, correpondente ao card clicado
 */
let abrirReuniao = () => {
    const cardsReuniao = document.querySelectorAll('div.cardbox, div.cardbox *');
    cardsReuniao.forEach(function (card) {
        card.addEventListener('click', function (event) {
            let idReuniao = event.target.getAttribute('data-turmaconselho') || event.target.parentElement.getAttribute('data-turmaconselho'); 
            console.log(idReuniao);
            window.location = './reuniao.html?idTurmaConselho=' + idReuniao;
        });

    });
};


events();