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
  const dropdown = document.querySelector('#dropdown-user');
  dropdown.addEventListener('click', function (event) {
    event.currentTarget.classList.toggle('is-active');
  });
};
events();
