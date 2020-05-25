import { getCookie, sendRequest } from "../utils.js";

const autenticarCoordenador = () => {
  const token = getToken();
  console.log(token);

  console.log(token);

  let dados = {
    acao: "Login/verificarCoordenador",
    token: token,
  };

  // Caso sucesso, usuário logado
  sendRequest(dados)
    .then((result) => console.log(result))
    .catch((err) => {
      //   console.error(err);
      /* Redireciona para a página anterior */
      window.history.back();
    });
};

const autenticarRepresentante = () => {
  const token = getToken();

  let dados = {
    acao: "Login/verificarRepresentante",
    token: token,
  };

  sendRequest(dados).catch((err) => window.history.back());
};



const getToken = () => {
  const token = getCookie("token") || null;

  return token;
};

export { autenticarCoordenador, autenticarRepresentante };
