import { sendRequest, showMessage, setCookie, getCookie } from "./utils.js";

const listeners = () => {
  const btnLogar = document.getElementById("btnLogar");
  btnLogar.addEventListener("click", login);
  verificarLogin();
};

const login = (params) => {
  const dados = pegarDados();

  if (dados.login != "" && dados.senha != "") {
    sendRequest(dados)
      .then((response) => {
        setCookie("token", response.jwt, response.expireAt);
        redirecionamento(response.type);
      })
      .catch((err) => {
        showMessage(
          "Houve um erro!",
          "Não foi possível realizar o login",
          "error",
          5000
        );
        console.error(err);
      });
  } else {
    showMessage(
      "Quase lá!",
      "Alguns campos não foram preenchidos!",
      "warning",
      5000
    );
  }
};

const pegarDados = () => {
  const usuario = document.getElementById("usuario").value || "";
  const senha = document.getElementById("senha").value || "";
  const lembrar = true;

  const dados = {
    acao: "Login/login",
    login: usuario,
    senha: senha,
    lembrar: lembrar,
  };

  return dados;
};

/**
 * Redirecionamento de páginas conforme o usuário logado
 * @param {string} type Tipo de usuario retornado pelo login
 */
const redirecionamento = (type) => {
  const pages = {
    1: "./gerencia/reunioes.html",
    2: "./coordenador/reunioes.html",
    3: "./professor/turmas.html",
    4: "./professor/turmas.html",
    5: "./representante/ensino.html",
    6: "./representante/ensino.html",
  };
  window.location.href = pages[type];
};

const verificarLogin = () => {
  const token = getCookie("token");
  if (token !== "") {
    console.log(">> Token");
    console.log(token);

    let dados = {
      acao: "Login/verificarLogin",
      token: token,
    };

    sendRequest(dados)
      .then((response) => {
        redirecionamento(response.type);
      })
      .catch((err) => {
        console.error(err);
      });
  }
};
listeners();
