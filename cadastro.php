<?php
include 'conexao.php';

$mensagem = '';
$tipo = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha_raw = $_POST['senha'];
    $nivel = $_POST['nivel'];

    if (empty($nome) || empty($email) || empty($senha_raw) || empty($nivel)) {
        $mensagem = "Preencha todos os campos!";
        $tipo = "warning";
    }
    elseif (preg_match('/[<>"\'%;(){}]/', $nome) || preg_match('/[<>"\'%;(){}]/', $email) || preg_match('/[<>"\'%;(){}]/', $senha_raw)) {
        $mensagem = "Input inválido!";
        $tipo = "error";
    }
    else {
        $senha = password_hash($senha_raw, PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $resultado = $check->get_result();

        if ($resultado->num_rows > 0) {
            $mensagem = "Este e-mail já está cadastrado.";
            $tipo = "error";
        } else {
            $sql = "INSERT INTO usuarios (nome, email, senha, nivel) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $nome, $email, $senha, $nivel);

            if ($stmt->execute()) {
                $mensagem = "Usuário cadastrado com sucesso!";
                $tipo = "success";
                echo "<script>
                    setTimeout(function(){
                        window.location.href = 'index.php';
                    }, 1800);
                </script>";
            } else {
                $mensagem = "Erro ao cadastrar: " . $conn->error;
                $tipo = "error";
            }
            $stmt->close();
        }
        $check->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <h2>Cadastro</h2>
        <form method="POST">
            <input type="text" name="nome" placeholder="Nome" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <select name="nivel">
                <option value="membro">Membro</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit">Cadastrar</button>
            <p>Já tem conta? <a href="index.php">Entrar</a></p>
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
