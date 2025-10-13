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

<div class="container my-5">
    <h3 class="text-center text-light fw-bold mb-4">
        Participações dos Países Campeões Históricos
    </h3>

    <div class="row justify-content-center">
        <div class="col-md-8 bg-dark p-4 rounded shadow">
            <canvas id="participacoesCampeoesChart"></canvas>
            <p class="text-muted text-center mt-3 small">
                O tamanho da fatia representa o total de participações em Copas do Mundo de cada país que já conquistou o título.
            </p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 💡 INJEÇÃO DE DADOS DO PHP
        const championsData = <?php echo json_encode($participacoesData ?? [], JSON_UNESCAPED_UNICODE); ?>;

        if (championsData && championsData.length > 0) {
            const labels = [];
            const participacoes = [];
            
            championsData.forEach(item => {
                // Apenas Seleção e Participações são retornados
                labels.push(item.Selecao); 
                participacoes.push(item.Participacoes);
            });
            
            // Paleta de cores para os campeões
            const backgroundColors = [
                '#FFD700', // Ouro (Brasil)
                '#A9A9A9', // Cinza Escuro (Alemanha)
                '#6495ED', // Azul Milenar (Argentina)
                '#0066FF', // Azul Escuro (Uruguai)
                '#B22222', // Tijolo (França)
                '#008000', // Verde (Itália)
                '#FF6347', // Tomate (Inglaterra)
                '#4682B4'  // Aço (Espanha)
            ];

            const ctx = document.getElementById('participacoesCampeoesChart');
            
            if (!ctx) {
                console.error("Elemento 'participacoesCampeoesChart' não encontrado no DOM.");
                return;
            }
            
            new Chart(ctx, {
                type: 'pie', 
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total de Participações',
                        data: participacoes,
                        backgroundColor: backgroundColors.slice(0, labels.length),
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#adb5bd' // Cor do texto da legenda
                            }
                        },
                        title: {
                            display: true,
                            text: 'Participações (Contagem de Anos)',
                            color: '#ffc107',
                            font: { size: 16 }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (context.parsed !== null) {
                                        label = context.label + ': ' + context.parsed + ' Participações';
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>