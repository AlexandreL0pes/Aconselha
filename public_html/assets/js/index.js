let events = () => {
  resposiveNavbar();
  activeDropdown();
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

let activeDropdown = () => {
  const dropdown = document.querySelector("#dropdown-user");
  dropdown.addEventListener("click", function (event) {
    event.currentTarget.classList.toggle("is-active");
  });
};

/**
 * Exibe e apaga a mensagem na tela
 * @param {*} config
 */
let showMessage = (config) => {
  config.title = config.title === "undefined" ? "" : config.title;
  config.subtitle = config.subtitle === "undefined" ? "Agora" : config.subtitle;
  config.content = config.content === "undefined" ? "" : config.content;
  config.type = config.type === "undefined" ? "info" : config.type;
  config.durationTime =
    config.durationTime === "undefined" ? 4000 : parseInt(config.durationTime);

  const toasts = document.querySelector("#toasts");

  let toast = `<div class="toast ${config.type}">
    <div class="toast-header">
      <div class="toast-title">${config.title}</div>
      <div class="toast-close">
        <span class="toast-time">${config.subtitle}</span>
      </div>
    </div>
    <div class="toast-content">
    ${config.content + " " + Math.round(Math.random() * 100)}
    <div class="toast-message">
    </div>
    </div>
  </div>`;
  console.log("> Adicionando o elemento!");

  toasts.innerHTML += toast;

  console.log("> Selecionando o elemento!");
  console.log(toasts);
  console.log("> Selecionando o primeiro elemento!");
  console.log(toasts.firstChild);

  setTimeout(() => {
    console.log(toasts.firstElementChild);
    
    toasts.removeChild(toasts.firstElementChild);
  }, config.durationTime);
};
events();
