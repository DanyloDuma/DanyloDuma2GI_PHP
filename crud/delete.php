<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['employeeNumber'])) {
  die("Acesso negado");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $table = $_POST['table'];
  $id = $_POST['id'];

  try {
    // Descobre a chave primária
    $pkQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = 'classicmodels' AND TABLE_NAME = ? AND COLUMN_KEY = 'PRI' LIMIT 1";
    $stmt = $conn->prepare($pkQuery);
    $stmt->bind_param("s", $table);
    $stmt->execute();
    $stmt->bind_result($pk);
    $stmt->fetch();
    $stmt->close();

    if (!$pk) {
      throw new Exception("Chave primária não encontrada.");
    }

    // Executa o DELETE com tratamento de exceção
    $sql = "DELETE FROM $table WHERE $pk = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();

    echo "success";

  } catch (mysqli_sql_exception $e) {
    if (strpos($e->getMessage(), 'a foreign key constraint fails') !== false) {
      echo "relacionado";
    } else {
      echo "erro:" . $e->getMessage();
    }
  }
}
?>