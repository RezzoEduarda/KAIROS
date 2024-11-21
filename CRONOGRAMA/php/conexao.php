<?php
// arquivo de conexão: conexao.php

$host = '127.0.0.1';
$dbname = 'CronogramaApp';
$username = 'root';
$password = '!Hunt3rP1s08642';

try {
    // Cria a conexão com o banco de dados usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Configura o PDO para lançar exceções em caso de erros
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<script>console.log('Conexão bem-sucedida ao banco de dados.');</script>";
} catch (PDOException $e) {
    // Mensagem de erro em caso de falha na conexão
    echo "<script>console.error('Erro na conexão: " . $e->getMessage() . "');</script>";
    exit;
}

?>
