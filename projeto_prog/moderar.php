<?php
include "conexao.php"; // conexão com MySQL + variáveis do Cloudinary

// Função para deletar imagem do Cloudinary
function deletarImagemCloudinary($public_id, $cloud_name, $api_key, $api_secret) {
    $timestamp = time();
    $string_to_sign = "public_id=$public_id&timestamp=$timestamp$api_secret";
    $signature = sha1($string_to_sign);

    $data = [
        'public_id' => $public_id,
        'timestamp' => $timestamp,
        'api_key' => $api_key,
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/$cloud_name/image/destroy");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Excluir produto
if(isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $res = mysqli_query($conexao, "SELECT imagem_url FROM produtos WHERE id = $id");
    $dados = mysqli_fetch_assoc($res);

    if($dados && !empty($dados['imagem_url'])) {
        $url = $dados['imagem_url'];
        $parts = explode("/", $url);
        $filename = end($parts);
        $public_id = pathinfo($filename, PATHINFO_FILENAME);
        deletarImagemCloudinary($public_id, $cloud_name, $api_key, $api_secret);
    }

    mysqli_query($conexao, "DELETE FROM produtos WHERE id = $id") or die("Erro ao excluir: " . mysqli_error($conexao));
    header("Location: moderar.php");
    exit;
}

// Editar produto
if(isset($_POST['editar'])) {
    $id = intval($_POST['id']);
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $descricao = mysqli_real_escape_string($conexao, $_POST['descricao']);
    $preco = floatval($_POST['preco']);

    $update_sql = "UPDATE produtos SET nome='$nome', descricao='$descricao', preco=$preco WHERE id=$id";
    mysqli_query($conexao, $update_sql) or die("Erro ao atualizar: " . mysqli_error($conexao));
    header("Location: moderar.php");
    exit;
}

// Selecionar produtos para exibição
$editar_id = isset($_GET['editar']) ? intval($_GET['editar']) : 0;
$produtos = mysqli_query($conexao, "SELECT * FROM produtos ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8"/>
<title>Moderar Produtos</title>
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
            <h1>🛠️ Moderar Produtos</h1>
            <p>Gerencie os produtos cadastrados no sistema</p>
        </div>

        <div class="produtos-container">
            <h2 class="produtos-title">📦 Produtos Cadastrados</h2>
            
            <?php if (mysqli_num_rows($produtos) > 0): ?>
                <div class="produtos-grid">
                    <?php while($res = mysqli_fetch_assoc($produtos)): ?>
                        <div class="produto-card">
                            <span class="produto-id">ID: <?= $res['id'] ?></span>
                            <img src="<?= htmlspecialchars($res['imagem_url']) ?>" alt="<?= htmlspecialchars($res['nome']) ?>" class="produto-imagem">
                            <h3 class="produto-nome"><?= htmlspecialchars($res['nome']) ?></h3>
                            <div class="produto-preco">R$ <?= number_format($res['preco'], 2, ',', '.') ?></div>
                            <p class="produto-descricao"><?= nl2br(htmlspecialchars($res['descricao'])) ?></p>
                            
                            <div class="acoes-produto">
                                <a href="moderar.php?excluir=<?= $res['id'] ?>" class="btn-excluir" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                                
                                <?php if($editar_id == $res['id']): ?>
                                    <a href="moderar.php" class="btn-cancelar">Cancelar</a>
                                <?php else: ?>
                                    <a href="moderar.php?editar=<?= $res['id'] ?>" class="btn-editar">Editar</a>
                                <?php endif; ?>
                            </div>

                            <!-- Formulário de edição inline -->
                            <?php if($editar_id == $res['id']): ?>
                                <form method="post" action="moderar.php" class="form-edicao">
                                    <input type="hidden" name="id" value="<?= $res['id'] ?>">
                                    
                                    <div class="form-group">
                                        <label for="nome">Nome do Produto</label>
                                        <input type="text" name="nome" value="<?= htmlspecialchars($res['nome']) ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="descricao">Descrição</label>
                                        <textarea name="descricao" required><?= htmlspecialchars($res['descricao']) ?></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="preco">Preço (R$)</label>
                                        <input type="number" step="0.01" name="preco" value="<?= $res['preco'] ?>" required>
                                    </div>
                                    
                                    <div class="form-botoes">
                                        <input type="submit" name="editar" value="Salvar" class="btn-salvar">
                                        <a href="moderar.php" class="btn-cancelar">Cancelar</a>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="sem-produtos">
                    Nenhum produto cadastrado ainda.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Confirmação de exclusão
        document.querySelectorAll('.btn-excluir').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Tem certeza que deseja excluir este produto?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>