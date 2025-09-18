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
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background: linear-gradient(135deg, #001f3f 0%, #003366 50%, #004080 100%);
    min-height: 100vh;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    overflow-x: hidden;
    color: #fff;
}

/* Estrelas reais no fundo */
.star {
    position: fixed;
    color: rgba(255, 255, 255, 0.8);
    z-index: 0;
    animation: twinkle 4s infinite ease-in-out;
}

.star.small { font-size: 16px; }
.star.medium { font-size: 24px; }
.star.large { font-size: 32px; }

@keyframes twinkle {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 1; }
}

.decoration {
    position: fixed;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
    z-index: 0;
}

.decoration-1 {
    width: 300px;
    height: 300px;
    top: -150px;
    right: -150px;
}

.decoration-2 {
    width: 200px;
    height: 200px;
    bottom: -100px;
    left: -100px;
}

.container {
    width: 100%;
    max-width: 800px;
    margin: 20px auto;
    position: relative;
    z-index: 10;
}

.header-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.header-card h1 {
    margin: 0;
    font-size: 2.5rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.header-card p {
    margin: 10px 0 0;
    opacity: 0.9;
}

.form-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

input, textarea {
    width: 100%;
    padding: 12px 15px;
    border: none;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
    font-size: 16px;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: background 0.3s, box-shadow 0.3s;
}

input::placeholder, textarea::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

input:focus, textarea:focus {
    outline: none;
    background: rgba(255, 255, 255, 0.25);
    box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.2);
}

textarea {
    min-height: 120px;
    resize: vertical;
}

.submit-btn {
    width: 100%;
    padding: 15px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #ff7e5f, #feb47b);
    color: white;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}

/* Estilos para a seção de produtos */
.produtos-container {
    width: 100%;
}

.produtos-title {
    font-size: 1.8rem;
    margin-bottom: 20px;
    text-align: center;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
}

.produtos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
}

.produto-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: transform 0.3s ease;
}

.produto-card:hover {
    transform: translateY(-5px);
}

.produto-imagem {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 15px;
}

.produto-nome {
    font-size: 1.3rem;
    font-weight: bold;
    margin-bottom: 10px;
    color: #fff;
}

.produto-preco {
    font-size: 1.5rem;
    font-weight: bold;
    color: #ff7e5f;
    margin-bottom: 10px;
}

.produto-descricao {
    line-height: 1.5;
    opacity: 0.9;
    margin-bottom: 15px;
}

.sem-produtos {
    text-align: center;
    opacity: 0.7;
    padding: 40px 0;
    grid-column: 1 / -1;
}

.success-message {
    display: none;
    margin-top: 20px;
    padding: 15px;
    border-radius: 10px;
    background: rgba(76, 175, 80, 0.2);
    text-align: center;
    border: 1px solid rgba(76, 175, 80, 0.5);
}

/* Responsividade */
@media (max-width: 768px) {
    .produtos-grid {
        grid-template-columns: 1fr;
    }
    
    .header-card h1 {
        font-size: 2rem;
    }
    
    .container {
        padding: 10px;
    }
}
</style>
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