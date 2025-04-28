<?php
session_start();
if (!isset($_SESSION['employeeNumber'])) {
  header("Location: ../index.php"); // Redireciona se não estiver logado
  exit;
}

include '../includes/db_connect.php'; // Inclui o arquivo de conexão

// Função para obter os dados de uma tabela e seus comentários
function getTableDataAndComments($conn, $tableName)
{
  $sql = "SELECT * FROM $tableName";
  $result = $conn->query($sql);
  if (!$result) {
    die("Erro ao obter dados da tabela $tableName: " . $conn->error);
  }
  $data = $result->fetch_all(MYSQLI_ASSOC);

  // Obter comentários das colunas
  $columns = array_keys($data[0]);
  $comments = [];
  foreach ($columns as $column) {
    $commentSql = "SELECT COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS 
                       WHERE TABLE_NAME = '$tableName' AND COLUMN_NAME = '$column' 
                       AND TABLE_SCHEMA = 'classicmodels'";
    $commentResult = $conn->query($commentSql);
    if ($commentResult && $commentResult->num_rows > 0) {
      $comments[$column] = $commentResult->fetch_row()[0];
    } else {
      $comments[$column] = ''; // Sem comentário
    }
  }

  return ['data' => $data, 'comments' => $comments];
}

// Função para obter os nomes de todas as tabelas e seus comentários
function getTableNamesAndComments($conn)
{
  $sql = "SHOW TABLES";
  $result = $conn->query($sql);
  if (!$result) {
    die("Erro ao obter nomes das tabelas: " . $conn->error);
  }
  $tables = [];
  while ($row = $result->fetch_row()) {
    $tableName = $row[0];
    $commentSql = "SELECT TABLE_COMMENT FROM INFORMATION_SCHEMA.TABLES 
                       WHERE TABLE_NAME = '$tableName' AND TABLE_SCHEMA = 'classicmodels'";
    $commentResult = $conn->query($commentSql);
    if ($commentResult && $commentResult->num_rows > 0) {
      $tableComment = $commentResult->fetch_row()[0];
    } else {
      $tableComment = ''; // Sem comentário
    }
    $tables[] = ['name' => $tableName, 'comment' => $tableComment];
  }
  return $tables;
}

$tables = getTableNamesAndComments($conn);
?>

<!DOCTYPE html>
<html lang="pt-pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ClassicModels Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>
  <header class="bg-dark text-white py-3">
    <div class="container d-flex justify-content-between align-items-center">
      <h1>ClassicModels</h1>
      <a href="../logout.php" class="btn btn-light">Logout</a>
    </div>
  </header>

  <div class="container mt-4">
    <?php foreach ($tables as $table): ?>
      <div class="mb-4">
        <button class="btn btn-primary w-100" type="button" data-bs-toggle="collapse"
          data-bs-target="#collapse<?php echo $table['name']; ?>" aria-expanded="false"
          aria-controls="collapse<?php echo $table['name']; ?>">
          <?php echo ucfirst($table['name']); ?>
          <?php if ($table['comment']): ?>
            <span class="table-comment">
              -
              <?php echo htmlspecialchars($table['comment']); ?>
            </span>
          <?php endif; ?>
        </button>
        <div class="collapse mt-3" id="collapse<?php echo $table['name']; ?>">
          <div class="card card-body">
            <div class="table-responsive">
              <?php
              $result = getTableDataAndComments($conn, $table['name']);
              $data = $result['data'];
              $comments = $result['comments'];
              if (count($data) > 0):
                ?>
                <table class="table table-striped table-bordered">
                  <thead class="table-dark">
                    <tr>
                      <?php foreach (array_keys($data[0]) as $column): ?>
                        <th>
                          <?php echo ucfirst($column); ?>
                          <?php if ($comments[$column]): ?>
                            <br>
                            <span class="column-comment">
                              <?php echo htmlspecialchars($comments[$column]); ?>
                            </span>
                          <?php endif; ?>
                        </th>
                      <?php endforeach; ?>
                      <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($data as $row): ?>
                      <tr>
                        <?php foreach ($row as $key => $value): ?>
                          <td>
                            <?php echo htmlspecialchars($value); ?>
                          </td>
                        <?php endforeach; ?>
                        <td>
                          <button class="btn btn-sm btn-info"
                            onclick="editRecord('<?php echo $table['name']; ?>', <?php echo json_encode($row); ?>)">Editar</button>
                          <button class="btn btn-sm btn-danger"
                            onclick="deleteRecord('<?php echo $table['name']; ?>', '<?php echo $row[array_key_first($row)]; ?>')">Excluir</button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <p>Não há dados nesta tabela.</p>
              <?php endif; ?>
            </div>
            <button class="btn btn-sm btn-success mt-2" onclick="addRecord('<?php echo $table['name']; ?>')">Adicionar
              Registro</button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function addRecord(table) {
      // Implementar a lógica para adicionar um registro (pode ser um modal ou redirecionamento para outra página)
      alert('Adicionar registro em ' + table);
    }

    function editRecord(table, data) {
      // Implementar a lógica para editar um registro
      console.log('Editar registro em ' + table, data);
      alert('Editar registro em ' + table);
    }

    function deleteRecord(table, id) {
      // Implementar a lógica para excluir um registro
      console.log('Excluir registro em ' + table + ' com ID: ' + id);
      alert('Excluir registro em ' + table);
    }
  </script>
</body>

</html>