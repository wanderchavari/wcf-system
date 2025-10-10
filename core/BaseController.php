<?php

namespace Core;

use Backend\Service\WorldCupService;

abstract class BaseController
{
    protected WorldCupService $worldCupService;

    public function __construct()
    {
        // Instancia serviços comuns aqui, como o WorldCupService
        $this->worldCupService = new WorldCupService();
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
}