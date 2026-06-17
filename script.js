// ==========================================
// 1. ROLAGEM SUAVE PARA OS LINKS DO MENU
// ==========================================
document.querySelectorAll('.nav-links a, .btn-contact').forEach(link => {
    link.addEventListener('click', function(e) {
        // Garante que o link é interno (começa com #)
        const targetId = this.getAttribute('href');
        if (targetId.startsWith('#')) {
            e.preventDefault(); // Evita o pulo seco
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: 'smooth', // Faz o deslize suave
                    block: 'start'
                });
            }
        }
    });
});

// ==========================================
// 2. ENVIO DO FORMULÁRIO SEM ATUALIZAR A TELA
// ==========================================
const formulario = document.querySelector('.contact-form');

if (formulario) {
    formulario.addEventListener('submit', function(e) {
        e.preventDefault(); // Impede o formulário de atualizar a página
        
        const btnSubmit = formulario.querySelector('.btn-submit');
        const textoOriginal = btnSubmit.innerText;
        
        // Muda o texto do botão para dar um feedback visual de carregamento
        btnSubmit.innerText = 'Enviando...';
        btnSubmit.disabled = true;
        
        // Pega todos os dados preenchidos no formulário
        const dados = new FormData(formulario);
        
        // Envia para o seu arquivo enviar.php em segundo plano
        fetch('enviar.php', {
            method: 'POST',
            body: dados
        })
        .then(response => {
            // Se o e-mail foi disparado com sucesso
            alert('Mensagem enviada com sucesso! Entraremos em contato em breve.');
            formulario.reset(); // Limpa os campos do formulário
        })
        .catch(error => {
            // Se houver algum erro de conexão
            alert('Ocorreu um erro ao enviar. Por favor, tente novamente ou use o WhatsApp.');
        })
        .finally(() => {
            // Restaura o botão ao estado original
            btnSubmit.innerText = textoOriginal;
            btnSubmit.disabled = false;
        });
    });


// ==========================================
// 3. MENU HAMBÚRGUER INTERATIVO (ABRE E FECHA)
// ==========================================
const menuToggle = document.querySelector('.menu-toggle');
const navMenu = document.querySelector('.nav-menu');
const navLinksItems = document.querySelectorAll('.nav-links a');

// Abre ou fecha ao clicar nas 3 listrinhas
if (menuToggle && navMenu) {
    menuToggle.addEventListener('click', () => {
        menuToggle.classList.toggle('active');
        navMenu.classList.toggle('active');
    });
}

// Fecha o menu automaticamente quando o usuário clica em alguma opção
navLinksItems.forEach(item => {
    item.addEventListener('click', () => {
        if (menuToggle && navMenu) {
            menuToggle.classList.remove('active');
            navMenu.classList.remove('active'); // Remove a classe que exibe o menu
        }
    });
});
}