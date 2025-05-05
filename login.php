<?php
// Inicia a sessão para poder armazenar dados do utilizador autenticado
session_start();

// Inclui o arquivo de conexão com o banco de dados
include_once('includes/db_connect.php');

// Verifica se o método de envio do formulário foi POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os valores inseridos pelo utilizador no formulário de login
    $email = $_POST['email'];
    $employeeNumber = $_POST['employeeNumber'];

    // Cria uma consulta SQL preparada para evitar injeções SQL
    $sql = "SELECT employeeNumber FROM employees WHERE email = ? AND employeeNumber = ?";
    
    // Prepara a consulta para execução
    $stmt = $conn->prepare($sql);
    
    // Liga os parâmetros da consulta (tipos 'ss' para string)
    $stmt->bind_param("ss", $email, $employeeNumber);
    
    // Executa a consulta
    $stmt->execute();
    
    // Armazena os resultados da consulta
    $stmt->store_result();

    // Verifica se encontrou exatamente uma linha com as credenciais corretas
    if ($stmt->num_rows == 1) {
        // Se as credenciais forem válidas, armazena o número do empregado na sessão
        $_SESSION['employeeNumber'] = $employeeNumber;
        
        // Redireciona o utilizador para o dashboard
        header("Location: pages/dashboard.php");
    } else {
        // Se as credenciais forem inválidas, redireciona para a página inicial com mensagem de erro
        header("Location: index.php?error=Credenciais inválidas");
    }

    // Fecha a consulta e a conexão com o banco de dados
    $stmt->close();
    $conn->close();
}
?>