<?php
include "conexao.php"; 

// Inserir novo pedido/recado
if(isset($_POST['cadastra'])){
    $nome  = mysqli_real_escape_string($conexao, $_POST['nome']);
    $email = mysqli_real_escape_string($conexao, $_POST['email']);
    $msg   = mysqli_real_escape_string($conexao, $_POST['msg']);

    $sql = "INSERT INTO usuarios (nome, email, mensagem) VALUES ('$nome', '$email', '$msg')";
    mysqli_query($conexao, $sql) or die("Erro ao inserir dados: " . mysqli_error($conexao));
    header("Location: mural.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8"/>
<title>Mural de pedidos</title>
<link rel="stylesheet" href="style.css"/>
<script src="scripts/jquery.js"></script>
<script src="scripts/jquery.validate.js"></script>
<script>
$(document).ready(function() {
    $("#mural").validate({
        rules: {
            nome: { required: true, minlength: 4 },
            email: { required: true, email: true },
            msg: { required: true, minlength: 10 }
        },
        messages: {
            nome: { required: "Digite o seu nome", minlength: "O nome deve ter no mínimo 4 caracteres" },
            email: { required: "Digite o seu e-mail", email: "Digite um e-mail válido" },
            msg: { required: "Digite sua mensagem", minlength: "A mensagem deve ter no mínimo 10 caracteres" }
        }
    });
});
</script>
</head>
<body>
<div id="main">
<div id="geral">
<div id="header">

<div id="footer">

</div>
</div>
</div>



<div id="footer">
</div>
</div>
</div>


    <!-- Estrelas reais no fundo -->
    <div class="star small" style="top: 5%; left: 10%; animation-delay: 0s;">★</div>
    <div class="star medium" style="top: 12%; left: 25%; animation-delay: 1s;">★</div>
    <div class="star small" style="top: 18%; left: 40%; animation-delay: 0.5s;">★</div>
    <div class="star large" style="top: 22%; left: 55%; animation-delay: 1.5s;">★</div>
    <div class="star medium" style="top: 30%; left: 70%; animation-delay: 0.7s;">★</div>
    <div class="star small" style="top: 35%; left: 85%; animation-delay: 1.2s;">★</div>
    <div class="star medium" style="top: 40%; left: 15%; animation-delay: 0.3s;">★</div>
    <div class="star large" style="top: 45%; left: 30%; animation-delay: 1.8s;">★</div>
    <div class="star small" style="top: 50%; left: 45%; animation-delay: 0.9s;">★</div>
    <div class="star medium" style="top: 55%; left: 60%; animation-delay: 1.1s;">★</div>
    <div class="star large" style="top: 60%; left: 75%; animation-delay: 0.4s;">★</div>
    <div class="star small" style="top: 65%; left: 90%; animation-delay: 1.6s;">★</div>
    <div class="star medium" style="top: 70%; left: 20%; animation-delay: 0.8s;">★</div>
    <div class="star large" style="top: 75%; left: 35%; animation-delay: 1.3s;">★</div>
    <div class="star small" style="top: 80%; left: 50%; animation-delay: 0.2s;">★</div>
    <div class="star medium" style="top: 85%; left: 65%; animation-delay: 1.7s;">★</div>
    <div class="star large" style="top: 90%; left: 80%; animation-delay: 0.6s;">★</div>
    <div class="star small" style="top: 92%; left: 95%; animation-delay: 1.4s;">★</div>
    <div class="star medium" style="top: 10%; left: 5%; animation-delay: 0.1s;">★</div>
    <div class="star large" style="top: 25%; left: 50%; animation-delay: 1.9s;">★</div>
    <div class="star small" style="top: 38%; left: 65%; animation-delay: 0.7s;">★</div>
    <div class="star medium" style="top: 48%; left: 80%; animation-delay: 1.2s;">★</div>
    <div class="star large" style="top: 58%; left: 10%; animation-delay: 0.3s;">★</div>
    <div class="star small" style="top: 68%; left: 25%; animation-delay: 1.5s;">★</div>
    <div class="star medium" style="top: 78%; left: 40%; animation-delay: 0.9s;">★</div>


    <div class="decoration decoration-1"></div>
    <div class="decoration decoration-2"></div>


    <div class="container">
        <div class="header-card">
            <h1>📋 Mural de Pedidos</h1>
            <p>Envie suas solicitações e entraremos em contato!</p>
        </div>


        <div class="email-card">
            <form class="email-form" id="contactForm">
                <div class="form-group">
                    <label for="name">Nome Completo</label>
                    <input type="text" id="name" placeholder="Digite seu nome completo" required />
                </div>


                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" placeholder="seu@email.com" required />
                </div>


                <div class="form-group">
                    <label for="subject">Assunto</label>
                    <input type="text" id="subject" placeholder="Assunto do pedido" required />
                </div>


                <div class="form-group">
                    <label for="message">Mensagem</label>
                    <textarea id="message" placeholder="Descreva seu pedido em detalhes..." required></textarea>
                </div>


                <button type="submit" class="submit-btn">Enviar Pedido</button>
            </form>


            <div class="success-message" id="successMessage">
                ✅ Pedido enviado com sucesso! Retornaremos em breve.
            </div>
        </div>
    </div>


    <script>
        document.getElementById('contactForm').addEventListener('submit', function (e) {
            e.preventDefault();


            // Simulação de envio bem-sucedido
            const successMessage = document.getElementById('successMessage');
            successMessage.style.display = 'block';


            // Limpar formulário
            this.reset();


            // Esconder mensagem após 5 segundos
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 5000);
        });
    </script>
</body>
</html>
</body>
</html>
