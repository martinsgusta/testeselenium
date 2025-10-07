<?php
session_start();
if(!isset($_SESSION['id'])){
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <header> Sistema de login </header>
    <div class="container">
        <h2>Bem-vindo, <?php echo $_SESSION['nome']; ?>!</h2>
        <p>Email: <?php echo $_SESSION['email']; ?></p>
        <p>Nível: <?php echo $_SESSION['nivel']; ?></p>
        <a href="perfil.php">Meu Perfil</a>
        <?php if($_SESSION['nivel'] == 'admin'){ ?>
            <a class="admin-link" href="usuarios.php">Gerenciar Usuários</a>
        <?php } ?>
        <a href="logout.php">Sair</a>
    </div>
</body>
</html>