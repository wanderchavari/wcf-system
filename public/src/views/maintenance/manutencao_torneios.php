<?php 
// Assumindo que você está usando a estrutura de renderização do Controller
// Variáveis disponíveis: $pageTitle, $message, $entities (a lista de torneios),
// $current_sort, $current_direction, $searchTerm, $baseRoute
use Core\Helper; 

$genero = '';
?>

<div class="container mt-5">
    <h2 class="text-light fw-bold mb-4"><?= Helper::h($pageTitle) ?? 'Manutenção' ?></h2>

    <?php if (!empty($message)): ?>
        <?= $message ?>
    <?php else: ?>
        <?php if (isset($_SESSION['message'])): ?>
            <?= $_SESSION['message'] ?>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    <?php endif; ?>

    <form method="GET" action="/manutencao/torneios" class="mb-4">
        <div class="input-group">
            <input type="text" 
                   class="form-control bg-dark text-light border-secondary" 
                   placeholder="Pesquisar por Ano, Sede ou Gênero..." 
                   name="search" 
                   value="<?= htmlspecialchars($searchTerm ?? '') ?>">
            
            <input type="hidden" name="sort" value="<?= $current_sort ?? 'ano_torneio' ?>">
            <input type="hidden" name="dir" value="<?= $current_direction ?? 'asc' ?>">
            
            <button class="btn btn-outline-info" type="submit">
                <i class="fas fa-search"></i> Buscar
            </button>
            <?php if (!empty($searchTerm)): ?>
                <a href="/manutencao/torneios?sort=<?= $current_sort ?? 'ano_torneio' ?>&direction=<?= $current_direction ?? 'asc' ?>" class="btn btn-outline-secondary">Limpar</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="card bg-dark text-light border-secondary mb-5">
        <div class="card-header border-secondary">
            Novo Torneio
        </div>
        <div class="card-body">
            <form action="/manutencao/torneios" method="POST">
                
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label for="ano_torneio" class="form-label">Ano</label>
                        <input type="number" class="form-control" id="ano_torneio" name="ano_torneio" maxlength="4" required>
                    </div>

                    <div class="col-md-5">
                        <label for="Sede" class="form-label">Sede</label>
                        <input type="text" class="form-control" id="sede" name="sede" required>
                    </div>

                    <div class="col-md-3">
                        <label for="genero" class="form-label">Gênero *</label>
                        <select name="genero" id="genero" class="form-select" required>
                            <option value="">Selecione o Gênero</option>
                            <option value="M" <?= $genero === 'M' ? 'selected' : '' ?>>Masculino</option>
                            <option value="F" <?= $genero === 'F' ? 'selected' : '' ?>>Feminino</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label for="ponto_por_vitoria" class="form-label">Pts. Vitória</label>
                        <input type="number" class="form-control" id="ponto_por_vitoria" name="ponto_por_vitoria" maxlength="1" value="3" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="url_cartaz" class="form-label">Cartaz (Opcional)</label>
                    <input type="text" class="form-control" id="url_cartaz" name="url_cartaz">
                </div>

                <div class="mb-3">
                    <label for="url_mascote" class="form-label">Mascote (Opcional)</label>
                    <input type="text" class="form-control" id="url_mascote" name="url_mascote">
                </div>
                
                <button type="submit" class="btn btn-outline-warning">
                    <i class="fas fa-plus me-1"></i> Cadastrar Torneio
                </button>
            </form>
        </div>
    </div>

    <h3 class="text-light fw-bold mb-3">Torneios Cadastrados (<?= count($data ?? []) ?>)</h3>

    <div class="table-responsive">
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th><?= Helper::sortableHeader('Ano', 'ano_torneio', $current_sort, $current_direction, $baseRoute) ?></th>
                    <th><?= Helper::sortableHeader('Sede', 'sede', $current_sort, $current_direction, $baseRoute) ?></th>
                    <th><?= Helper::sortableHeader('Gênero', 'genero', $current_sort, $current_direction, $baseRoute) ?></th>
                    <th><?= Helper::sortableHeader('Pontos V.', 'ponto_por_vitoria', $current_sort, $current_direction, $baseRoute) ?></th>
                    <th>Cartaz</th>
                    <th>Mascote</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $torneio): ?>
                        <tr>
                            <td><?= Helper::h($torneio['ano_torneio']) ?></td>
                            <td><?= Helper::h($torneio['sede']) ?></td>
                            <td><?= Helper::h($torneio['genero'] === 'M' ? 'Masculino' : 'Feminino') ?></td>
                            <td><?= Helper::h($torneio['ponto_por_vitoria']) ?></td>
                            <td>
                                <?php if (!empty($torneio['url_cartaz'])): ?>
                                    <a href="<?= Helper::h($torneio['url_cartaz']) ?>" target="_blank">Ver</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($torneio['url_mascote'])): ?>
                                    <a href="<?= Helper::h($torneio['url_mascote']) ?>" target="_blank">Ver</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= Helper::h($baseRoute) ?>/editar/<?= Helper::h($torneio['ano_torneio']) ?>" 
                                    class="btn btn-sm btn-outline-info me-2">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <a href="<?= Helper::h($baseRoute) ?>/excluir/<?= Helper::h($torneio['ano_torneio']) ?>" 
                                    class="btn btn-sm btn-outline-danger" 
                                    onclick="return confirm('Tem certeza que deseja excluir o torneio de <?= Helper::h($torneio['ano_torneio']) ?>?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">Nenhum Torneio cadastrado ou encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-end mt-3">
        <a href="<?= Helper::h($baseRoute) ?>/export" class="btn btn-outline-info ms-2">
            <i class="fas fa-file-export me-1"></i> Exportar Dados (JSON)
        </a>
    </div>

</div>