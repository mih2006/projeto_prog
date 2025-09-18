<?php
include "conexao.php"; // Aqui já tem conexão MySQL + variáveis do Cloudinary ($cloud_name, $api_key, $api_secret)
// ==========================
// Inserir novo produto
// ==========================
if(isset($_POST['cadastra'])){
    // Pegando os dados do formulário (tratamento contra SQL Injection)
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $descricao = mysqli_real_escape_string($conexao, $_POST['descricao']);
    $preco = floatval($_POST['preco']);
    $imagem_url = ""; // Inicializa a variável que vai guardar a URL da imagem
    // --------------------------
    // Upload da imagem para Cloudinary
    // --------------------------
    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0){
        $cfile = new CURLFile($_FILES['imagem']['tmp_name'], $_FILES['imagem']['type'], $_FILES['imagem']['name']);

        $timestamp = time();
        $string_to_sign = "timestamp=$timestamp$api_secret";
        $signature = sha1($string_to_sign);

        $data = [
            'file' => $cfile,
            'timestamp' => $timestamp,
            'api_key' => $api_key,
            'signature' => $signature
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/$cloud_name/image/upload");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if($response === false){ die("Erro no cURL: " . curl_error($ch)); }
        curl_close($ch);

        $result = json_decode($response, true);
        if(isset($result['secure_url'])){
            $imagem_url = $result['secure_url'];
        } else {
            die("Erro no upload: " . print_r($result, true));
        }
    }

    // ==========================
    // Inserindo no banco de dados
    // ==========================
    if($imagem_url != ""){
        $sql = "INSERT INTO produtos (nome, descricao, preco, imagem_url) VALUES ('$nome', '$descricao', $preco, '$imagem_url')";
        mysqli_query($conexao, $sql) or die("Erro ao inserir: " . mysqli_error($conexao));
    }

    // ==========================
    // REDIRECIONAMENTO
    // ==========================
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Buscar produtos existentes
$produtos = [];
$seleciona = mysqli_query($conexao, "SELECT * FROM produtos ORDER BY id DESC");
if($seleciona) {
    $produtos = mysqli_fetch_all($seleciona, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8"/>
<title>Mural de Produtos</title>
<link rel="stylesheet" href="style.css"/>

</head>
<body>
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
            <h1>🛍️ Mural de Produtos</h1>
            <p>Adicione seus produtos com imagem, descrição e preço!</p>
        </div>

        <div class="form-card">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nome">Nome do Produto</label>
                    <input type="text" id="nome" name="nome" placeholder="Digite o nome do produto" required />
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" placeholder="Descreva o produto em detalhes..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="preco">Preço (R$)</label>
                    <input type="number" step="0.01" id="preco" name="preco" placeholder="0.00" required />
                </div>

                <div class="form-group">
                    <label for="imagem">Imagem do Produto</label>
                    <input type="file" id="imagem" name="imagem" accept="image/*" required />
                </div>

                <button type="submit" name="cadastra" class="submit-btn">Cadastrar Produto</button>
            </form>

            <div class="success-message" id="successMessage">
                ✅ Produto cadastrado com sucesso!
            </div>
        </div>
        
        <!-- Seção de produtos -->
        <div class="produtos-container">
            <h2 class="produtos-title">📦 Produtos Cadastrados</h2>
            
            <?php if (count($produtos) > 0): ?>
                <div class="produtos-grid">
                    <?php foreach ($produtos as $produto): ?>
                        <div class="produto-card">
                            <img src="<?php echo htmlspecialchars($produto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="produto-imagem">
                            <h3 class="produto-nome"><?php echo htmlspecialchars($produto['nome']); ?></h3>
                            <div class="produto-preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></div>
                            <p class="produto-descricao"><?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="sem-produtos">
                    Nenhum produto cadastrado ainda. Seja o primeiro a cadastrar!
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Mostrar mensagem de sucesso se um produto foi cadastrado
        <?php if(isset($_POST['cadastra'])): ?>
            document.addEventListener('DOMContentLoaded', function() {
                const successMessage = document.getElementById('successMessage');
                successMessage.style.display = 'block';
                
                // Esconder mensagem após 5 segundos
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 5000);
            });
        <?php endif; ?>
    </script>
</body>
</html>