<?php
// Inicia a sessão para acessá-la e destruí-la
session_start();

// Limpa todas as variáveis de sessão
session_unset();

// Destroi a sessão
session_destroy();

// Redireciona para a página de login
header("Location: login.php");
exit();
?>
