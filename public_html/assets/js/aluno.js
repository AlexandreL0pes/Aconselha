import { sendRequest, getSearchParams } from "./utils.js";

const listener = () => {};

const obterInfoAluno = () => {
  const matricula = getMatricula();
  if (matricula) {
    const dados = {
      acao: "Alunos/obterInformacoes",
      aluno: matricula,
    };

    sendRequest(dados)
      .then((response) => {
        apresentarInfoAluno(response);
      })
      .catch((err) => {
        console.error(err);
      });
  }
};

const apresentarInfoAluno = (info) => {
  const divAluno = document.querySelector(".info .aluno");
  divAluno.querySelector(".nome").innerHTML = info.aluno.nome;

  divAluno.querySelector(".turma").innerHTML = info.turma.nome;
  divAluno.querySelector(".curso").innerHTML = info.turma.curso;
  divAluno.querySelector(".matricula").innerHTML = info.aluno.matricula;
};

const obterEstatisticaAluno = () => {
  const matricula = getMatricula();

  if (matricula) {
    const dados = {
      acao: "Alunos/obterEstatisticas",
      aluno: matricula,
    };

    sendRequest(dados)
      .then((response) => {
        apresentarEstatisticas(response);
      })
      .catch((err) => {
        console.error(err);
      });
  }
};

const apresentarEstatisticas = (estatisticas) => {
  const divEstatisticas = document.querySelector(".estatistica-aluno");
  divEstatisticas.querySelector(".coef-geral .resultado").innerHTML =
    estatisticas.coeficiente_geral;
  divEstatisticas.querySelector(".aprendizado .resultado").innerHTML =
    estatisticas.aprendizados;
  divEstatisticas.querySelector(".medidas .resultado").innerHTML =
    estatisticas.medidas;
};

const obterPrincipaisAvaliacoes = () => {
  const matricula = getMatricula();
  const dados = {
    acao: "Perfis/listarPerfisRelevantesMatricula",
    aluno: matricula,
  };

  sendRequest(dados)
    .then((response) => {
      apresentarPrincipaisAvaliacoes(response);
    })
    .catch((err) => {
      console.error(err);
    });
};

const apresentarPrincipaisAvaliacoes = (dados) => {
  if (dados[0].nome !== undefined) {
    const avaliacoes = document.getElementById("avaliacoes");
    dados.map((perfil) => avaliacoes.appendChild(gerarChip(perfil)));
  } else {
    const avaliacoes = document.getElementById("avaliacoes");
    avaliacoes.innerHTML = "Nenhuma avaliação foi encontrada.";
  }
};

const gerarChip = (perfil) => {
  const chip = document.createElement("span");
  chip.classList.add("chip");

  if (perfil.tipo === "1") {
    chip.classList.add("positivo");
  } else {
    chip.classList.add("negativo");
  }

  chip.innerText = perfil.nome;

  return chip;
};
const getMatricula = () => {
  const aluno = getSearchParams();

  let matricula;
  aluno.map((item) => {
    if ("key" in item && item.key === "matricula") {
      console.log(item);
      matricula = item.value;
    }
  });

  return matricula;
};

const obterMedidasDisciplinares = () => {
  const matricula = getMatricula();

  if (matricula) {
    const dados = {
      acao: "Alunos/obterMedidasDisciplinares",
      aluno: matricula,
    };

    sendRequest(dados)
      .then((response) => {
        apresentarMedidas(response);
      })
      .catch((err) => {
        console.error(err);
      });
  }
};

const apresentarMedidas = (medidas) => {
  if (medidas.length > 0) {
    const lista_medidas = document.querySelector(".lista-medidas");
    medidas.map((medida) => {
      lista_medidas.appendChild(gerarMedida(medida));
    });
  } else {
    const lista_medidas = document.querySelector(".lista-medidas");
    let resultado = document.createElement("div");
    resultado.innerText = "Nenhuma medida foi encontrada.";
    resultado.classList.add("nenhum-resultado");
    lista_medidas.appendChild(resultado);
  }
};

const gerarMedida = (medida) => {
  const medidaDiv = document.createElement("div");
  medidaDiv.classList.add("medida");
  medidaDiv.setAttribute("data-cod-medida", medida.cod_medida);

  medidaDiv.addEventListener("click", (element) => abrirMedida(element));
  const meses = [
    "Jan",
    "Fev",
    "Mar",
    "Abr",
    "Mai",
    "Jun",
    "Jul",
    "Ago",
    "Set",
    "Out",
    "Nov",
    "Dez",
  ];
  const data = new Date(medida.data);
  const dataFormatada = `${
    meses[data.getMonth()]
  } ${data.getFullYear().toString().substr(-2)}`;

  const descricao = medida.descricao.split(" ");
  console.log(descricao);

  let descricaoFormatada = descricao[0];
  if (descricao[1] !== undefined) {
    descricaoFormatada += " " + descricao[1];
  }
  const content = `
  <div class="descricao">
    <p class="nome">${descricaoFormatada}</p>
    <p class="data">${dataFormatada}</p>
  </div>
  `;

  medidaDiv.innerHTML = content;

  return medidaDiv;
};

