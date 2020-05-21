import Sidebar from "./components/Sidebar.js";


let events = () => {
  resposiveNavbar();
  activeDropdown();
  navegacaoTabs();
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

Sidebar();
events();
