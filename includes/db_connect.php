<?php
// Arquivo de conexão à base de dados
$servername = "localhost"; // Nome do servidor de banco de dados
$username = "root"; // Nome de usuário (root é o padrão em muitas instalações locais)
$password = ""; // Senha (nenhuma por padrão em muitas instalações locais)
$dbname = "classicmodels"; // Nome da base de dados que estamos acessando

// Criação da conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se ocorreu um erro na conexão
if ($conn->connect_error) {
  die("Erro na conexão: " . $conn->connect_error); // Caso haja erro, exibe a mensagem de erro
}
?>
