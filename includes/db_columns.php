<?php
include 'db_connect.php';

$table = $_GET['table'] ?? '';
if (!$table) {
  http_response_code(400);
  echo json_encode(['erro' => 'Tabela não especificada']);
  exit;
}

$sql = "SELECT COLUMN_NAME, COLUMN_KEY, EXTRA, COLUMN_TYPE
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = 'classicmodels' AND TABLE_NAME = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $table);
$stmt->execute();
$result = $stmt->get_result();

$columns = [];
while ($row = $result->fetch_assoc()) {
  $columns[] = $row;
}

echo json_encode($columns);
?>