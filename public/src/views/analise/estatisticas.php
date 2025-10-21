<?php 
// 💡 NOTA: Certifique-se de que a biblioteca Chart.js está incluída no seu layout principal.
// As variáveis $golsFeitosSofridosData, $saldoGolsData, $golsMediaData estão disponíveis aqui.

$pointsDataJson = json_encode($pointsData ?? [], JSON_UNESCAPED_UNICODE);
$performanceDataJson = json_encode($performanceData ?? [], JSON_UNESCAPED_UNICODE);
$golsFeitosSofridosDataJson = json_encode($golsFeitosSofridosData ?? [], JSON_UNESCAPED_UNICODE);
$saldoGolsDataJson = json_encode($saldoGolsData ?? [], JSON_UNESCAPED_UNICODE);
$golsMediaDataJson = json_encode($golsMediaData ?? [], JSON_UNESCAPED_UNICODE);
$mediaClassificacaoDataJson = json_encode($mediaClassificacaoData ?? [], JSON_UNESCAPED_UNICODE);
?>

<div class="container my-5">
    
    <h2 class="mb-4 text-light">Métricas de Desempenho Histórico</h2>

    <div class="row justify-content-center mb-5">
        <div class="col-md-6">
            <label for="chartSelector" class="form-label text-light fw-bold">Selecione a Estatística para Visualizar:</label>
            <select id="chartSelector" class="form-select bg-dark text-light border-secondary">
                <option value="points" selected>Pontos</option>
                <option value="performance">Desempenho (V/E/D)</option>
                <option value="goals">Gols Feitos vs Gols Sofridos</option>
                <option value="saldo">Saldo de Gols</option>
                <option value="gols_media">Média de Gols Feitos por Jogo</option>
                <option value="media_classificacao">Média de Classificação Final</option>
                </select>
        </div>
    </div>
    
    <div class="chart-container-wrapper">
        
        <div id="pointsChartContainer" class="bg-dark p-4 rounded shadow" style="display: block;">
            <canvas id="pointsChart"></canvas>
        </div>
        
        <div id="performanceChartContainer" class="bg-dark p-4 rounded shadow" style="display: block;">
            <canvas id="performanceChart"></canvas>
        </div>
        
        <div id="goalsChartContainer" class="bg-dark p-4 rounded shadow" style="display: block;">
            <canvas id="golsFeitosSofridosChart"></canvas>
        </div>
        
        <div id="saldoChartContainer" class="bg-dark p-4 rounded shadow" style="display: none;">
            <canvas id="saldoGolsChart"></canvas>
        </div>
        
        <div id="golsMediaChartContainer" class="bg-dark p-4 rounded shadow" style="display: none;">
            <canvas id="golsMediaChart"></canvas>
        </div>

        <div id="mediaClassificacaoChartContainer" class="bg-dark p-4 rounded shadow" style="display: none;"> 
            <canvas id="mediaClassificacaoChart"></canvas>
        </div>
        
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. INJEÇÃO DE DADOS JSON DO PHP
        const pointsData = JSON.parse('<?= $pointsDataJson ?>');
        const performanceData = JSON.parse('<?= $performanceDataJson ?>');
        const golsFeitosSofridosData = JSON.parse('<?= $golsFeitosSofridosDataJson ?>');
        const saldoGolsData = JSON.parse('<?= $saldoGolsDataJson ?>');
        const golsMediaData = JSON.parse('<?= $golsMediaDataJson ?>');
        const mediaClassificacaoData = JSON.parse('<?= $mediaClassificacaoDataJson; ?>');

        let charts = {}; // Objeto para armazenar as instâncias dos gráficos

        // =================================================================
        // 2. FUNÇÃO DE RENDERIZAÇÃO CENTRALIZADA (Evita renderizar mais de uma vez)
        // =================================================================

        function renderChart(chartId, type, data, options) {
            const ctx = document.getElementById(chartId);
            if (ctx && charts[chartId]) {
                charts[chartId].destroy(); 
                delete charts[chartId];
            }
            if (ctx) {
                charts[chartId] = new Chart(ctx, { type, data, options });
                charts[chartId].resize(); 
            }
        }
        
        // =================================================================
        // 3. DEFINIÇÃO E RENDERIZAÇÃO DOS DATASETS
        // =================================================================
        const alturaPorItem = 30; 
        const alturaMinima = 300;

        // --- Gráfico 1: Pontos ---
        if (pointsData.length > 0) {
            const labels = pointsData.map(item => item.selecao);
            const pontos = pointsData.map(item => item.pontos);
            
            const alturaCalculada = Math.max(pointsData.length * alturaPorItem + 100, alturaMinima);
            const container = document.getElementById('pointsChartContainer');
            if (container) {
                container.style.height = `${alturaCalculada}px`;
                const canvas = document.getElementById('pointsChart');
                if (canvas) {
                    canvas.style.height = `${alturaCalculada}px`;
                }
            }

            const pointsColors = pontos.map(p => {
                                                    if (p > 100) {
                                                        return 'rgba(40, 167, 69, 0.9)'; // Exemplo: Verde Escuro (Excelente)
                                                    } 
                                                    else if (p >= 50) {
                                                        return 'rgba(255, 193, 7, 0.8)'; // Exemplo: Amarelo/Ouro (Médio)
                                                    } 
                                                    else {
                                                        return 'rgba(220, 53, 69, 0.8)'; // Exemplo: Vermelho (Baixo)
                                                    }
                                                });
            
            const data1 = {
                labels: labels,
                datasets: [{
                    label: 'Pontos',
                    data: pontos,
                    backgroundColor: pointsColors,
                    borderWidth: 1
                }]
            };
            
            const options1 = {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: { ticks: { color: '#adb5bd' }, grid: { color: 'rgba(255, 255, 255, 0.1)' } },
                    y: { ticks: { color: '#adb5bd' }, grid: { color: 'rgba(255, 255, 255, 0.1)' } }
                },
                plugins: {
                    legend: { labels: { color: '#adb5bd' } },
                    title: { display: true, text: 'Pontos Ganhos (Total Histórico)', color: '#ffc107' }
                }
            };

            renderChart('pointsChart', 'bar', data1, options1);
        }

        // --- Gráfico 2: Performance ---
        if (performanceData.length > 0) {
            const labels = performanceData.map(item => item.selecao);
            const jogos = performanceData.map(item => item.jogos);
            const vitorias = performanceData.map(item => item.vitorias);
            const empates = performanceData.map(item => item.empates);
            const derrotas = performanceData.map(item => item.derrotas);
            
            const alturaCalculada = Math.max(performanceData.length * alturaPorItem + 100, alturaMinima);
            const container = document.getElementById('performanceChartContainer');
            if (container) {
                container.style.height = `${alturaCalculada}px`;
                const canvas = document.getElementById('performanceChart');
                if (canvas) {
                    canvas.style.height = `${alturaCalculada}px`;
                }
            }
            
            const data2 = {
                labels: labels,
                datasets: [
                    {
                        label: 'Vitórias',
                        data: vitorias,
                        backgroundColor: 'rgba(40, 167, 69, 0.8)', // Verde
                    },
                    {
                        label: 'Empates',
                        data: empates,
                        backgroundColor: 'rgba(255, 193, 7, 0.8)', // Amarelo
                    },
                    {
                        label: 'Derrotas',
                        data: derrotas,
                        backgroundColor: 'rgba(220, 53, 69, 0.8)', // Vermelho
                    },
                    // Opcional: Adicionar Jogos como uma barra não empilhada ou total
                    // Se for empilhado, ele fará o total de V+E+D, mas se o array já tiver o campo Total_Jogos, pode ser útil.
                    // Para manter a visualização em pilhas (stacked) de V+E+D, o Total_Jogos não é necessário como dataset.
                ]
            };
            
            const options2 = { 
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: { // Eixo X (Valores: 0, 1, 2...)
                        stacked: true, // Empilha as barras (V + E + D)
                        ticks: { color: '#adb5bd' }, 
                        grid: { color: 'rgba(255, 255, 255, 0.1)' } 
                    },
                    y: { // Eixo Y (Rótulos: SIGLA)
                        stacked: true, // O empilhamento deve ser em ambos os eixos para funcionar
                        ticks: { color: '#adb5bd' }, 
                        grid: { color: 'rgba(255, 255, 255, 0.1)' } 
                    }
                },
                plugins: {
                    legend: { labels: { color: '#adb5bd' } },
                    title: { 
                        display: true, 
                        text: 'Desempenho (Vitórias, Empates, Derrotas) - Total Histórico', 
                        color: '#ffc107' 
                    }
                }
            };
            
            // O tipo de gráfico permanece 'bar', mas o indexAxis o torna horizontal.
            renderChart('performanceChart', 'bar', data2, options2);
        }

        // --- Gráfico 3: Gols Feitos vs Sofridos ---
        if (golsFeitosSofridosData.length > 0) {
            const labels = golsFeitosSofridosData.map(item => item.selecao);
            const feitos = golsFeitosSofridosData.map(item => item.gols_feitos);
            const sofridos = golsFeitosSofridosData.map(item => item.gols_sofridos);
            
            const alturaCalculada = Math.max(golsFeitosSofridosData.length * alturaPorItem + 100, alturaMinima);
            const container = document.getElementById('goalsChartContainer');
            if (container) {
                container.style.height = `${alturaCalculada}px`;
                const canvas = document.getElementById('golsFeitosSofridosChart');
                if (canvas) {
                    canvas.style.height = `${alturaCalculada}px`;
                }
            }
            
            const data3 = {
                labels: labels,
                datasets: [
                    {
                        label: 'Gols Feitos',
                        data: feitos,
                        backgroundColor: 'rgba(255, 193, 7, 0.8)', // Amarelo/Ouro
                    },
                    {
                        label: 'Gols Sofridos',
                        data: sofridos,
                        backgroundColor: 'rgba(220, 53, 69, 0.8)', // Vermelho/Perigo
                    }
                ]
            };
            
            const options3 = { 
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: { stacked: true, ticks: { color: '#adb5bd' }, grid: { color: 'rgba(255, 255, 255, 0.1)' } },
                    y: { stacked: true, ticks: { color: '#adb5bd' }, grid: { color: 'rgba(255, 255, 255, 0.1)' } }
                },
                plugins: {
                    legend: { labels: { color: '#adb5bd' } },
                    title: { display: true, text: 'Gols Feitos vs Gols Sofridos (Total Histórico)', color: '#ffc107' }
                }
            };
            
            renderChart('golsFeitosSofridosChart', 'bar', data3, options3);
        }

        // --- Gráfico 4: Saldo de Gols ---
        if (saldoGolsData.length > 0) {
            const labels = saldoGolsData.map(item => item.selecao);
            const saldo = saldoGolsData.map(item => item.saldo_gols);
            
            const saldoGolsColors = saldo.map(s => s >= 0 ? 'rgba(40, 167, 69, 0.8)' : 'rgba(220, 53, 69, 0.8)'); // Verde para positivo, Vermelho para negativo
            
            const alturaCalculada = Math.max(saldoGolsData.length * alturaPorItem + 100, alturaMinima);
            const container = document.getElementById('saldoChartContainer');
            if (container) {
                container.style.height = `${alturaCalculada}px`;
                const canvas = document.getElementById('saldoGolsChart');
                if (canvas) {
                    canvas.style.height = `${alturaCalculada}px`;
                }
            }
            
            const data4 = {
                labels: labels,
                datasets: [{
                    label: 'Saldo de Gols',
                    data: saldo,
                    backgroundColor: saldoGolsColors,
                    borderWidth: 1
                }]
            };
            
            const options4 = {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: { ticks: { color: '#adb5bd' }, grid: { color: 'rgba(255, 255, 255, 0.1)' } },
                    y: { ticks: { color: '#adb5bd' }, grid: { color: 'rgba(255, 255, 255, 0.1)' } }
                },
                plugins: {
                    legend: { labels: { color: '#adb5bd' } },
                    title: { display: true, text: 'Saldo de Gols (Total Histórico)', color: '#ffc107' }
                }
            };

            renderChart('saldoGolsChart', 'bar', data4, options4);
        }
        
        // --- Gráfico 5: Média de Gols Feitos (NOVO) ---
        if (golsMediaData.length > 0) {
            console.log(golsMediaData);
            const labels = golsMediaData.map(item => item.selecao);
            const media = golsMediaData.map(item => parseFloat(item.Gols_Feitos_Media)); 
            
            const alturaCalculada = Math.max(golsMediaData.length * alturaPorItem + 100, alturaMinima);
            const container = document.getElementById('golsMediaChartContainer');
            if (container) {
                container.style.height = `${alturaCalculada}px`;
                const canvas = document.getElementById('golsMediaChart');
                if (canvas) {
                    canvas.style.height = `${alturaCalculada}px`;
                }
            }

            const data5 = {
                labels: labels,
                datasets: [{
                    label: 'Média de Gols Feitos por Jogo (GP/J)',
                    data: media,
                    backgroundColor: 'rgba(0, 123, 255, 0.8)', // Azul
                    borderWidth: 1
                }]
            };

            const options5 = {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: { ticks: { color: '#adb5bd' }, grid: { color: 'rgba(255, 255, 255, 0.1)' } },
                    y: { 
                        beginAtZero: true, 
                        ticks: { color: '#adb5bd' }, 
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    }
                },
                plugins: {
                    legend: { labels: { color: '#adb5bd' } },
                    title: { display: true, text: 'Média de Gols Feitos por Jogo (Total Histórico)', color: '#ffc107' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                valorBruto = context.raw;
                                return context.label + ': ' + parseFloat(context.raw).toFixed(2);
                            }
                        }
                    }
                }
            };

            renderChart('golsMediaChart', 'bar', data5, options5);
        }

        // --- Gráfico 6: Média de Classificação Final
        if (mediaClassificacaoData.length > 0) {
            const labels = mediaClassificacaoData.map(item => item.selecao); // Usando o nome completo
            const media = mediaClassificacaoData.map(item => item.media_classificacao);
            
            // Altura dinâmica
            const alturaPorItem = 25;
            const alturaMinima = 300;
            const alturaCalculada = Math.max(mediaClassificacaoData.length * alturaPorItem + 100, alturaMinima);

            const container = document.getElementById('mediaClassificacaoChartContainer');
            if (container) {
                container.style.height = `${alturaCalculada}px`;
                const canvas = document.getElementById('mediaClassificacaoChart');
                if (canvas) {
                    canvas.style.height = `${alturaCalculada}px`;
                }
            }

            const data6 = {
                labels: labels,
                datasets: [{
                    label: 'Média de Classificação Final (Menor é Melhor)',
                    data: media,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)', // Azul (Desempenho)
                    borderWidth: 1
                }]
            };
            
            const options6 = { 
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Barra Horizontal
                scales: {
                    x: { 
                        ticks: { color: '#adb5bd', precision: 1 }, // Precision para a média
                        grid: { color: 'rgba(255, 255, 255, 0.1)' } 
                    },
                    y: { 
                        ticks: { color: '#adb5bd' }, 
                        grid: { color: 'rgba(255, 255, 255, 0.1)' } 
                    }
                },
                plugins: {
                    legend: { labels: { color: '#adb5bd' } },
                    title: { display: true, text: 'Média Histórica de Classificação Final', color: '#ffc107' }
                }
            };
            
            renderChart('mediaClassificacaoChart', 'bar', data6, options6);
        }


        // =================================================================
        // 4. LÓGICA DE ALTERNÂNCIA (Exibição/Ocultação)
        // =================================================================
        const chartSelector = document.getElementById('chartSelector');
        const containers = {
            'points': document.getElementById('pointsChartContainer'),
            'performance': document.getElementById('performanceChartContainer'),
            'goals': document.getElementById('goalsChartContainer'),
            'saldo': document.getElementById('saldoChartContainer'),
            'gols_media': document.getElementById('golsMediaChartContainer'),
            'media_classificacao': document.getElementById('mediaClassificacaoChartContainer')
        };
        
        function updateChartVisibility() {
            const selectedChart = chartSelector.value;
            for (const key in containers) {
                if (containers[key]) {
                    containers[key].style.display = (key === selectedChart) ? 'block' : 'none';
                }
            }
        }

        if (chartSelector) {
            chartSelector.addEventListener('change', updateChartVisibility);
            // Garante que o estado inicial (goals) é exibido
            updateChartVisibility(); 
        } else {
             console.error("Seletor de gráficos não encontrado.");
        }
    });
</script>