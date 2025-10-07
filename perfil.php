<?php
session_start();
include 'conexao.php';
if(!isset($_SESSION['id'])){
    header("Location: index.php");
    exit;
}
$sql = "SELECT * FROM usuarios WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <h2>Perfil de <?php echo $user['nome']; ?></h2>
    <p>Email: <?php echo $user['email']; ?></p>
    <p>Nível: <?php echo $user['nivel']; ?></p>
    <a href="dashboard.php">Voltar</a>
</body>
</html>