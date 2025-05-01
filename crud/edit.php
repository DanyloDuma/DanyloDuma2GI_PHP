<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['employeeNumber'])) {
  die("Acesso negado");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $table = $_POST['table'];
  $idValue = $_POST['id'];
  unset($_POST['table'], $_POST['id']);

  // Descobre o nome da chave primária real da tabela
  $pkQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
              WHERE TABLE_SCHEMA = 'classicmodels' AND TABLE_NAME = ? AND COLUMN_KEY = 'PRI' LIMIT 1";
  $stmt = $conn->prepare($pkQuery);
  $stmt->bind_param("s", $table);
  $stmt->execute();
  $stmt->bind_result($primaryKey);
  $stmt->fetch();
  $stmt->close();

  if (!$primaryKey) {
    die("Chave primária não encontrada.");
  }

  // Monta os pares campo = ?
  $set = [];
  $values = [];
  foreach ($_POST as $col => $val) {
    $set[] = "$col = ?";
    $values[] = $val;
  }

  $setStr = implode(', ', $set);
  $values[] = $idValue; // adiciona o valor da PK no final

  $types = str_repeat('s', count($values));

  $sql = "UPDATE $table SET $setStr WHERE $primaryKey = ?";
  $stmt = $conn->prepare($sql);

  if (!$stmt) {
    die("Erro ao preparar: " . $conn->error);
  }

  $stmt->bind_param($types, ...$values);

  if ($stmt->execute()) {
    echo "success";
  } else {
    echo "Erro ao atualizar: " . $conn->error;
  }

  $stmt->close();
  $conn->close();
}
?>