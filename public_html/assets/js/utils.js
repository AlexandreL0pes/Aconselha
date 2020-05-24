/**
 * Exibe e apaga a mensagem na tela
 * @param {string} title - Title of the toast
 * @param {string} content - Text for the toast
 * @param {('success' | 'warning' | 'error' | 'info')} type - Type of the toast
 * @param {number} durationTime - Time that toast will remain on screen
 */
let showMessage = (title, content, type, durationTime) => {
  title = title === undefined ? "" : title;
  const subtitle = "Agora";
  content = content === undefined ? "" : content;
  type = type === undefined ? "info" : type;
  durationTime = durationTime === undefined ? 5000 : durationTime;

  const toasts = document.querySelector("#toasts");
  if (toasts === null)
    throw new Error(
      "Não foi encontrado a localização para exibir as mensagens"
    );

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

  toasts.innerHTML += toast;

  setTimeout(() => {
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
  // const baseUrl = `${base}/${url[1]}/api.php`;
  const baseUrl = `http://localhost/public_html/api.php`;

  if (data.acao === undefined)
    throw new Error("A acao é necessária para efetuar a requisição!");

  const response = await fetch(baseUrl, {
    // credentials: "same-origin",
    method: "post",
    body: JSON.stringify(data),
  });
  if (!response.ok)
    throw new Error("Houve um erro durante a execução " + response.status);
  const responseData = await response.json();
  return responseData;
}

/**
 * Salva um cookie dentro do navegador
 * @param {string} name Cookie Name
 * @param {string} value Cookie Value
 * @param {number} expire time for expire
 */
const setCookie = (name, value, expire) => {
  let date = new Date();
  date.setTime(date.getTime + expire);
  let expires = "expires=" + date.toUTCString();
  document.cookie = name + "=" + value + ";" + expires + ";path=/; SameSite=Strict";
};

/**
 * Obtem o valor do cookie especificado
 * @param {string} cname Cookie name
 */
const getCookie = (cname) => {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(";");

  for (let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == " ") {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
};

export { sendRequest, showMessage, setCookie, getCookie};
