<div class="container my-5">
    <?php 
    
    // =================================================================
    // LÓGICA DE PAGINAÇÃO
    // =================================================================
    // Aplicar paginação apenas se for Ranking Geral OU Listagem Filtrada
    $isPaginable = (($isGeneralRanking || $listMode !== null) && count($rankingData) > 10);
    
    $paginatedData = $rankingData; // Valor padrão: todos os dados
    $page = 1;
    $totalPages = 1;

    if ($isPaginable) {
        $limit = 10; // 10 itens por página
        $page = intval($_GET['page'] ?? 1); 
        $totalItems = count($rankingData);
        $totalPages = ceil($totalItems / $limit);
        
        // Garante que a página está dentro dos limites
        if ($page < 1) $page = 1;
        if ($page > $totalPages) $page = $totalPages;
        
        $offset = ($page - 1) * $limit;

        // Limita o array de dados para a página atual
        $paginatedData = array_slice($rankingData, $offset, $limit);
    } else {
        // Se não for paginável (ranking por ano), use todos os dados
        $paginatedData = $rankingData;
    }

    // Prepara a URL base para os links de paginação
    $url_params = $_GET;
    unset($url_params['page']);
    $base_url_query = http_build_query($url_params); 
    $base_url = '/ranking?' . $base_url_query . (empty($base_url_query) ? '' : '&'); 
    
    
    // =================================================================
    // INÍCIO DO HTML
    // =================================================================
    if ($isGeneralRanking || $year !== null || $listMode !== null): 
    ?>

    <h3 class="text-center text-light fw-bold mb-4">
        <?php 
            echo $isGeneralRanking ? 'Tabela Consolidada' : ($year !== null ? 'Análise Detalhada do Torneio' : ($pageTitle ?? 'Tabela de Ranking'));
        ?>
    </h3>

    <?php if ($year === null): ?>
    <div class="table-responsive mb-5">
        <table class="table table-dark text-light border-secondary">
            <thead>
                <tr>
                    <?php if ($isGeneralRanking || $listMode !== null): // Ranking Geral ou Listagem (Consolidado Filtrado) ?>
                        <th scope="col">Seleção</th>
                        <th scope="col" class="text-center">Pts</th>
                        <th scope="col" class="text-center">Jogos</th>
                        <th scope="col" class="text-center">V</th>
                        <th scope="col" class="text-center">E</th>
                        <th scope="col" class="text-center">D</th>
                        <th scope="col" class="text-center">GP</th>
                        <th scope="col" class="text-center">GC</th>
                        <th scope="col" class="text-center">Saldo</th>
                    <?php else: // Classificação Detalhada por Ano - Cabeçalho (Não será mais renderizado aqui) ?>
                        <th scope="col">Pos.</th>
                        <th scope="col">Seleção</th>
                        <th scope="col" class="text-center">Pts</th>
                        <th scope="col" class="text-center">V</th>
                        <th scope="col" class="text-center">E</th>
                        <th scope="col" class="text-center">D</th>
                        <th scope="col" class="text-center">GP</th>
                        <th scope="col" class="text-center">GC</th>
                        <th scope="col" class="text-center">Saldo</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                $counter = 1;
                // USANDO DADOS PAGINADOS: Looping sobre o array fatiado ($paginatedData)
                foreach ($paginatedData as $item): 
                    // Lógica para destaque de cores do top 3 (se for ranking específico)
                    $classDestaque = '';
                    if (!$isGeneralRanking && $year !== null) { // Apenas se for ranking por ano!
                        if (($item['posicao'] ?? 0) == 1) $classDestaque = 'ranking-ouro';
                        elseif (($item['posicao'] ?? 0) == 2) $classDestaque = 'ranking-prata';
                        elseif (($item['posicao'] ?? 0) == 3) $classDestaque = 'ranking-bronze';
                    }
                ?>
                <tr class="<?= $classDestaque ?>">
                    <?php if ($isGeneralRanking || $listMode !== null): // Ranking Geral ou Listagem (Consolidado Filtrado) ?>
                        <td><?= $item['selecao'] ?? 'N/A' ?></td>
                        <td class="text-center fw-bold"><?= $item['pontos'] ?? '0' ?></td>
                        <td class="text-center"><?= $item['jogos'] ?? '0' ?></td>
                        <td class="text-center"><?= $item['vitorias'] ?? '0' ?></td>
                        <td class="text-center"><?= $item['empates'] ?? '0' ?></td>
                        <td class="text-center"><?= $item['derrotas'] ?? '0' ?></td>
                        <td class="text-center"><?= $item['gols_feitos'] ?? '0' ?></td>
                        <td class="text-center"><?= $item['gols_sofridos'] ?? '0' ?></td>
                        <td class="text-center fw-bold"><?= $item['saldo_gols'] ?? '0' ?></td>
                    <?php else: // Classificação Detalhada por Ano - (Este bloco técnico não será mais renderizado) ?>
                        <td class="fw-bold"><?= $item['posicao'] ?? 'N/A' ?>º</td>
                        <td><?= $item['selecao'] ?? 'N/A' ?> (<?= $item['sigla_iso'] ?? '---' ?>)</td>
                        <td class="text-center fw-bold"><?= $item['pontos'] ?? '0' ?></td>
                        <td class="text-center"><?= $item['vitorias'] ?? '0' ?></td>
                        <td class="text-center"><?= $item['empates'] ?? '0' ?></td>
                        <td class="text-center"><?= $item['derrotas'] ?? '0' ?></td>
                        <td class="text-center"><?= $item['gols_feitos'] ?? '0' ?></td>
                        <td class="text-center"><?= $item['gols_sofridos'] ?? '0' ?></td>
                        <td class="text-center fw-bold"><?= $item['saldo_gols'] ?? '0' ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <?php if ($isPaginable && $totalPages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            
            <li class="page-item mx-1 <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="btn btn-outline-light btn-sm" href="<?= $base_url . 'page=1' ?>" tabindex="-1">
                    <i class="fas fa-angle-double-left"></i> Primeiro
                </a>
            </li>

            <li class="page-item mx-1 <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="btn btn-outline-light btn-sm" href="<?= $base_url . 'page=' . ($page - 1) ?>" tabindex="-1">
                    <i class="fas fa-angle-left"></i> Anterior
                </a>
            </li>
            
            <li class="page-item active mx-1">
                <span class="btn btn-outline-light btn-sm" style="cursor: default; background-color: var(--wcf-verde-nav); border-color: var(--wcf-verde-nav);">
                    Página <?= $page ?> de <?= $totalPages ?>
                </span>
            </li>

            <li class="page-item mx-1 <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="btn btn-outline-light btn-sm" href="<?= $base_url . 'page=' . ($page + 1) ?>">
                    Próximo <i class="fas fa-angle-right"></i>
                </a>
            </li>

            <li class="page-item mx-1 <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="btn btn-outline-light btn-sm" href="<?= $base_url . 'page=' . $totalPages ?>">
                    Último <i class="fas fa-angle-double-right"></i>
                </a>
            </li>

        </ul>
    </nav>
    <?php endif; ?>
    
    <?php if ($isGeneralRanking || (isset($year) && $year > 0) || $listMode !== null): ?>
        <hr class="my-5 border-secondary">
        <h3 class="text-center text-light fw-bold mb-4">
            Visualização Gráfica
        </h3>

        <div class="row">
            <div class="col-md-12 bg-dark p-4 rounded shadow">
                <h4 class="text-light mb-4">Gols: Ataque vs. Defesa</h4>
                <canvas id="golsChart"></canvas>
            </div>
        </div>

        <div class="row mt-5"> 
            <div class="col-md-12 bg-dark p-4 rounded shadow">
                <h4 class="text-light mb-4">Desempenho: Vitórias, Empates, Derrotas</h4>
                <canvas id="desempenhoChart"></canvas> 
            </div>
        </div>
        
        <div class="row mt-5"> 
            <div class="col-md-12 bg-dark p-4 rounded shadow">
                <h4 class="text-light mb-4">Saldo de Gols (Eficiência)</h4>
                <canvas id="saldoGolsChart"></canvas> 
            </div>
        </div>

    <?php endif; ?>
    
    <?php else: ?>
        <p class="text-muted text-center">Nenhum dado de ranking encontrado para esta análise.</p>
    <?php endif; ?>

    <hr class="my-5 border-secondary">

    <a href="/" class="btn btn-outline-light mt-4">
        <i class="bi bi-arrow-left"></i> Voltar à Home
    </a>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php 
        if (($isGeneralRanking || $year !== null || $listMode !== null) && !empty($paginatedData)): 
        ?>

        const isGeneral = <?php echo json_encode($isGeneralRanking || $listMode !== null); ?>; 
        const dataSet = <?php echo json_encode($paginatedData, JSON_UNESCAPED_UNICODE); ?>;
        const dataForCharts = dataSet; 

        // Determina chaves dinâmicas
        const labelsKey = 'selecao';
        
        // Chaves do Gráfico 1 (Gols)
        const golsFeitosKey = 'gols_feitos';
        const golsSofridosKey = 'gols_sofridos';
        
        // Chaves do Gráfico 2 (V/E/D)
        const vitoriasKey = 'vitorias';
        const empatesKey = 'empates';
        const derrotasKey = 'derrotas';
        
        // 💡 CORREÇÃO JS: A chave 'saldo_gols' é a mesma para todos os modos (minúsculas)
        const saldoGolsKey = 'saldo_gols'; 

        // Arrays para os dados
        const labels = [];
        const golsFeitos = [];
        const golsSofridos = [];
        const vitorias = [];
        const empates = [];
        const derrotas = [];
        const saldoGols = [];
        const saldoGolsColors = []; 
        
        // Loop único para preencher todos os arrays
        dataForCharts.forEach(item => {
            labels.push(item[labelsKey]);
            
            // Gráfico de Gols
            golsFeitos.push(item[golsFeitosKey] || 0); // Adicionado || 0 para segurança
            golsSofridos.push(item[golsSofridosKey] || 0);
            
            // Gráfico de Desempenho (V/E/D)
            vitorias.push(item[vitoriasKey] || 0);
            empates.push(item[empatesKey] || 0);
            derrotas.push(item[derrotasKey] || 0);

            // Gráfico de Saldo de Gols
            // 💡 USANDO A CHAVE CONSISTENTE: Não é mais necessário o cálculo condicional,
            // pois o Service já retorna 'saldo_gols' em ambos os modos (RankingService.php)
            const saldo = parseInt(item[saldoGolsKey] || 0); 
            saldoGols.push(saldo);
            
            // Cor condicional: Verde para positivo, Vermelho para negativo, Amarelo para zero
            if (saldo > 0) {
                saldoGolsColors.push('rgba(0, 153, 51, 0.8)');
            } else if (saldo < 0) {
                saldoGolsColors.push('rgba(220, 53, 69, 0.8)');
            } else {
                saldoGolsColors.push('rgba(255, 193, 7, 0.8)');
            }
        });
        
        // =================================================================
        // 1. Configuração do Gráfico de Gols 
        // =================================================================

        const ctxGols = document.getElementById('golsChart');
        if (ctxGols) {
            new Chart(ctxGols.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Gols Feitos (GP)',
                            data: golsFeitos,
                            backgroundColor: 'rgba(0, 102, 0, 0.8)', 
                            borderColor: 'rgba(0, 102, 0, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Gols Sofridos (GC)',
                            data: golsSofridos,
                            backgroundColor: 'rgba(255, 99, 132, 0.8)', 
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            ticks: { color: '#adb5bd' }, 
                            grid: { color: 'rgba(255, 255, 255, 0.1)' } 
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#adb5bd' }, 
                            grid: { color: 'rgba(255, 255, 255, 0.1)' }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: { color: '#adb5bd' }
                        },
                        title: {
                            display: true,
                            text: 'Comparativo de Gols (Ataque vs. Defesa)',
                            color: '#ffc107'
                        }
                    }
                }
            });
        }
        
        // =================================================================
        // 2. Configuração do Gráfico de Desempenho (V/E/D)
        // =================================================================

        const ctxDesempenho = document.getElementById('desempenhoChart');
        if (ctxDesempenho) {
            new Chart(ctxDesempenho.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Vitórias (V)',
                            data: vitorias,
                            backgroundColor: 'rgba(50, 205, 50, 0.8)', 
                            borderColor: 'rgba(50, 205, 50, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Empates (E)',
                            data: empates,
                            backgroundColor: 'rgba(255, 193, 7, 0.8)', 
                            borderColor: 'rgba(255, 193, 7, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Derrotas (D)',
                            data: derrotas,
                            backgroundColor: 'rgba(220, 53, 69, 0.8)', 
                            borderColor: 'rgba(220, 53, 69, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true, 
                            ticks: { color: '#adb5bd' }, 
                            grid: { color: 'rgba(255, 255, 255, 0.1)' } 
                        },
                        y: {
                            stacked: true, 
                            beginAtZero: true,
                            ticks: { color: '#adb5bd' }, 
                            grid: { color: 'rgba(255, 255, 255, 0.1)' }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: { color: '#adb5bd' }
                        },
                        title: {
                            display: true,
                            text: 'Desempenho Total (Vitórias, Empates, Derrotas)', 
                            color: '#ffc107' 
                        }
                    }
                }
            });
        }
        
        // =================================================================
        // 3. Configuração do Gráfico de Saldo de Gols (NOVO)
        // =================================================================
        const ctxSaldoGols = document.getElementById('saldoGolsChart');
        if (ctxSaldoGols) {
            new Chart(ctxSaldoGols.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Saldo de Gols',
                            data: saldoGols,
                            backgroundColor: saldoGolsColors,
                            borderColor: saldoGolsColors.map(color => color.replace('0.8', '1')),
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            ticks: { color: '#adb5bd' }, 
                            grid: { color: 'rgba(255, 255, 255, 0.1)' } 
                        },
                        y: {
                            ticks: { color: '#adb5bd' }, 
                            grid: { color: 'rgba(255, 255, 255, 0.1)' }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: { color: '#adb5bd' }
                        },
                        title: {
                            display: true,
                            text: 'Saldo Líquido de Gols (GP - GC)', 
                            color: '#ffc107' 
                        }
                    }
                }
            });
        }
        <?php endif; ?>
    });
</script>