<div class="container mt-5">
    
    <?php if (!empty($torneios)): ?>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            
            <?php foreach ($torneios as $torneio): ?>
                
                <div class="col">
                    <div class="card h-100 bg-dark border-secondary shadow-lg">
                        
                        <div class="card-header card-header-verde fw-bold text-light text-center">
                            <?= $torneio['ano_torneio'] ?> - <?= $torneio['sede'] ?>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            
                            <h5 class="card-title text-light mb-3">
                                Campeão: 
                                <?php if (!empty($torneio['campeao'])): ?>
                                    <span class="text-warning">🏆 <?= $torneio['campeao'] ?></span>
                                <?php else: ?>
                                    <span class="text-muted">A definir</span>
                                <?php endif; ?>
                            </h5>

                            <div class="mt-auto"> 
                                
                                <a href="/copas/<?= $torneio['ano_torneio'] ?>" class="btn btn-outline-primary btn-sm w-100">
                                    Ver Detalhes
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
                
            <?php endforeach; ?>
            
        </div>
        
    <?php else: ?>
        
        <div class="alert alert-info text-dark text-center" role="alert">
            Nenhuma edição da Copa do Mundo encontrada no banco de dados.
        </div>
        
    <?php endif; ?>

</div>