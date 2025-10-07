<?php
session_start();
include 'conexao.php';
if(!isset($_SESSION['id']) || $_SESSION['nivel'] != 'admin'){
    header("Location: dashboard.php");
    exit;
}
$res = $conn->query("SELECT id, nome, email, nivel FROM usuarios");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
    <link rel="stylesheet" href="usuarios.css">
</head>
<body>
    <header>Gerenciamento de Usuários</header>
    <div class="container">
        <h2>Lista de Usuários</h2>
        <table>
            <tr><th>ID</th><th>Nome</th><th>Email</th><th>Nível</th></tr>
            <?php while($u = $res->fetch_assoc()){ ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo $u['nome']; ?></td>
                <td><?php echo $u['email']; ?></td>
            </tr>
            <?php } ?>
        </table>
        <a href="dashboard.php">Voltar</a>
    </div>
</body>
</html>