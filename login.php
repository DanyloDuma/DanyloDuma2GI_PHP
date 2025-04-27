<?php
session_start();
include_once('includes/db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $employeeNumber = $_POST['employeeNumber'];

    $sql = "SELECT employeeNumber FROM employees WHERE email = ? AND employeeNumber = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $employeeNumber);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $_SESSION['employeeNumber'] = $employeeNumber;
        header("Location: pages/dashboard.php");
    } else {
        header("Location: index.php?error=Credenciais inválidas");
    }

    $stmt->close();
    $conn->close();
}
?>