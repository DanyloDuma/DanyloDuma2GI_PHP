<?php
// Inicia a sessão para verificar se o usuário está autenticado
session_start();
include '../includes/db_connect.php'; // Inclui o arquivo de conexão à base de dados

// Verifica se o usuário está autenticado. Caso contrário, interrompe a execução com uma mensagem de acesso negado.
if (!isset($_SESSION['employeeNumber'])) {
  die("Acesso negado");
}

// Verifica se a requisição foi feita via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $table = $_POST['table']; // Obtém o nome da tabela a ser editada
  $idValue = $_POST['id'];  // Obtém o valor da chave primária (ID) do registro a ser editado
  unset($_POST['table'], $_POST['id']); // Remove os campos 'table' e 'id' para não inseri-los na consulta

  // Consulta para descobrir o nome da chave primária da tabela especificada
  $pkQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
              WHERE TABLE_SCHEMA = 'classicmodels' AND TABLE_NAME = ? AND COLUMN_KEY = 'PRI' LIMIT 1";
  $stmt = $conn->prepare($pkQuery); // Prepara a consulta
  $stmt->bind_param("s", $table); // Faz o binding do parâmetro (nome da tabela)
  $stmt->execute(); // Executa a consulta
  $stmt->bind_result($primaryKey); // Armazena o resultado da chave primária
  $stmt->fetch(); // Recupera o resultado da consulta
  $stmt->close(); // Fecha a instrução

  // Se não encontrar a chave primária, encerra a execução com uma mensagem de erro
  if (!$primaryKey) {
    die("Chave primária não encontrada.");
  }

  // Monta a lista de campos e valores para a atualização
  $set = [];
  $values = [];
  foreach ($_POST as $col => $val) {
    // Para cada coluna e valor, prepara a cláusula SET
    $set[] = "$col = ?";
    $values[] = $val; // Adiciona o valor correspondente à lista de valores
  }

  // Converte o array de SET em uma string
  $setStr = implode(', ', $set);
  $values[] = $idValue; // Adiciona o valor da chave primária ao final dos valores

  // Define o tipo de dados para os parâmetros (todos são strings no caso)
  $types = str_repeat('s', count($values));

  // Monta a consulta SQL de atualização
  $sql = "UPDATE $table SET $setStr WHERE $primaryKey = ?";
  $stmt = $conn->prepare($sql); // Prepara a consulta

  // Verifica se houve erro ao preparar a consulta
  if (!$stmt) {
    die("Erro ao preparar: " . $conn->error);
  }

  // Faz o binding dos parâmetros com a consulta SQL
  $stmt->bind_param($types, ...$values);

  // Executa a consulta e retorna "success" se bem-sucedido, ou exibe a mensagem de erro
  if ($stmt->execute()) {
    echo "success"; // Sucesso na atualização
  } else {
    echo "Erro ao atualizar: " . $conn->error; // Erro ao atualizar
  }

  $stmt->close(); // Fecha a instrução
  $conn->close(); // Fecha a conexão
}
?>
