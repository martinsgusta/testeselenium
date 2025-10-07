<?php
session_start();
include 'conexao.php';

$mensagem = '';
$tipo = '';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Validação de campos vazios
    if (empty($email) || empty($senha)) {
        $mensagem = "Preencha todos os campos!";
        $tipo = "warning";
    }
    // Validação de XSS (caracteres suspeitos)
    elseif (preg_match('/[<>"\'%;(){}]/', $email) || preg_match('/[<>"\'%;(){}]/', $senha)) {
        $mensagem = "Input inválido!";
        $tipo = "error";
    }
    else {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if($res->num_rows > 0){
            $user = $res->fetch_assoc();
            if(password_verify($senha, $user['senha'])){
                $_SESSION['id'] = $user['id'];
                $_SESSION['nome'] = $user['nome'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['nivel'] = $user['nivel'];

                $mensagem = "Login realizado com sucesso!";
                $tipo = "success";
                // Redireciona após mostrar o alerta
                echo "<script>
                    setTimeout(function(){
                        window.location.href = 'dashboard.php';
                    }, 1800);
                </script>";
            } else {
                $mensagem = "Senha incorreta!";
                $tipo = "error";
            }
        } else {
            $mensagem = "Usuário não encontrado!";
            $tipo = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <div id="mensagem" style="text-align:center;color:red;">
            <?php if (!empty($mensagem)) echo $mensagem; ?>
        </div>
        <form method="POST">
            <input type="email" name="email" id="email" placeholder="Email" required>
            <input type="password" name="senha" id="senha" placeholder="Senha" required>
            <button type="submit" id="btn-login">Entrar</button>
            <p>Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
        </form>
    </div>
    <?php if (!empty($mensagem)): ?>
    <script>
        Swal.fire({
            icon: '<?php echo $tipo; ?>',
            title: '<?php echo $mensagem; ?>',
            showConfirmButton: false,
            timer: 1700
        });
    </script>
    <?php endif; ?>
</body>
</html>
