let events = () => {
  resposiveNavbar();
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
events();