const obterAprendizados = () => {
  const matricula = getMatricula();

  if (matricula) {
    const dados = { acao: "Alunos/obterAprendizados", aluno: matricula };

    sendRequest(dados)
      .then((response) => {
        apresentarAprendizados(response);
      })
      .catch((err) => {
        console.error(err);
      });
  }
};

const apresentarAprendizados = (aprendizados) => {
  if (aprendizados.length > 0) {
    const lista_aprendizados = document.getElementById("lista-aprendizados");
    aprendizados.map((aprendizado) => {
      lista_aprendizados.appendChild(gerarAprendizado(aprendizado));
		});
  } else{
		const lista_aprendizados = document.getElementById("lista-aprendizados");
    let resultado = document.createElement("div");
    resultado.innerText = "Nenhuma medida foi encontrada.";
    resultado.classList.add("nenhum-resultado");
    lista_aprendizados.appendChild(resultado);
	}
};

const gerarAprendizado = (aprendizado) => {
	const aprendizadoDiv = document.createElement("div");
	aprendizadoDiv.classList.add("aprendizado");

	const meses = [
    "Jan",
    "Fev",
    "Mar",
    "Abr",
    "Mai",
    "Jun",
    "Jul",
    "Ago",
    "Set",
    "Out",
    "Nov",
    "Dez",
  ];
  const data = new Date(aprendizado.data);
  const dataFormatada = `${
    meses[data.getMonth()]
  } ${data.getFullYear().toString().substr(-2)}`;
	let content = `
		<div class="descricao">
			<p class="disciplina">${aprendizado.disciplina.nome}</p>
			<p class="data">${dataFormatada}</p>  
		</div>
	`

	aprendizadoDiv.innerHTML = content;
	return aprendizadoDiv;
}




const abrirMedida = (element) => {
  console.log("Apertou o aprendizado!");

  let medida = element.currentTarget.getAttribute("data-cod-medida");

  const modalMedida = document.getElementById("visualizar-medida");

  if (medida) {
    localStorage.setItem("medida", medida);
    modalMedida.classList.toggle("is-active");

    const dados = {
      acao: "MedidasDisciplinares/selecionar",
      medida_disciplinar: medida,
    };

    sendRequest(dados)
      .then((response) => {
        console.log(response);
        preencherMedida(response);
      })
      .catch((err) => {
        console.error(err);
      });
  }
};

const preencherMedida = (medida) => {
  const modal = document.getElementById("visualizar-medida");
  modal.querySelector(".info-m .nome").innerHTML = medida.aluno.nome;
  modal.querySelector(".info-m .matricula").innerHTML = medida.aluno.matricula;
  const data = new Date(medida.data);

  const meses = [
    "Janeiro",
    "Fevereiro",
    "Março",
    "Abril",
    "Maio",
    "Junho",
    "Julho",
    "Agosto",
    "Setembro",
    "Outubro",
    "Novembro",
    "Dezembro",
  ];

  const dataFormatada = ` ${data.getDay()} ${
    meses[data.getMonth()]
  } ${data.getFullYear()}`;
  modal.querySelector(".info-m .data").innerHTML = dataFormatada;

  modal.querySelector(".info-medida .tipo-medida p").innerHTML =
    medida.descricao;
  modal.querySelector(".info-medida .observacao").innerHTML = medida.observacao;
};

const closeModal = (params) => {
  const modalMedida = document.getElementById("visualizar-medida");

  let closeBtn = modalMedida.querySelector(".modal-close-btn");
  closeBtn.addEventListener("click", (evnt) => {
    fecharMedida();
  });
  let bgModal = modalMedida.querySelector(".modal-background");
  bgModal.addEventListener("click", (evnt) => {
    fecharMedida();
  });
};

const fecharMedida = () => {
  const modal = document.getElementById("visualizar-medida");
  modal.classList.toggle("is-active");

  if (localStorage.getItem("medida")) {
    localStorage.removeItem("medida");
  }

  modal.querySelector(".info-m .nome").innerHTML = "";
  modal.querySelector(".info-m .matricula").innerHTML = "";

  modal.querySelector(".info-m .data").innerHTML = "";

  modal.querySelector(".info-medida .tipo-medida p").innerHTML = "";
  modal.querySelector(".info-medida .observacao").innerHTML = "";
};

const obterEstudantes = () => {
  const turma = localStorage.getItem("turmaAtual");

  if (turma) {
    const dados = { acao: "Turmas/listarEstudantes", turma: turma };

    sendRequest(dados)
      .then((response) => {
        console.log(response);
        listarEstudantes(response);
        localStorage.setItem("estudantes", JSON.stringify(response));
      })
      .catch((err) => {
        console.error(err);
      });
  }
};

obterInfoAluno();
obterEstatisticaAluno();
obterPrincipaisAvaliacoes();
obterMedidasDisciplinares();
obterAprendizados();
listener();
