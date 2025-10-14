<div class="container mt-5">
    
    <?php if ($isFound): ?>

        <?php 
        // Verifica se há pelo menos um resultado (Campeão, Vice ou Terceiro)
        $hasTop3Results = !empty($campeao) || !empty($vice) || !empty($terceiro);
        ?>

        <?php if ($hasTop3Results): ?>

            <h2 class="text-light fw-bold mb-5 text-center">Classificação Final</h2>

            <div class="d-flex justify-content-center align-items-end mb-5">
                
                <?php if (!empty($vice)): ?>
                    <div class="text-center mx-2 podium-col" style="width: 180px;">
                        <h4 class="copa-prata mb-1 fs-5">🥈 Vice-Campeão</h4>
                        <p class="lead fw-bold"><?= $vice ?></p>
                        <div class="podium-bar" style="height: 150px;"></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($campeao)): ?>
                    <div class="text-center mx-2 podium-col" style="width: 200px;">
                        <h3 class="copa-ouro mb-1 fs-4">🏆 Campeão</h3>
                        <p class="lead fw-bold"><?= $campeao ?></p>
                        <div class="podium-bar" style="height: 200px;"></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($terceiro)): ?>
                    <div class="text-center mx-2 podium-col" style="width: 180px;">
                        <h5 class="copa-bronze mb-1 fs-5">🥉 3º Lugar</h5>
                        <p class="lead fw-bold"><?= $terceiro ?></p>
                        <div class="podium-bar" style="height: 100px;"></div>
                    </div>
                <?php endif; ?>
            </div>

            <hr class="my-4 border-secondary">

            <div class="row mb-4">
                <div class="col-12 text-center">
                    <a href="/ranking/<?= $ano ?>" class="btn btn-outline-light">
                        <i class="fas fa-chart-bar me-2"></i> Ver Análise Estatística da Edição
                    </a>
                </div>
            </div>

            <hr class="my-4 border-secondary">

            <div class="my-5">
                <h3 class="text-center text-light fw-bold mb-4">
                    Classificação Detalhada do Torneio
                </h3>

                <?php if (!empty($ranking)): ?>
                    <div class="table-responsive">
                    <div class="table-responsive">
                        <!-- <table class="table table-dark table-hover table-striped text-light border-secondary"> -->
                        <table class="table table-dark text-light border-secondary">
                        
                            <thead>
                                <tr>
                                    <th scope="col">Pos.</th>
                                    <th scope="col">Seleção</th>
                                    <th scope="col" class="text-center">Pts</th>
                                    <th scope="col" class="text-center">V</th>
                                    <th scope="col" class="text-center">E</th>
                                    <th scope="col" class="text-center">D</th>
                                    <th scope="col" class="text-center">GP</th>
                                    <th scope="col" class="text-center">GC</th>
                                    <th scope="col" class="text-center">SG</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php foreach ($ranking as $posicao): ?>

                                <?php 
                                    // Define a classe de destaque baseada na posição
                                    $classDestaque = '';
                                    if ($posicao['classificacao_final'] == 1) {
                                        $classDestaque = 'ranking-ouro';
                                    } elseif ($posicao['classificacao_final'] == 2) {
                                        $classDestaque = 'ranking-prata';
                                    } elseif ($posicao['classificacao_final'] == 3) {
                                        $classDestaque = 'ranking-bronze';
                                    }
                                ?>

                                <tr class="<?= $classDestaque ?>">
                                    <td class="fw-bold"><?= $posicao['classificacao_final'] ?>º</td>
                                    <td><?= $posicao['nome_selecao'] ?> (<?= $posicao['sigla_iso'] ?>)</td>
                                    
                                    <td class="text-center fw-bold"><?= $posicao['pontos_torneio'] ?></td>
                                    <td class="text-center"><?= $posicao['vitorias'] ?></td>
                                    <td class="text-center"><?= $posicao['empates'] ?></td>
                                    <td class="text-center"><?= $posicao['derrotas'] ?></td>
                                    <td class="text-center"><?= $posicao['gols_feitos'] ?></td>
                                    <td class="text-center"><?= $posicao['gols_sofridos'] ?></td>
                                    <td class="text-center"><?= $posicao['saldo_gols'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Nenhum dado de classificação encontrado para este torneio.</p>
                <?php endif; ?>

            </div>

            <hr class="my-5 border-secondary">

        <?php else: ?>
            <div class="alert alert-info text-dark text-center" role="alert">
                <h4 class="alert-heading">ℹ️ Classificação ainda não definida!</h4>
                <p>A Copa de **<?= $ano ?>** está cadastrada, mas a classificação final ainda não está disponível.</p>
            </div>
        <?php endif; ?>

        <a href="/" class="btn btn-outline-light mt-4">
            <i class="bi bi-arrow-left"></i> Voltar à Home
        </a>
        
    <?php else: ?>

        <div class="alert alert-warning text-dark text-center" role="alert">
            <h4 class="alert-heading">🔍 Edição não encontrada!</h4>
            <p>Não encontramos dados para a edição da Copa do Mundo de **<?= $ano ?>** em nosso sistema.</p>
            <hr>
            <p class="mb-0">Verifique o menu "Copas do Mundo" para as edições cadastradas e tente novamente.</p>
        </div>
        
        <a href="/" class="btn btn-outline-light mt-4">
            <i class="bi bi-arrow-left"></i> Voltar à Home
        </a>

    <?php endif; ?>

</div>