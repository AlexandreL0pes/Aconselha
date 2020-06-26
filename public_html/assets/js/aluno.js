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
	divEstatisticas.querySelector(".coef-geral .resultado").innerHTML = estatisticas.coeficiente_geral;
	divEstatisticas.querySelector(".aprendizado .resultado").innerHTML = estatisticas.aprendizados;
	divEstatisticas.querySelector(".medidas .resultado").innerHTML = estatisticas.medidas;

}
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
obterInfoAluno();
obterEstatisticaAluno()
listener();
