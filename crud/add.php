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
  $table = $_POST['table']; // Obtém o nome da tabela a ser inserido o novo registro
  unset($_POST['table']); // Remove o parâmetro 'table' do array para não ser inserido

  // Remove o campo 'id' se ele existir (caso de campos auto_increment)
  if (isset($_POST['id'])) {
    unset($_POST['id']);
  }

  // Filtra os campos vazios (por exemplo, valores auto_increment não preenchidos) para garantir que apenas campos com valores válidos sejam enviados
  $filtered = array_filter($_POST, function ($v) {
    return $v !== '';
  });

  // Verifica se algum dado válido foi enviado
  if (empty($filtered)) {
    die('Nenhum dado válido enviado.');
  }

  // Obtém os nomes das colunas e valores a partir dos dados filtrados
  $columns = array_keys($filtered);
  $values = array_values($filtered);

  // Prepara os placeholders para os valores (ex: ?, ?, ?, ...)
  $placeholders = implode(',', array_fill(0, count($values), '?'));

  // Cria uma lista de colunas para a query (ex: coluna1, coluna2, ...)
  $columnsList = implode(',', $columns);

  // Define o tipo de dados para cada valor (sempre string no caso de 's')
  $types = str_repeat('s', count($values));

  // Monta a query SQL de inserção
  $sql = "INSERT INTO $table ($columnsList) VALUES ($placeholders)";
  $stmt = $conn->prepare($sql); // Prepara a consulta

  // Verifica se a preparação falhou
  if (!$stmt) {
    die("Erro no prepare: " . $conn->error);
  }

  // Associa os valores aos placeholders na query
  $stmt->bind_param($types, ...$values);

  // Executa a consulta e verifica se a inserção foi bem-sucedida
  if ($stmt->execute()) {
    echo "success"; // Retorna 'success' se a inserção for bem-sucedida
  } else {
    echo "Erro ao inserir: " . $conn->error; // Retorna erro caso contrário
  }

  // Fecha a declaração e a conexão
  $stmt->close();
  $conn->close();
}
?>