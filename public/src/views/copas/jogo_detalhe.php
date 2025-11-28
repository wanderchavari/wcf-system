<?php
// Exemplo: Recebe o ID do jogo da URL
$jogoId = $_GET['id'] ?? 0;

// TODO: Buscar os dados específicos do jogo $jogoId no banco de dados.
// Por enquanto, apenas um placeholder:
$ano = '2022'; // O ano deve ser buscado junto com o jogo, ou passado na URL
$jogoDetalhe = ['selecao_casa' => '...', 'selecao_fora' => '...', 'fase' => '...']; // Dados reais viriam do banco
?>

<div class="container mt-5">

    <h2 class="text-light fw-bold mb-4 text-center">
        Detalhes do Jogo #<?= $jogoId ?>
    </h2>

    <div class="d-flex justify-content-start mb-5">
        <a href="/" class="btn btn-outline-light me-3">
            <i class="fas fa-home me-2"></i> HOME
        </a>
        <a href="/jogos/<?= $ano ?>" class="btn btn-outline-info">
            <i class="fas fa-arrow-left me-2"></i> Voltar para Placares
        </a>
    </div>

    <div class="match-main-fixed">
        </div>

    <div class="match-details-body bg-primary p-4 mt-4">
        <h3 class="text-white text-center mb-4">MATCH</h3>
        <p class="text-white-50 text-center">Conteúdo detalhado do jogo (Gols, Jogadores, etc.) será exibido aqui.</p>
    </div>
</div>