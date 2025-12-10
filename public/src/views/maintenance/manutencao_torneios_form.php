<?php
// Variáveis disponíveis: $pageTitle, $message, $entity (o torneio em edição), $baseRoute, $isEditing

use Core\Helper;

// Extrai os dados da entidade para preencher o formulário (se estiver em edição)
$isEditing = isset($data) && is_array($data);

// Se for edição, a PK é o ano_torneio. Se for cadastro, o ID (PK) é nulo.
$id = $isEditing ? ($data['ano_torneio'] ?? null) : null; 

// Campos do Formulário
$ano_torneio = $isEditing ? Helper::h($data['ano_torneio'] ?? '') : '';
$sede = $isEditing ? Helper::h($data['sede'] ?? '') : '';
$ponto_por_vitoria = $isEditing ? Helper::h($data['ponto_por_vitoria'] ?? '') : '';
$genero = $isEditing ? Helper::h($data['genero'] ?? '') : '';
$url_cartaz = $isEditing ? Helper::h($data['url_cartaz'] ?? '') : '';
$url_mascote = $isEditing ? Helper::h($data['url_mascote'] ?? '') : '';

// Define a rota de submissão do formulário
$formAction = $isEditing ? Helper::h($baseRoute) . '/editar/' . Helper::h($id) : Helper::h($baseRoute);
?>

<div class="container mt-5">

    <h2 class="text-light fw-bold mb-4"><?= Helper::h($pageTitle) ?></h2>

    <?php if (!empty($message)): ?>
        <?= $message ?>
    <?php else: ?>
        <?php if (isset($_SESSION['message'])): ?>
            <?= $_SESSION['message'] ?>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card bg-dark text-light border-secondary mb-5">
        <div class="card-header border-secondary">
            Editar Dados
        </div>
        <div class="card-body">
            <form method="POST" action="<?= $formAction ?>">
        
                <?php if ($isEditing): ?>
                    <input type="hidden" name="id" value="<?= Helper::h($id) ?>">
                <?php endif; ?>
                
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label for="ano_torneio" class="form-label">Ano do Torneio *</label>
                        <input 
                            type="number" 
                            name="ano_torneio" 
                            id="ano_torneio" 
                            class="form-control" 
                            value="<?= $ano_torneio ?>"
                            required
                            <?= $isEditing ? 'readonly' : '' ?> 
                            placeholder="Ex: 2026"
                        >
                        <?php if ($isEditing): ?>
                            <small class="form-text text-muted">A Chave Primária (Ano) não pode ser alterada.</small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-5">
                        <label for="sede" class="form-label">Sede *</label>
                        <input 
                            type="text" 
                            name="sede" 
                            id="sede" 
                            class="form-control" 
                            value="<?= $sede ?>"
                            required
                            placeholder="Ex: Brasil"
                        >
                    </div>
                    <div class="col-md-3">
                        <label for="genero" class="form-label">Gênero *</label>
                        <select name="genero" id="genero" class="form-select" required>
                            <option value="">Selecione o Gênero</option>
                            <option value="M" <?= $genero === 'M' ? 'selected' : '' ?>>Masculino</option>
                            <option value="F" <?= $genero === 'F' ? 'selected' : '' ?>>Feminino</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="ponto_por_vitoria" class="form-label">Pontos por Vitória *</label>
                        <input 
                            type="number" 
                            name="ponto_por_vitoria" 
                            id="ponto_por_vitoria" 
                            class="form-control" 
                            value="<?= $ponto_por_vitoria ?>"
                            required
                            placeholder="Ex: 3"
                        >
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="md-3">
                        <label for="url_cartaz" class="form-label">URL do Cartaz (Opcional)</label>
                        <input 
                            type="text" 
                            name="url_cartaz" 
                            id="url_cartaz" 
                            class="form-control" 
                            value="<?= $url_cartaz ?>"
                        >
                    </div>
                <!-- </div>
                <div class="row mb-3"> -->
                    <div class="md-3">
                        <label for="url_mascote" class="form-label">URL do Mascote (Opcional)</label>
                        <input 
                            type="text" 
                            name="url_mascote" 
                            id="url_mascote" 
                            class="form-control" 
                            value="<?= $url_mascote ?>"
                        >
                    </div>
                </div>
                    
                <button type="submit" class="btn btn-outline-warning">
                    <i class="fas fa-save me-1"></i> Salvar Alterações
                </button>
                
                <a href="/manutencao/torneios" class="btn btn-outline-secondary ms-2">
                    Cancelar
                </a>
                
            </form>
        </div>
    </div>

</div>
