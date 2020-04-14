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
 * @param {string} title 
 * @param {string} subtitle 
 * @param {string} content 
 * @param {string} type 
 * @param {number} durationTime 
 */
let showMessage = (title, subtitle, content, type, durationTime) => {
  title = title === "undefined" ? "" : title;
  subtitle = subtitle === "undefined" ? "Agora" : subtitle;
  content = content === "undefined" ? "" : content;
  type = type === "undefined" ? "info" : type;
  durationTime =
    durationTime === "undefined" ? 4000 : parseInt(durationTime);

  const toasts = document.querySelector("#toasts");

  let toast = `<div class="toast ${type}">
    <div class="toast-header">
      <div class="toast-title">${title}</div>
      <div class="toast-close">
        <span class="toast-time">${subtitle}</span>
      </div>
    </div>
    <div class="toast-content">
    ${content + " " + Math.round(Math.random() * 100)}
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
  }, durationTime);
};
events();
