function toggleClass(elem, className) {
    if (elem.className.indexOf(className) !== -1) {
      elem.className = elem.className.replace(className, "");
    } else {
      elem.className = elem.className.replace(/\s+/g, " ") + " " + className;
    }

    return elem;
  }

  function toggleDisplay(elem) {
    const curDisplayStyle = elem.style.display;

    if (curDisplayStyle === "none" || curDisplayStyle === "") {
      elem.style.display = "block";
    } else {
      elem.style.display = "none";
    }
  }

  function toggleMenuDisplay(e) {
    const dropdown = e.currentTarget.parentNode;
    const menu = dropdown.querySelector(".menu");
    const icon = dropdown.querySelector(".fa-angle-right");

    toggleClass(menu, "hide");
    toggleClass(icon, "rotate-90");
  }

  function handleOptionSelected(e) {
    toggleClass(e.target.parentNode, "hide");

    const id = e.target.id;
    const newValue = e.target.textContent + " ";
    const titleElem = document.querySelector(".dropdown .titulo");
    const icon = document.querySelector(".dropdown .titulo .fa");

    titleElem.textContent = newValue;
    titleElem.appendChild(icon);

    //trigger custom event
    document
      .querySelector(".dropdown .titulo")
      .dispatchEvent(new Event("change"));
    //setTimeout is used so transition is properly shown
    setTimeout(() => toggleClass(icon, "rotate-90", 0));
  }

  function handleTitleChange(e) {
    const result = document.getElementById("result");

    // result.innerHTML = "The result is: " + e.target.textContent;
    console.log("Resultado do Checkbox" + e.target.textContent);
  }

  //get elements
  const dropdownTitle = document.querySelector(".dropdown .titulo");
  const dropdownOptions = document.querySelectorAll(".dropdown .option");

  //bind listeners to these elements
  dropdownTitle.addEventListener("click", toggleMenuDisplay);

  dropdownOptions.forEach((option) =>
    option.addEventListener("click", handleOptionSelected)
  );

  document
    .querySelector(".dropdown .titulo")
    .addEventListener("change", handleTitleChange);