<?php

namespace App\Controller;

use Backend\Service\RankingService;
use Core\BaseController;
use Core\Helper;

class AnaliseController extends BaseController
{

    /**
     * Exibe a página de Estatísticas Detalhadas com os gráficos.
     */
    public function estatisticas(?int $year = null)
    {
        $ranking = []; 
        $pointsData = []; 
        $performanceData = []; 
        $golsFeitosSofridosData = []; 
        $saldoGolsData = []; 
        $golsMediaData = []; 
        try {

            // 1. Carrega os dados para montar os gráficos
            $ranking = $this->getRankingParamsAndData($year);
            $dados = $ranking['rankingData'];

            foreach ($dados as $item) {
                $pointsData[] = [
                    'selecao' => Helper::h($item['selecao']),
                    'sigla' => Helper::h($item['sigla']),
                    'pontos' => $item['pontos'],
                ];
                $performanceData[] = [
                    'selecao' => Helper::h($item['selecao']),
                    'sigla' => Helper::h($item['sigla']),
                    'jogos' => $item['jogos'],
                    'vitorias' => $item['vitorias'],
                    'empates' => $item['empates'],
                    'derrotas' => $item['derrotas'],
                ];
                $golsFeitosSofridosData[] = [
                    'selecao' => Helper::h($item['selecao']),
                    'sigla' => Helper::h($item['sigla']),
                    'gols_feitos' => $item['gols_feitos'],
                    'gols_sofridos' => $item['gols_sofridos'],
                ];
                $saldoGolsData[] = [
                    'selecao' => Helper::h($item['selecao']),
                    'sigla' => Helper::h($item['sigla']),
                    'saldo_gols' => $item['saldo_gols'],
                ];
                $golsMediaData[] = [
                    'selecao' => Helper::h($item['selecao']),
                    'sigla' => Helper::h($item['sigla']),
                    'Gols_Feitos_Media' => $item['jogos'] > 0 ? $item['gols_feitos'] / $item['jogos'] : 0,
                ];
            }
            //$this->dd($golsMediaData); 
            
        } catch (\Exception $e) {
            error_log("Erro no AnaliseController ao buscar estatísticas: " . $e->getMessage()); 
            $pointsData = []; 
            $performanceData = [];
            $golsFeitosSofridosData = [];
            $saldoGolsData = [];
            $golsMediaData = [];
        }

        $mediaClassificacaoData = $this->rankingService->getMediaClassificacaoFinal();

        $viewData = [
            'pageTitle' => 'Estatísticas Detalhadas da Copa do Mundo',
            'pageSubtitle' => 'Comparação entre as seleções em diferentes métricas.',
            'pageDetail' => null,
            
            // Passa todos os datasets para a View
            'pointsData' => $pointsData,
            'performanceData' => $performanceData,
            'golsFeitosSofridosData' => $golsFeitosSofridosData,
            'saldoGolsData' => $saldoGolsData,
            'golsMediaData' => $golsMediaData,
            'mediaClassificacaoData' => $mediaClassificacaoData,
        ];

        // RENDERIZA A NOVA VIEW
        $this->render('analise/estatisticas', $viewData);
    }
}