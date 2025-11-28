<div class="container mt-5">
    
    <h2 class="text-light fw-bold mb-4 text-center">
        Copa do Mundo FIFA <?= $ano ?>
    </h2>

    <div class="d-flex justify-content-between mb-5">
        <a href="/" class="btn btn-outline-light">
            <i class="fas fa-home me-2"></i> HOME
        </a>
        <a href="/copas/<?= $ano ?>" class="btn btn-outline-info">
            <i class="fas fa-table me-2"></i> Voltar para Tabela
        </a>
    </div>

    <div class="mb-4">
        </div>

    <div class="row row-cols-1 g-4">
    <?php if (!empty($jogos)): ?>
        <?php foreach ($jogos as $jogo): 
            // 1. Definição e Formatação dos Dados
            //$dataHora = date('d/m/Y H:i', strtotime($jogo['data_jogo']));
            $timestamp = strtotime($jogo['data_jogo']);
            $dataJogo = date('d/m/Y', $timestamp);
            $horaJogo = date('H:i', $timestamp);

            $faseGrupo = $jogo['fase'] . ($jogo['grupo'] ? " ({$jogo['grupo']})" : '');
            // $statusJogo = $jogo['vitoria_penaltis'] ? 'PENALTIES' : 'FULLTIME'; // Simplificado por hora
            $statusJogo = '';
            
            // 2. Definição de Cores/Classes (Personalize no CSS)
            // Exemplo: Azul para Oitavas/Quartas, Vermelho para Semifinal/Final
            $scoreColorClass = 'bg-primary-score'; 
            if (in_array($jogo['fase'], ['SEMIFINAL', 'FINAL', 'TERCEIRO'])) {
                $scoreColorClass = 'bg-danger-score';
            }

            // 3. Montagem do URL de Detalhes (Assumindo que temos o ID do jogo)
            // OBS: Você precisará garantir que o array $jogo contenha o 'id_jogo'
            $jogoId = $jogo['id_jogo'] ?? 0; 
            $linkDetalhes = "/jogo_detalhe/$jogoId"; 
        ?>

        <div class="col">
            <div class="card bg-dark border-secondary score-card">
                
                <div class="match-header text-center small text-light pt-2 pb-1">
                    <div class="mx-5 my-1">
                        <i class="bi bi-calendar-event me-1 text-info"></i> <?= $dataJogo ?>
                        <i class="bi bi-clock me-1 text-info"></i> <?= $horaJogo ?>
                        <i class="bi bi-badge-ad me-1 text-info"></i> <?= $jogo['estadio'] ?>
                        <i class="bi bi-geo-alt-fill me-1 text-info"></i> <?= $jogo['cidade'] ?>
                    </div>
                    <div class="text-group mx-5 my-1">
                        <?= $faseGrupo ?>
                    </div>
                </div>

                <!-- <a href="<?= $linkDetalhes ?>" class="text-decoration-none"> -->
                    <div class="match-main d-flex justify-content-between align-items-center p-3">
                        
                        <div class="team-info team-home text-end flex-grow-1 me-3">
                            <span class="team-name fw-bold text-light"><?= $jogo['selecao_casa'] ?></span>
                            <span class="team-icon"></span>
                        </div>

                        <div class="score-container text-center align-items-center justify-content-center">
                            <div class="score-box <?= $scoreColorClass ?> text-light">
                                <span class="score fs-2 fw-bolder"><?= $jogo['gols_casa'] ?>:<?= $jogo['gols_fora'] ?></span>
                            </div>
                            <div class="status-box small fw-bold text-light-50 mt-1">
                                <?= $statusJogo ?>
                            </div>
                        </div>

                        <div class="team-info team-away text-start flex-grow-1 ms-3">
                            <span class="team-icon"></span>
                            <span class="team-name fw-bold text-light"><?= $jogo['selecao_fora'] ?></span>
                        </div>
                    </div>
                <!-- </a> -->

                <div class="match-footer text-center small text-light py-2">
                    <?= $jogo['observacao'] ?>
                </div>
                
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-light text-center">Nenhum jogo encontrado para este torneio.</p>
    <?php endif; ?>
    </div>
</div>