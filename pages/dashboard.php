<?php
// Inicia a sessão para verificar se o utilizador está autenticado
session_start();

// Verifica se a variável de sessão 'employeeNumber' está definida. Se não, redireciona para a página de login
if (!isset($_SESSION['employeeNumber'])) {
  header("Location: ../index.php"); // Redireciona se não estiver logado
  exit;
}

// Inclui o arquivo de conexão com o banco de dados
include '../includes/db_connect.php'; 

// Função para obter os dados de uma tabela e seus comentários
function getTableDataAndComments($conn, $tableName)
{
  // Consulta para obter todos os dados da tabela
  $sql = "SELECT * FROM $tableName";
  $result = $conn->query($sql);
  if (!$result) {
    die("Erro ao obter dados da tabela $tableName: " . $conn->error);
  }
  // Armazena os dados da tabela
  $data = $result->fetch_all(MYSQLI_ASSOC);

  // Obter comentários das colunas
  $columns = array_keys($data[0]);
  $comments = [];
  foreach ($columns as $column) {
    // Consulta para obter o comentário da coluna
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
  // Loop para obter o nome e comentário de cada tabela
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

// Obtém os nomes das tabelas e seus comentários
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

  <!-- Modal para adicionar ou editar registros -->
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

  <!-- Cabeçalho da página -->
  <header class="bg-dark text-white py-3">
    <div class="container d-flex justify-content-between align-items-center">
      <h1>ClassicModels</h1>
      <a href="../logout.php" class="btn btn-light">Logout</a>
    </div>
  </header>

  <!-- Corpo principal da página -->
  <div class="container mt-4">
    <?php foreach ($tables as $table): ?>
      <div class="mb-4">
        <!-- Botão para colapsar/expandir dados de cada tabela -->
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
          <div class="card">
            <div class="card-body">
              <?php
              // Obtém os dados e comentários da tabela
              $result = getTableDataAndComments($conn, $table['name']);
              $data = $result['data'];
              $comments = $result['comments'];
              if (count($data) > 0):
                ?>
                <div class="table-responsive">
                  <!-- Exibe a tabela de dados -->
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
                            <td><?php echo htmlspecialchars($value); ?></td>
                          <?php endforeach; ?>
                          <td>
                            <!-- Botões para editar e excluir registros -->
                            <button class="btn btn-sm btn-info"
                              onclick="editRecord('<?php echo $table['name']; ?>', <?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>)">Editar</button>
                            <button class="btn btn-sm btn-danger"
                              onclick="deleteRecord('<?php echo $table['name']; ?>', '<?php echo $row[array_key_first($row)]; ?>')">Excluir</button>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div> <!-- .table-responsive -->
              <?php else: ?>
                <p>Não há dados nesta tabela.</p>
              <?php endif; ?>
              <!-- Botão para adicionar um novo registro -->
              <button class="btn btn-sm btn-success mt-2" onclick="addRecord('<?php echo $table['name']; ?>')">Adicionar
                Registo</button>
            </div> <!-- .card-body -->
          </div> <!-- .card -->
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Scripts para manipulação do modal e ações de editar/excluir -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Função para abrir o modal de adicionar/editar
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

    // Função para fechar o modal
    function closeModal() {
      document.getElementById('recordModal').style.display = 'none';
    }

    // Evento de envio do formulário de registro
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

    // Função para adicionar um novo registro
    function addRecord(table) {
      openModal('Novo Registo em ' + table, table, false);
    }

    // Função para editar um registro existente
    function editRecord(table, data) {
      console.log("editRecord chamado para tabela:", table);
      console.log("Dados recebidos:", data);
      data._id = data[Object.keys(data)[0]]; // assume a primeira coluna como PK
      openModal('Editar Registo em ' + table, table, data, true);
    }

    // Função para excluir um registro
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

  <!-- Rodapé -->
  <footer class="bg-dark text-white text-center py-3 mt-4 border-top shadow-sm">
    <div class="container">
      <p class="mb-1 fw-bold fs-6">
        Danylo Duma — Nº3 — 2ºGI — RC — AE Aqua Alba — 2025
      </p>
      <small class="text-secondary">Projeto de Avaliação — PHP & MySQL</small>
    </div>
  </footer>

</body>

</html>
