import { sendRequest, getCookie } from "./utils.js";
// import { navegacaoTabs } from "./index.js";

const solicitarReunioes = async () => {
  const dados = { acao: "Reunioes/reunioesEncerradas" };

  sendRequest(dados)
    .then((response) => {
      gerarTabs(response);
      navegacaoTabs();
    })
    .catch((err) => {
      console.error(err);
    });
};

const gerarTabs = (reunioes) => {
  const tabs = document.querySelector("#tabs-with-content .tabs");
  // Gerara as tabs
  const ul = document.createElement("ul");

  const anos = Object.keys(reunioes);
  anos.sort();
  anos.reverse();
  const lis = anos.map((ano) => {
    const li = document.createElement("li");
    const a = document.createElement("a");
    a.textContent = ano;
    li.append(a);

    return li;
  });

  lis.forEach((li) => {
    ul.append(li);
  });

  ul.firstChild.classList.add("is-active");
  tabs.append(ul);

  gerarContentTab(reunioes);
};

const gerarContentTab = (reunioes) => {
  const tabsContent = document.querySelector("#tabs-with-content");
  // Criar uma section do ano
  const anos = Object.keys(reunioes);

  const sectionTabs = [];
  Object.keys(reunioes).forEach((key) => {
    const content = document.createElement("section");
    content.classList.add("tab-content");

    const reuniaoDiv = document.createElement("div");
    reuniaoDiv.classList.add("reunioes");

    // Gera os cards de reunião
    const cards = reunioes[key].map((reuniao) =>
      reuniaoDiv.append(addReuniaoCard(reuniao))
    );

    // Inclui a div de reuniao em tabs

    content.append(reuniaoDiv);

    sectionTabs.push(content);
  });

  sectionTabs.forEach((item) => tabsContent.append(item));
};

const addReuniaoCard = (reuniao) => {
  let card = document.createElement("div");

  let classCurso = "";
  if (reuniao.curso === "Informática para Internet") {
    classCurso = "is-info";
  } else if (reuniao.curso === "Meio Ambiente") {
    classCurso = "is-amb";
  } else {
    classCurso = "is-agro";
  }

  card.classList.add("cardbox", "card-turma", classCurso);
  card.setAttribute("data-turma", reuniao.codigo);
  card.setAttribute("data-turmaconselho", reuniao.reuniao);

  card.innerHTML += `
    <p class="subtitulo is-7 gray-text">${reuniao.etapa_avaliativa}° Trimestre</p>
    <p class="subtitulo is-6">${reuniao.nome}</p>
    <p class="subtitulo is-8 gray-text">${reuniao.curso}</p>
    `;

  card.addEventListener("click", (event) => abrirReuniao(event));

  return card;
};

let navegacaoTabs = () => {
    let tabs = document.querySelectorAll(".tabs li");
    let tabsContent = document.querySelectorAll(".tab-content");
  
    let deactvateAllTabs = function () {
      tabs.forEach(function (tab) {
        tab.classList.remove("is-active");
      });
    };
  
    let hideTabsContent = function () {
      tabsContent.forEach(function (tabContent) {
        tabContent.classList.remove("is-active");
      });
    };
  
    let activateTabsContent = function (tab) {
      tabsContent[getIndex(tab)].classList.add("is-active");
    };
  
    let getIndex = function (el) {
      return [...el.parentElement.children].indexOf(el);
    };
  
    tabs.forEach(function (tab) {
      tab.addEventListener("click", function () {
        deactvateAllTabs();
        hideTabsContent();
        tab.classList.add("is-active");
        activateTabsContent(tab);
      });
    });
  
    if (tabs.length > 0) {
      tabs[0].click();
    }
  };
  
solicitarReunioes();
