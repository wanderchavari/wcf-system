<?php
// O $campeoes vem do Controller através da função extract()

// Estrutura de Cartões
?>
<h2 class="mb-4">
    <img src="/assets/img/jules_rimet.png" alt="Old Trophy Icon" style="height: 40px; vertical-align: middle; margin-right: 5px;">
    <img src="/assets/img/taca_fifa.png" alt="Trophy Icon" style="height: 40px; vertical-align: middle; margin-right: 5px;">
    Os Campeões
</h2>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-5 g-4 justify-content-center">
    
    <?php foreach ($campeoes as $titulos => $selecoes): ?>

    <div class="col">
        <div class="card h-100 shadow-sm bg-dark text-light border-secondary">
            <div class="card-header card-header-verde text-center">
                <h4 class="mb-0"><?= $titulos ?> Títulos</h4>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php 
                    if (is_array($selecoes)): 
                        foreach ($selecoes as $selecao): 
                    ?>
                        <li class="list-group-item bg-dark text-light text-center border-0">
                            <?= $selecao ?>
                        </li>
                    <?php 
                        endforeach;
                    endif; 
                    ?>
                </ul>
            </div>
        </div>
    </div>
    
    <?php endforeach; ?>

</div>