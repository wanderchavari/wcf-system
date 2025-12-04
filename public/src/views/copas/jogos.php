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
            $timestamp_atual = time();
            $timestamp = strtotime($jogo['data_jogo']);
            $dataJogo = date('d/m/Y', $timestamp);
            $horaJogo = date('H:i', $timestamp);

            $faseGrupo = $jogo['fase'] . ($jogo['grupo'] ? " ({$jogo['grupo']})" : '');

            $golsCasa = (int)$jogo['gols_casa'];
            $golsFora = (int)$jogo['gols_fora'];
            $placar = "{$golsCasa}:{$golsFora}";
            
            $gpCasa = (int)$jogo['gp_casa'];
            $gpFora = (int)$jogo['gp_fora'];

            $placar = "{$golsCasa} : {$golsFora}";

            // --- Lógica de Destaque do Vencedor ---
            $vencedorCasa = '';
            $vencedorFora = '';
            $scoreColorClass = 'bg-secondary'; // Cor padrão (cinza)
            $statusJogo = 'AGUARDANDO';
            $showPenalties = ($gpCasa > 0 || $gpFora > 0);
            if ($timestamp_atual > $timestamp) {
                $statusJogo = 'FIM DE JOGO';
            }

            if ($showPenalties) {
                // 1. Houve Pênaltis: O vencedor é determinado pelo placar dos pênaltis
                if ($gpCasa > $gpFora) {
                    $vencedorCasa = 'vencedor';
                } elseif ($gpFora > $gpCasa) {
                    $vencedorFora = 'vencedor';
                }
                $placar = "{$golsCasa}({$gpCasa}) : ({$gpFora}){$golsFora}";
                $statusJogo = 'PÊNALTIS';
                $scoreColorClass = 'bg-danger '; // Cor para placares finalizados com PÊNALTIS

            } elseif ($golsCasa > $golsFora || $golsFora > $golsCasa) {
                // 2. Vitória no Tempo Normal/Prorrogação
                if ($golsCasa > $golsFora) {
                    $vencedorCasa = 'vencedor';
                } else {
                    $vencedorFora = 'vencedor';
                }
                $statusJogo = 'FIM DE JOGO';
                $scoreColorClass = 'bg-success'; // Cor para vitória
                
            } elseif ($golsCasa == $golsFora && ($timestamp_atual > $timestamp)) {
                 // 3. Empate (após gols)
                $statusJogo = 'FIM DE JOGO';
                $scoreColorClass = 'bg-info text-dark';
            }

            $flagCasa = $jogo['bandeira_casa'];
            $flagFora = $jogo['bandeira_fora'];

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
                        <i class="bi bi-code-square me-1 text-info"></i> <?= $jogo['estadio'] ?>
                        <i class="bi bi-geo-alt-fill me-1 text-info"></i> <?= $jogo['cidade'] ?>
                    </div>
                    <div class="text-group mx-5 my-1">
                        <?= $faseGrupo ?>
                    </div>
                </div>

                <!-- <a href="<?= $linkDetalhes ?>" class="text-decoration-none"> -->
                    <div class="match-main d-flex justify-content-between align-items-center p-3">
                        
                        <div class="team-info team-home text-end flex-grow-1 me-3 <?= $vencedorCasa ?>">
                            <span class="team-name fw-bold text-light"><?= $jogo['selecao_casa'] ?></span>
                            <img src="<?= $flagCasa ?>" alt="Bandeira <?= $jogo['selecao_casa'] ?>" class="team-icon">
                        </div>

                        <div class="score-container text-center align-items-center justify-content-center">
                            <div class="status-box small fw-bold text-light-50 mt-1">
                                <?= $statusJogo ?>
                            </div>
                            <div class="score-box <?= $scoreColorClass ?> text-light">
                                <span class="score fs-2 fw-bolder"><?= $placar ?></span>
                            </div>
                        </div>

                        <div class="team-info team-away text-start flex-grow-1 ms-3 <?= $vencedorFora ?>">
                            <img src="<?= $flagFora ?>" alt="Bandeira <?= $jogo['selecao_fora'] ?>" class="team-icon">
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