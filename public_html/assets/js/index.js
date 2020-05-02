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
 * @param {string} title - Title of the toast
 * @param {string} subtitle - Subtitle of the toast
 * @param {string} content - Text for the toast
 * @param {('success' | 'warning' | 'error' | 'info')} type - Type of the toast
 * @param {number} durationTime - Time that toast will remain on screen
 */
let showMessage = (title, content, type, durationTime) => {
  title = title === undefined ? "" : title;
  subtitle = "Agora";
  content = content === undefined ? "" : content;
  type = type === undefined ? "info" : type;
  durationTime = durationTime === undefined ? 5000 : durationTime;

  const toasts = document.querySelector("#toasts");

  let toast = `<div class="toast ${type}">
    <div class="toast-header">
      <div class="toast-title">${title}</div>
      <div class="toast-close">
        <span class="toast-time">${subtitle}</span>
      </div>
    </div>
    <div class="toast-content">
    ${content}
    <div class="toast-message">
    </div>
    </div>
  </div>`;
  // console.log("> Adicionando o elemento!");

  toasts.innerHTML += toast;

  // console.log("> Selecionando o elemento!");
  // console.log(toasts);
  // console.log("> Selecionando o primeiro elemento!");
  // console.log(toasts.firstChild);

  setTimeout(() => {
    // console.log(toasts.firstElementChild);

    toasts.removeChild(toasts.firstElementChild);
  }, durationTime);
};

/**
 * Função Assincrona para o envio de dados para o servidor
 * @param {JSON} data Objeto JSON com os dados para a requisição
 */
async function sendRequest(data) {
  const base = window.location.origin;
  const url = window.location.pathname.split("/");
  const baseUrl = `${base}/${url[1]}/api.php`;

  const response = await fetch(baseUrl, {
    method: "post",
    body: JSON.stringify(data),
  });
  if (!response.ok)
    throw new Error("Houve um erro durante a execução " + response.status);
  const responseData = await response.json();
  return responseData;
}

events();
