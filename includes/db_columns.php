<?php
// Inclusão do arquivo de conexão à base de dados
include 'db_connect.php';

// Obtém o nome da tabela a partir da URL
$table = $_GET['table'] ?? ''; // Caso a tabela não seja especificada, assume um valor vazio
if (!$table) {
  // Caso não seja especificada a tabela, retorna um erro 400 (Bad Request)
  http_response_code(400);
  echo json_encode(['erro' => 'Tabela não especificada']);
  exit; // Encerra a execução do script
}

// Consulta SQL para obter informações sobre as colunas da tabela
$sql = "SELECT COLUMN_NAME, COLUMN_KEY, EXTRA, COLUMN_TYPE
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = 'classicmodels' AND TABLE_NAME = ?";
$stmt = $conn->prepare($sql); // Prepara a consulta para prevenção de SQL injection
$stmt->bind_param("s", $table); // Faz o binding do parâmetro (nome da tabela)
$stmt->execute(); // Executa a consulta
$result = $stmt->get_result(); // Obtém o resultado da consulta

$columns = []; // Array para armazenar os dados das colunas
while ($row = $result->fetch_assoc()) {
  // Preenche o array com os dados de cada coluna
  $columns[] = $row;
}

// Retorna os dados das colunas em formato JSON
echo json_encode($columns);
?>
