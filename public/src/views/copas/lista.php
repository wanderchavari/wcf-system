<?php
// O $copas vem do Controller

// A página de Copas usa o header/footer padrão, mas exibe um conteúdo diferente
?>

<h1 class="display-5">Listagem Histórica das Copas do Mundo</h1>
<p class="lead">Aqui serão listadas todas as edições da Copa do Mundo FIFA.</p>

<table class="table table-striped mt-4">
    <thead>
        <tr>
            <th>Ano</th>
            <th>Sede</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($copas as $copa): ?>
        <tr>
            <td><?= $copa['ano'] ?></td>
            <td><?= $copa['sede'] ?></td>
            <td>
                <a href="/copas/<?= $copa['ano'] ?>" class="btn btn-sm btn-info text-white">Ver Detalhes</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="alert alert-warning mt-5">
    Atenção: Para que os links de detalhes funcionem, o próximo passo será a implementação do Roteamento com Parâmetros ({ano}).
</div>