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

<!-- Modal -->
<div id="recordModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <form id="recordForm">
      <h4 id="modalTitle">Novo Registro</h4>
      <div id="formFields"></div>
      <input type="hidden" name="table" id="formTable">
      <input type="hidden" name="id" id="formId">
      <button type="submit" class="btn btn-success mt-3">Salvar</button>
    </form>
  </div>
</div>

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
                            onclick="editRecord('<?php echo $table['name']; ?>', <?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>)">Editar</button>
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
    function openModal(title, table, data = {}, isEdit = false) {
      console.log("Abrindo modal para:", table, data);
      document.getElementById('modalTitle').textContent = title;
      document.getElementById('formFields').innerHTML = '';
      document.getElementById('formTable').value = table;
      document.getElementById('formId').value = data._id || '';

      fetch(`../includes/db_columns.php?table=${table}`)
        .then(res => res.json())
        .then(columns => {
          columns.forEach(col => {
            if (col.EXTRA.includes('auto_increment')) return;

            const label = document.createElement('label');
            label.className = 'form-label mt-2';
            label.textContent = `${col.COLUMN_NAME} (${col.COLUMN_TYPE})`;

            const input = document.createElement('input');
            input.className = 'form-control';
            input.name = col.COLUMN_NAME;
            input.value = data[col.COLUMN_NAME] || '';

            // Só desativa em modo edição
            if (isEdit && (col.COLUMN_KEY === 'PRI' || col.COLUMN_KEY === 'MUL')) {
              input.disabled = true;
            }


            document.getElementById('formFields').appendChild(label);
            document.getElementById('formFields').appendChild(input);
          });

          document.getElementById('recordModal').style.display = 'block';
        });
    }

    function closeModal() {
      document.getElementById('recordModal').style.display = 'none';
    }

    document.getElementById('recordForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const form = e.target;
      const formData = new URLSearchParams(new FormData(form));
      const isEdit = form.id.value !== '';

      fetch(`../crud/${isEdit ? 'edit' : 'add'}.php`, {
        method: 'POST',
        body: formData
      })
        .then(res => res.text())
        .then(response => {
          if (response.trim() === 'success') {
            alert('Registo salvo com sucesso!');
            location.reload();
          } else {
            alert(response);
          }
        });
    });

    function addRecord(table) {
      openModal('Novo Registro em ' + table, table, false);
    }

    function editRecord(table, data) {
      console.log("editRecord chamado para tabela:", table);
      console.log("Dados recebidos:", data);
      data._id = data[Object.keys(data)[0]]; // assume a primeira coluna como PK
      openModal('Editar Registo em ' + table, table, data, true);
    }


    function deleteRecord(table, id) {
      if (confirm("Deseja realmente excluir este registro?")) {
        fetch('../crud/delete.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `table=${table}&id=${encodeURIComponent(id)}`
        })
          .then(res => res.text())
          .then(response => {
            if (response.trim() === "success") {
              alert("Registo excluído com sucesso.");
              location.reload();
            } else if (response.includes("relacionado")) {
              alert("Este registo está relacionado a outros dados e não pode ser excluído.");
            } else {
              alert("Erro ao excluir: " + response);
            }
          });
      }
    }
  </script>
</body>

</html>