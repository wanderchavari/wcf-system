<?php

namespace App\Controller; // Usamos o namespace App\ que mapeamos para public/src

use Backend\Service\WorldCupService;
use Core\Helper;
use Core\BaseController; 

class HomeController extends BaseController
{
    
    /**
     * Executa a lógica para a página inicial e renderiza a view.
     */
    public function index()
    {
        // 1. LÓGICA DE DADOS (Business Logic)
        try {
            $campeoesPorTitulo = $this->worldCupService->getCampeoesPorTitulo();
            $participacoesData = $this->worldCupService->getParticipacoes();
        } catch (\Exception $e) {
            // O serviço já logou o erro, apenas garante um array vazio
            $campeoesPorTitulo = []; 
            $participacoesData = [];
        }

        // 2. DADOS PARA A VIEW (ViewModel)
        $viewData = [
            'titulo' => 'Campeões Históricos',
            'campeoes' => $campeoesPorTitulo,
            'pageTitle' => 'Histórico de Campeões da Copa do Mundo',
            'pageSubtitle' => 'Análise das seleções mais vitoriosas e suas conquistas.',
            'pageDetail' => null,
            'participacoesData' => $participacoesData,
        ];

        // 3. CHAMA O MÉTODO DE RENDERIZAÇÃO
        $this->render('home', $viewData);
    }

}