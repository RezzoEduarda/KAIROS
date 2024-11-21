<?php
header("Content-Type: application/json");
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lendo os dados enviados no corpo da requisição
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['nome'], $data['data'], $data['horario_inicio'], $data['horario_fim'], $data['voluntario'])) {
        $nome = $data['nome'];
        $dataEvento = $data['data'];
        $horarioInicio = $data['horario_inicio'];
        $horarioFim = $data['horario_fim'];
        $voluntario = $data['voluntario'];

        // Inserindo o evento no banco de dados
        $stmt = $conn->prepare("INSERT INTO eventos (nome, data, horario_inicio, horario_fim, voluntario) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $dataEvento, $horarioInicio, $horarioFim, $voluntario]);

        echo json_encode(["message" => "Evento adicionado com sucesso"]);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Dados incompletos"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Consultando eventos no banco de dados
    $stmt = $conn->query("SELECT * FROM eventos ORDER BY data, horario_inicio");
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($eventos);
} else {
    http_response_code(405);
    echo json_encode(["message" => "Método não permitido"]);
}
?>
