let events = () => {
  abrirReuniao();
  resposiveNavbar();
};

/**
 * Listener
 * Redireciona o usuário para a página da reunião, correpondente ao card clicado
 */
let abrirReuniao = () => {
  const cardsReuniao = document.querySelectorAll("div.cardbox, div.cardbox *");
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

let resposiveNavbar = () => {
  const menuIconEl = document.querySelector(".menu-icon");
  const sidenavEl = document.querySelector(".sidenav-mobile");
  const sidenavCloseEl = document.querySelector(".sidenav__close-icon-mobile");

  function toggleClassName(el, className) {
    el.classList.toggle(className);
  }

  menuIconEl.addEventListener("click", function () {
    toggleClassName(sidenavEl, "active");
  });

  sidenavCloseEl.addEventListener("click", function () {
    toggleClassName(sidenavEl, "active");
  });
};
events();
