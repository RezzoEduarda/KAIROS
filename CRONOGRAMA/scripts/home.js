document.addEventListener("DOMContentLoaded", () => {
    // Simula uma notificação ao clicar no ícone de notificação
    const notificationIcon = document.querySelector(".notification-icon");
    notificationIcon.addEventListener("click", () => {
        alert("Você tem novas notificações!");
    });

    // Exibe um menu de configurações ao clicar no ícone de configurações
    const settingsIcon = document.querySelector(".settings-icon");
    settingsIcon.addEventListener("click", () => {
        const config = confirm("Abrir configurações?");
        if (config) {
            alert("Indo para as configurações...");
            // Aqui você pode redirecionar para uma página de configurações
            // window.location.href = "configuracoes.html";
        }
    });

    // Adiciona comportamento aos botões de menu
    const menuButtons = document.querySelectorAll(".menu-button");
    menuButtons.forEach(button => {
        button.addEventListener("click", () => {
            // Remove destaque de outros botões e adiciona ao botão clicado
            menuButtons.forEach(btn => btn.classList.remove("active"));
            button.classList.add("active");

            // Ação para cada botão com base no seu ID
            switch (button.id) {
                case "suaEscala":
                    alert("Abrindo sua escala...");
                    // Simular navegação
                    // window.location.href = "suaEscala.html";
                    break;
                case "escalaGeral":
                    alert("Abrindo escala geral...");
                    // Simular navegação
                    // window.location.href = "escalaGeral.html";
                    break;
                case "seuPerfil":
                    alert("Abrindo seu perfil...");
                    // Simular navegação
                    // window.location.href = "perfil.html";
                    break;
            }
        });
    });
});
