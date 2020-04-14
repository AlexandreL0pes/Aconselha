const abrirMemoria = () => {
    const memoriaLink = document.querySelector('.action-card.memoria, .action-card.memoria >* ');
    memoriaLink.addEventListener('click', function (params) {
        
        if (localStorage.getItem('conselhoAtual')) {
            window.open('./memoria.html', '_blank');
        }else{
            showMessage('Houve um erro!','Selecione um conselho antes de prosseguir.', 'error', 5000);
            
        }
    });

};

abrirMemoria();
