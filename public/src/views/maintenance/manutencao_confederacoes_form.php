<?php
// Espera as variáveis $pageTitle, $message, $confederation, $isEditing
// Assumindo que $confederation é o array associativo da Confederação

$idConfederacao = !empty($data) ? true : false;
$sigla = $data['sigla'] ?? '';
$nome_completo = $data['nome_completo'] ?? '';
$url_logo = $data['url_logo'] ?? '';
?>

<div class="container mt-5">
    <h2 class="text-light fw-bold mb-4"><?= $pageTitle ?></h2>

    <?= $message ?? '' ?>

    <div class="card bg-dark text-light border-secondary mb-5">
        <div class="card-header border-secondary">
            Editar Dados
        </div>
        <div class="card-body">
            <?php if ($idConfederacao) { ?>
                <form action="/manutencao/confederacoes/editar/<?= $idConfederacao ?>" method="POST">
                    
                    <input name="id" value="<?= $data['id_confederacao'] ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="sigla" class="form-label">Sigla</label>
                            <input type="text" class="form-control" id="sigla" name="sigla" value="<?= htmlspecialchars($sigla) ?>" maxlength="10" required readonly>
                        </div>

                        <div class="col-md-9">
                            <label for="nome_completo" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome_completo" name="nome_completo" value="<?= htmlspecialchars($nome_completo) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="url_logo" class="form-label">URL da Logo</label>
                        <input type="text" class="form-control" id="url_logo" name="url_logo" value="<?= htmlspecialchars($url_logo) ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-outline-warning">
                        <i class="fas fa-save me-1"></i> Salvar Alterações
                    </button>
                    
                    <a href="/manutencao/confederacoes" class="btn btn-outline-secondary ms-2">
                        Cancelar
                    </a>
                </form>
            <?php } else { ?>
                <div class="alert alert-danger" role="alert">
                    Confederação não encontrada.
            <?php } ?>
        </div>
    </div>
</div>