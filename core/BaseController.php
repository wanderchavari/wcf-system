<?php

namespace Core;

use Backend\Service\WorldCupService;
use Backend\Service\RankingService;
use Backend\Service\GameService;

abstract class BaseController
{
    protected WorldCupService $worldCupService;
    protected RankingService $rankingService;
    protected GameService $gameService;

    public function __construct()
    {
        // Instancia serviços comuns aqui, como o WorldCupService
        $this->worldCupService = new WorldCupService();
        $this->rankingService = new RankingService();
        $this->gameService = new GameService();
    }

    /**
     * Helper para renderizar a view com os templates, injetando dados globais.
     */
    protected function render(string $view, array $data = []): void
    {
        // 1. DADOS GLOBAIS (O que todos os headers/footers precisam)
        $globalData = [
            'ambiente' => Helper::isDevEnvironment() ? 'Desenvolvimento' : 'Produção',
            'versao' => Helper::getAppVersion(),
            
            // 💡 Lógica do Menu: Busca os dados de copas aqui!
            'torneiosParaMenu' => $this->worldCupService->getTorneiosParaMenu(), 
        ];

        // Mescla dados específicos do Controller com dados globais
        $mergedData = array_merge($globalData, $data);
        
        // Converte o array $data em variáveis
        extract($mergedData);

        // Caminho da view (subindo do Core/Controller para public/src/views/)
        $baseViewPath = __DIR__ . '/../public/src/views/'; 

        require $baseViewPath . 'header.php';
        require $baseViewPath . $view . '.php';
        require $baseViewPath . 'footer.php';
    }

    /**
     * Exibe o conteúdo de uma variável formatada e encerra a execução.
     * @param mixed $var A variável a ser inspecionada.
     */
    protected function dd($var): void
    {
        echo "<!DOCTYPE html><html><head>";
        // Aplica um estilo básico para escurecer o fundo, já que a página não renderizou o header
        echo "<style>body { background-color: #333; color: #fff; padding: 20px; font-family: monospace; }</style>";
        echo "</head><body>";
        
        echo "<h1>DEBUG DUMP (Execução Abortada)</h1>";
        echo "<pre>";
        print_r($var); // Use print_r para arrays, var_dump é mais verboso, mas também funciona.
        echo "</pre>";
        
        echo "</body></html>";
        die();
    }

    /**
     * Lógica comum para determinar o modo de ranking (geral/era/ano) 
     * e buscar os dados e títulos da página.
     * * @param int|null $year O ano da copa se vier da rota.
     * @return array Contém listMode, rankingData, pageTitle, pageSubtitle.
     */
    protected function getRankingParamsAndData(?int $year = null): array
    {
        // 1. Verifica os modos de acesso (Query String)
        $listMode = $_GET['listagem'] ?? null;
        $rankingData = [];
        $pageTitle = "";
        $pageSubtitle = "";

        // Se for listagem (ex: ?listagem=all, old, modern)
        // if ($listMode != 'old' && $listMode != 'modern' && $listMode != 'all') {
        //     $listMode = 'all'; // Valor padrão se inválido
        // }
        
        // 2. Lógica de Busca e Títulos
        if ($listMode !== null) {
            // Define Títulos e Busca Dados baseados no listMode
            if ($listMode === 'all') {
                $pageTitle = 'Ranking Histórico Geral (Todas as Copas)';
                $pageSubtitle = "Análise consolidada de todas as edições da Copa do Mundo";
                $pageDetail = '';
            } elseif ($listMode === 'old') {
                $pageTitle = "Análise Estatística das Copas até 1990";
                $pageSubtitle = "Copas entre 1930 e 1990 (2 pontos por vitória)";
                $pageDetail = "1930 - 1934 - 1938 - 1950 - 1954 - 1958 - 1962 - 1966 - 1970 - 1974 - 1978 - 1982 - 1986 - 1990";
            } elseif ($listMode === 'modern') {
                $pageTitle = "Análise Estatística das Copas a partir de 1994";
                $pageSubtitle = "Copas entre 1994 e 2022 (3 pontos por vitória)";
                $pageDetail = "1994 - 1998 - 2002 - 2006 - 2010 - 2014 - 2018 - 2022";
            }
            $rankingData = $this->rankingService->getRanking(null, $listMode);

        } elseif ($year !== null) {
            // Se for Ranking Específico por Ano (/ranking/{year})
            $pageTitle = "Detalhamento da Copa de {$year}";
            $pageSubtitle = "Estatísticas detalhadas da edição.";
            $pageDetail = '';
            $rankingData = $this->rankingService->getRanking($year);
            $listMode = null; // Garante que listMode seja null para ranking por ano
            
        } else {
            // Padrão: Ranking Histórico Geral (/ranking)
            $pageTitle = "Ranking Histórico Geral";
            $pageSubtitle = "Análise consolidada de todas as edições da Copa do Mundo.";
            $pageDetail = '';
            $rankingData = $this->rankingService->getRanking(null);
            $listMode = null;
        }

        return [
            'pageTitle' => $pageTitle,
            'pageSubtitle' => $pageSubtitle,
            'pageDetail' => $pageDetail,
            'rankingData' => $rankingData,
            'year' => $year, // O ano para filtros
            'listMode' => $listMode,
            'isGeneralRanking' => ($listMode !== null || $year === null), // Flag para a View
        ];
    }

}