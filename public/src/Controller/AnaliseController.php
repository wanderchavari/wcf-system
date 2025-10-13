<?php
// public/src/Controller/AnaliseController.php

namespace App\Controller;

use Core\BaseController;

class AnaliseController extends BaseController 
{

    public function ranking(?int $year = null)
    {

        // 1. Verifica os modos de acesso (Query String)
        $listMode = $_GET['listagem'] ?? null;
        $rankingData = [];
        $viewFile = 'analise/ranking';
        
        // 2. Lógica de Busca e Títulos
        if ($listMode !== null) {
            if ($listMode != 'old' && $listMode != 'modern' && $listMode != 'all') {
                $listMode = 'all'; // Valor padrão se inválido
            }
            if ($listMode === 'all') {
                $pageTitle = 'Ranking Histórico Geral (Todas as Copas)';
                $pageSubtitle = "Análise consolidada de todas as edições da Copa do Mundo";
            } elseif ($listMode === 'old') {
                $pageTitle = "Análise Estatística das Copas antes de 1994";
                $pageSubtitle = "Copas entre 1930 e 1990 (2 pontos por vitória)";
            } elseif ($listMode === 'modern') {
                $pageTitle = "Análise Estatística das Copas a partir de 1994";
                $pageSubtitle = "Copas entre 1994 e 2022 (3 pontos por vitória)";
            }
            // Se for listagem (ex: ?listagem=all)
            $rankingData = $this->analiseService->getRanking(null,$listMode);
            // Podemos usar uma view separada ou adaptar ranking.php para listagem
            // Para simplicidade, vamos para a próxima funcionalidade primeiro.
        } elseif ($year !== null) {
            // Se for Ranking Específico por Ano (/analise/{year})
            $pageTitle = "Detalhamento da Copa de {$year}";
            $pageSubtitle = "Estatísticas detalhadas da edição.";
            $rankingData = $this->analiseService->getRanking($year);
        } else {
            // Padrão: Ranking Histórico Geral (/analise)
            $pageTitle = "Ranking Histórico Geral";
            $pageSubtitle = "Análise consolidada de todas as edições da Copa do Mundo.";
            $rankingData = $this->analiseService->getRanking(null);
        }

        // //teste
        // header('Content-Type: application/json');
        // http_response_code(200);
        // echo json_encode($rankingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        // 3. Renderiza a view
        $this->render($viewFile, [
            'pageTitle' => $pageTitle,
            'pageSubtitle' => $pageSubtitle,
            'rankingData' => $rankingData,
            'year' => $year,
            'listMode' => $listMode,
            'isGeneralRanking' => ($year === null && $listMode === null)
        ]);
    }

}