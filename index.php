<?php
// Função para ler o arquivo JSON
function lerRoupas() {
    $json = file_get_contents('roupas.json');
    return json_decode($json, true);
}

// Função para salvar o arquivo JSON
function salvarRoupas($roupas) {
    file_put_contents('roupas.json', json_encode($roupas, JSON_PRETTY_PRINT));
}

// CREATE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'adicionar') {
    $nome = $_POST['nome'];
    $tamanho = $_POST['tamanho'];
    $quantidade = $_POST['quantidade'];

    // Configurando o upload da imagem
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["imagem"]["name"]);
    move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file);

    // Lê as roupas existentes
    $roupas = lerRoupas(); 

    // Garante que $roupas é um array
    if (!is_array($roupas)) {
        $roupas = [];
    }

    // Adiciona uma nova roupa
    $roupas[] = [
        'id' => count($roupas) + 1,
        'nome' => $nome,
        'tamanho' => $tamanho,
        'quantidade' => $quantidade,
        'imagem_url' => $target_file // Adiciona o caminho da imagem
    ];

    // Salva as roupas de volta no arquivo JSON
    salvarRoupas($roupas);
    header('Location: index.php'); // Redireciona após adicionar
    exit;
}

// READ
$roupas = lerRoupas();

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $roupas = array_filter($roupas, fn($roupa) => $roupa['id'] !== $id);
    salvarRoupas(array_values($roupas));
    header('Location: index.php');
    exit;
}

?>





<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Estoque de Roupas</title>
</head>
<body>

<header class="logo-superior">
        <img src="foto logo/DALL·E 2024-10-15 20.11.05 - A sleek, modern logo for a store specializing in branded sneakers like Adidas, Nike, and Puma. The logo should feature a dynamic, stylish sneaker in m.webp" alt="">
    </header>

    <div class="estoqueDeRoupas">
    <h1>Estoque de Roupas</h1>
    <h2>Adicionar Roupas</h2>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="acao" value="adicionar">
        Marca: <input type="text" name="nome" required><br><br>
        Tamanho: <input type="number" name="tamanho"  min="0"  step="1" required><br><br>
        Quantidade: <input type="number" name="quantidade" min="0" step="1" required><br><br>
        

        <button type="submit">Adicionar</button>
    </form>

    <div class="listaDeEstoque">
    <h2>Lista de Roupas</h2>
    </div>
    <table>
        
        <tr>
            <th>Nome</th>
            <th>Tamanho</th>
            <th>Quantidade</th>
            <th>Ações</th>
        </tr>

        <?php if (empty($roupas)): ?>
            <tr>
                <td colspan="5">Nenhuma roupa encontrada.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($roupas as $roupa): ?>
                <tr>
                    
                    <td><?= htmlspecialchars($roupa['nome']) ?></td>
                    <td><?= htmlspecialchars($roupa['tamanho']) ?></td>
                    <td><?= htmlspecialchars($roupa['quantidade']) ?></td>
                    <td>
                        <a href="?delete=<?= $roupa['id'] ?>">Remover</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>
</html>