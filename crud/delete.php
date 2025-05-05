<?php
// Inicia a sessão para garantir que o usuário esteja autenticado
session_start();
include '../includes/db_connect.php'; // Inclui o arquivo de conexão à base de dados

// Verifica se o usuário está autenticado. Caso contrário, interrompe a execução com uma mensagem de acesso negado.
if (!isset($_SESSION['employeeNumber'])) {
  die("Acesso negado");
}

// Verifica se a requisição foi feita via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $table = $_POST['table']; // Obtém o nome da tabela a ser editada
  $id = $_POST['id']; // Obtém o valor da chave primária (ID) do registro a ser deletado

  try {
    // Consulta para descobrir o nome da chave primária da tabela
    $pkQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = 'classicmodels' AND TABLE_NAME = ? AND COLUMN_KEY = 'PRI' LIMIT 1";
    $stmt = $conn->prepare($pkQuery); // Prepara a consulta
    $stmt->bind_param("s", $table); // Faz o binding do parâmetro (nome da tabela)
    $stmt->execute(); // Executa a consulta
    $stmt->bind_result($pk); // Armazena o resultado da chave primária
    $stmt->fetch(); // Recupera o resultado da consulta
    $stmt->close(); // Fecha a instrução

    // Se não encontrar a chave primária, lança uma exceção
    if (!$pk) {
      throw new Exception("Chave primária não encontrada.");
    }

    // Monta a consulta SQL de DELETE
    $sql = "DELETE FROM $table WHERE $pk = ?";
    $stmt = $conn->prepare($sql); // Prepara a consulta
    $stmt->bind_param("s", $id); // Faz o binding do valor da chave primária
    $stmt->execute(); // Executa o DELETE

    // Retorna 'success' se a exclusão for bem-sucedida
    echo "success";

  } catch (mysqli_sql_exception $e) {
    // Verifica se o erro está relacionado a uma restrição de chave estrangeira
    if (strpos($e->getMessage(), 'a foreign key constraint fails') !== false) {
      echo "relacionado"; // Retorna 'relacionado' se o erro for devido a uma chave estrangeira
    } else {
      // Caso contrário, retorna o erro SQL
      echo "erro:" . $e->getMessage();
    }
  }
}
?>
