window.alert("teste")
document.getElementById("loginForm").addEventListener("submit", function(event) {
    event.preventDefault();

    // Captura os valores de entrada
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    // Validação de exemplo (substituir pela lógica do servidor conforme necessário)
    if (username === "admin" && password === "1234") {
        alert("Login bem-sucedido!");
        // Redirecionar para a página principal ou realizar alguma ação
    } else {
        alert("Usuário ou senha incorretos!");
    }
});
