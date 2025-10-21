<?php
// public/src/Controller/CopasController.php

namespace App\Controller;

use Core\BaseController;
use Core\Helper;

class CopasController extends BaseController 
{
    // ... listar() ...

    public function index() // Mapeado para a rota /copas
    {
        // 1. Busca a lista de torneios
        $torneios = $this->worldCupService->getAllTorneios();

        $campeonatos = [];
        if (!empty($torneios)) {
            $item = [];
            foreach ($torneios as $copa) {
                $item['ano_torneio'] = $copa['ano_torneio'];
                $item['sede'] = Helper::h($copa['sede']);
                $item['campeao'] = Helper::h($copa['campeao']);
                if (!empty($item)) {
                    $campeonatos[] = $item;
                    $item = []; // Reseta o item para a próxima iteração
                }
            }
        }
        //$this->dd($campeonatos);
        
        $viewData = [
            'titulo' => 'Listagem de Copas',
            'pageTitle' => '⚽️ Todas as Edições da Copa do Mundo', 
            'pageSubtitle' => 'Navegue por todas as sedes e campeões desde 1930.',
            'pageDetail' => 'Total de edições: ' . count($torneios),
            
            // Dados para a View (a lista que será iterada)
            'torneios' => $campeonatos, 
        ];
        
        // Assumimos que a view se chamará copas/index.php
        $this->render('copas/index', $viewData); 
    }

    public function detalhes(string $ano)
    {
        // 1. LÓGICA DE DADOS: Busca os dados reais da Copa
        // Assumindo que o seu WorldCupService está injetado em $this->worldCupService
        // O método deve retornar o array de resultados (posicao, selecao, etc.)
        $participacoes = $this->worldCupService->getChampionByYear((int)$ano);
        $ranking = $this->worldCupService->getTorneioRankingByYear((int)$ano);
        
        // 2. Prepara variáveis de exibição
        $copaData = [
            'sede' => 'A definir',
            'campeao' => null,
            'vice' => null,
            'terceiro' => null,
        ];

        // Variável de controle: se a Copa existe (tem sede ou resultados)
        $isFound = !empty($participacoes);
        
        if ($isFound) {
        // Processa o array para extrair as posições e sede
        // 💡 O FOREACH AGORA FUNCIONA CORRETAMENTE!
        foreach ($participacoes as $part) { 
            // Sua lógica de seleção de nome e atribuição de sede/posições está correta aqui.
            $selecaoNome = $part['selecao_historica'] ?? $part['selecao_atual'];
            
            $copaData['sede'] = $part['sede'] ?? $copaData['sede'];

            switch ((int)$part['posicao']) {
                case 1:
                    $copaData['campeao'] = $selecaoNome;
                    break;
                case 2:
                    $copaData['vice'] = $selecaoNome;
                    break;
                case 3:
                    $copaData['terceiro'] = $selecaoNome;
                    break;
            }
        }
    }
        
        // 3. Prepara os dados para a View
        $pageTitle = $isFound 
            ? "🏆 Copa do Mundo de {$ano} em {$copaData['sede']}" 
            : "Edição não encontrada: {$ano}";
            
        $pageSubtitle = $isFound
            ? "País sede: {$copaData['sede']}"
            : "Desculpe, não conseguimos encontrar dados para esta edição.";
            
        $pageSubtitleClass = $isFound
            ? 'subtitle-destaque'
            : '';

        $pageDetail = $isFound
            ? null
            : "Verifique o menu 'Copas do Mundo' para as edições cadastradas e tente novamente.";

        $viewData = [
            'titulo' => "Copa de {$ano}",
            'ano' => $ano,
            'isFound' => $isFound,
            
            // Variáveis de exibição que você usa em detalhes.php
            'campeao' => $copaData['campeao'],
            'vice' => $copaData['vice'],
            'terceiro' => $copaData['terceiro'],
            
            // Variáveis do Layout (Header)
            'pageTitle' => $pageTitle, 
            'pageSubtitle' => $pageSubtitle,
            'pageDetail' => $pageDetail,
            'pageSubtitleClass' => $pageSubtitleClass,
            'ranking' => $ranking,
        ];
        
        $this->render('copas/detalhes', $viewData);
    }
}