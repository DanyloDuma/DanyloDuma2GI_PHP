<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['employeeNumber'])) {
  die("Acesso negado");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $table = $_POST['table'];
  unset($_POST['table']);

  // Remove o campo 'id' se ele não existir na tabela (precaução extra)
  if (isset($_POST['id'])) {
    unset($_POST['id']);
  }

  // Remove campos vazios (ex: auto_increment não preenchido)
  $filtered = array_filter($_POST, function ($v) {
    return $v !== '';
  });

  if (empty($filtered)) {
    die('Nenhum dado válido enviado.');
  }
  $columns = array_keys($filtered);
  $values = array_values($filtered);

  $placeholders = implode(',', array_fill(0, count($values), '?'));
  $columnsList = implode(',', $columns);

  $types = str_repeat('s', count($values));

  $sql = "INSERT INTO $table ($columnsList) VALUES ($placeholders)";
  $stmt = $conn->prepare($sql);

  if (!$stmt) {
    die("Erro no prepare: " . $conn->error);
  }

  $stmt->bind_param($types, ...$values);

  if ($stmt->execute()) {
    echo "success";
  } else {
    echo "Erro ao inserir: " . $conn->error;
  }

  $stmt->close();
  $conn->close();
}
?>