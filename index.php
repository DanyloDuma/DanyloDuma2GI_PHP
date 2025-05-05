<!--Página de login-->
<!--
Irá receber os dados de email e employeeNumber
-->

<!DOCTYPE html>
<html lang="pt-pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Inclusão do arquivo CSS personalizado para o estilo da página -->
  <link rel="stylesheet" href="css/style.css">
  <!-- Inclusão do Bootstrap para facilitar o design responsivo e componentes prontos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <title>LOGIN</title>
</head>

<body class="bg-light">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-lg p-3 mb-5 bg-white rounded">
          <div class="card-body">
            <!-- Título centralizado para a página de login -->
            <h1 class="text-center">Login</h1>
            <!-- Formulário que envia os dados para o script de login.php -->
            <form action="login.php" method="post">
              <!-- Campo de entrada para o e-mail -->
              <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
              <!-- Campo de entrada para o número do empregado -->
              <div class="mb-3">
                <label for="employeeNumber" class="form-label">Número do Employee</label>
                <input type="password" class="form-control" id="employeeNumber" name="employeeNumber" required>
              </div>
              <!-- Botão de envio do formulário -->
              <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
            <!-- Verificação de erro e exibição de mensagem caso haja um erro de credenciais -->
            <?php if (isset($_GET['error'])): ?>
              <div class="alert alert-danger mt-3" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Rodapé com informações do projeto e do autor -->
  <footer class="bg-dark text-white text-center py-3 mt-4 border-top shadow-sm">
    <div class="container">
      <p class="mb-1 fw-bold fs-6">
        Danylo Duma — Nº3 — 2ºGI — RC — AE Aqua Alba — 2025
      </p>
      <small class="text-secondary">Projeto de Avaliação — PHP & MySQL</small>
    </div>
  </footer>

  <!-- Inclusão dos scripts do Bootstrap para funcionalidade dos componentes interativos -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>
