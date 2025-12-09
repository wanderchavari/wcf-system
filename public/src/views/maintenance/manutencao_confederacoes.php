<?php 
// Esta view espera as variáveis $pageTitle, $message e $confederations vindas do Controller.
    $getSortLink = function(string $column, string $current_sort, string $current_direction) {
        if ($column === $current_sort) {
            $new_dir = ($current_direction === 'asc') ? 'desc' : 'asc';
        } else {
            $new_dir = 'asc';
        }
        return "?sort={$column}&direction={$new_dir}";
    };
?>

<div class="container mt-5">
    <h2 class="text-light fw-bold mb-4"><?= $pageTitle ?? 'Manutenção' ?></h2>

    <?= $message ?? '' ?>

    <form method="GET" action="/manutencao/confederacoes" class="mb-4">
        <div class="input-group">
            <input type="text" 
                   class="form-control bg-dark text-light border-secondary" 
                   placeholder="Pesquisar por Sigla ou Nome Completo..." 
                   name="search" 
                   value="<?= htmlspecialchars($searchTerm ?? '') ?>">
            
            <input type="hidden" name="sort" value="<?= $current_sort ?? 'id_confederacao' ?>">
            <input type="hidden" name="dir" value="<?= $current_direction ?? 'asc' ?>">
            
            <button class="btn btn-outline-info" type="submit">
                <i class="fas fa-search"></i> Buscar
            </button>
            <?php if (!empty($searchTerm)): ?>
                <a href="/manutencao/confederacoes?sort=<?= $current_sort ?? 'id_confederacao' ?>&direction=<?= $current_direction ?? 'asc' ?>" class="btn btn-outline-secondary">Limpar</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="card bg-dark text-light border-secondary mb-5">
        <div class="card-header border-secondary">
            Nova Confederação
        </div>
        <div class="card-body">
            <form action="/manutencao/confederacoes" method="POST">
                
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="sigla" class="form-label">Sigla (Ex: UEFA)</label>
                        <input type="text" class="form-control" id="sigla" name="sigla" maxlength="10" required>
                    </div>

                    <div class="col-md-9">
                        <label for="nome_completo" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nome_completo" name="nome_completo" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="url_logo" class="form-label">URL da Logo (Opcional)</label>
                    <input type="text" class="form-control" id="url_logo" name="url_logo">
                </div>
                
                <button type="submit" class="btn btn-outline-warning">
                    <i class="fas fa-plus me-1"></i> Cadastrar Confederação
                </button>
            </form>
        </div>
    </div>

    <h3 class="text-light fw-bold mb-3">Confederações Cadastradas (<?= count($data) ?>)</h3>
    <?php if (!empty($data)): ?>
    <div class="table-responsive">
        <table class="table table-dark table-striped table-hover border-secondary">
            <thead>
                <tr>
                    <th scope="col">
                        <a href="<?= $getSortLink('id_confederacao', $current_sort ?? '', $current_direction ?? '') ?>" class="text-light text-decoration-none">
                            ID
                            <?php if ($current_sort === 'id_confederacao'): ?>
                                <i class="fas fa-arrow-<?= ($current_direction === 'asc') ? 'up' : 'down' ?>"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= $getSortLink('sigla', $current_sort ?? '', $current_direction ?? '') ?>" class="text-light text-decoration-none">
                            Sigla
                            <?php if ($current_sort === 'sigla'): ?>
                                <i class="fas fa-arrow-<?= ($current_direction === 'asc') ? 'up' : 'down' ?>"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= $getSortLink('nome_completo', $current_sort ?? '', $current_direction ?? '') ?>" class="text-light text-decoration-none">
                            Nome Completo
                            <?php if ($current_sort === 'nome_completo'): ?>
                                <i class="fas fa-arrow-<?= ($current_direction === 'asc') ? 'up' : 'down' ?>"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th>URL Logo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $conf): ?>
                <tr>
                    <td><?= $conf['id_confederacao'] ?></td>
                    <td><?= htmlspecialchars($conf['sigla']) ?></td>
                    <td><?= htmlspecialchars($conf['nome_completo']) ?></td>
                    <td>
                        <?php 
                            $urlLogo = $conf['url_logo'] ?? ''; 
                            if (!empty($urlLogo)): 
                        ?>
                        <a href="<?= htmlspecialchars($urlLogo) ?>" target="_blank" class="btn btn-sm btn-info" title="Abrir URL da Logo">
                            <i class="fas fa-link"></i>
                        </a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td class="text-center" style="width: 150px;">
        
                        <a href="/manutencao/confederacoes/editar/<?= $conf['id_confederacao'] ?>" 
                        class="btn btn-sm btn-outline-info me-2" 
                        title="Editar Confederação">
                        <i class="fas fa-pencil-alt"></i> 
                        </a>

                        <a href="/manutencao/confederacoes/excluir/<?= $conf['id_confederacao'] ?>" 
                        class="btn btn-sm btn-outline-danger" 
                        title="Excluir Confederação"
                        onclick="return confirm('Tem certeza que deseja excluir a confederação <?= $conf['sigla'] ?>?');">
                        <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p class="text-light">Nenhuma confederação cadastrada ainda.</p>
    <?php endif; ?>

    <div class="text-end mt-3">
        <a href="/manutencao/confederacoes/export" class="btn btn-outline-info ms-2">
            <i class="fas fa-file-export me-1"></i> Exportar Dados (JSON)
        </a>
    </div>
    
</div>

<?php 
// Inclua seu footer aqui
// require_once 'footer.php'; // Se o seu render não fizer isso
?>