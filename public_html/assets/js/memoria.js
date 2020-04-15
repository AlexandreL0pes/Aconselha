const autoSizeTextarea = () => {
    const textarea = document.querySelector('#memoriaReuniao');

    textarea.addEventListener('keydown', (e) => {
        let element = e.currentTarget;
        // element.style.height = "5px";
        element.style.height = (element.scrollHeight) + "px";
    });
};

autoSizeTextarea();