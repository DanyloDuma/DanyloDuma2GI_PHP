<?php

// Inicia a sessão para poder manipular dados da sessão
session_start();

// Limpa todos os dados armazenados na sessão, tornando-a vazia
$_SESSION = array();

// Verifica se a utilização de cookies está ativada para a sessão
if (ini_get("session.use_cookies")) {
  // Obtém os parâmetros do cookie da sessão
  $params = session_get_cookie_params();
  
  // Apaga o cookie da sessão definindo o seu tempo de expiração no passado
  setcookie(
    session_name(), // Nome do cookie de sessão
    '', // Valor do cookie (vazio para "remover")
    time() - 42000, // Define o tempo de expiração do cookie no passado
    $params["path"], // Caminho onde o cookie é válido
    $params["domain"], // Domínio onde o cookie é válido
    $params["secure"], // Indica se o cookie deve ser enviado apenas por conexões seguras (HTTPS)
    $params["httponly"] // Indica se o cookie deve ser acessível apenas por HTTP (não por JavaScript)
  );
}

// Destrói completamente a sessão
session_destroy();

// Redireciona o utilizador para a página inicial
header("Location: index.php");

?>
