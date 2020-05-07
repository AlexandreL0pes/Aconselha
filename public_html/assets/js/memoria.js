const btnSalvarMemoria = document.getElementById("salvar-memoria");
btnSalvarMemoria.addEventListener("click", (e) => salvarMemoria(e));

const autoSizeTextarea = () => {
  const textarea = document.querySelector("#memoriaReuniao");

  textarea.addEventListener("keydown", (e) => {
    let element = e.currentTarget;
    // element.style.height = "5px";
    element.style.height = element.scrollHeight + "px";
  });
};

const selecionarMemoria = () => {
  const reuniao = localStorage.getItem("conselhoAtual") || "";
  const dados = {
    acao: "Reunioes/selecionarMemoria",
    reuniao: reuniao,
  };

  sendRequest(dados)
    .then((response) => {
      preencherMemoria(response.memoria);
    })
    .catch((err) => {
      console.log(err);
    });
};

const preencherMemoria = (memoria) => {
  const textareaMemoria = document.querySelector("#memoriaReuniao");
  textareaMemoria.value = memoria;
};

const salvarMemoria = (params) => {
  const reuniao = localStorage.getItem("conselhoAtual") || "";
  const memoria = document.querySelector("#memoriaReuniao").value;
  if (memoria !== "" && reuniao !== "") {
    const dados = {
      acao: "Reunioes/salvarMemoria",
      reuniao: reuniao,
      memoria: memoria,
    };
    console.log(dados);
    sendRequest(dados)
      .then((response) => {
        showMessage(
          "Deu certo!",
          "A memoria da reunião foi salva com sucesso.",
          "success",
          4000
        );
      })
      .catch((err) => {
        console.error(err);
        showMessage(
          "Ops, houve um erro!",
          "Não foi possível salvar a memória, tente novamente.",
          "error",
          5000
        );
      });
  } else {
    showMessage(
      "Quase lá!",
      "Antes de enviar, preencha os campos necessários!",
      "warning",
      5000
    );
  }
};
autoSizeTextarea();
selecionarMemoria();
