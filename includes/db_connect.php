<?php
//Arquivo de conexão à base dados
$servername = "localhost"; //nome do servidor
$username = "root"; //nome do utilizador (root)
$password = ""; //password (nenhum)
$dbname = "classicmodels"; //base de dados selecionada

//conexão à base de dados
$conn = new mysqli($servername, $username, $password, $dbname);

//caso dê erro durante a conexão, retorna a mensagem de erro
if ($conn->connect_error) {
  die("Erro na conexão: " . $conn->connect_error);
}
?>