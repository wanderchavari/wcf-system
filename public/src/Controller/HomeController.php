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

        $campeoes = [];
        if (!empty($campeoesPorTitulo)) {
            $selCampea = [];
            foreach ($campeoesPorTitulo as $titulos => $campeao) {
                if (isset($campeoes[$titulos])) {
                    array_push($campeoes[$titulos], $campeao);
                } else {
                    $campeoes[$titulos] = $campeao;
                }
            }
        }
        //$this->dd($campeoes);

        $participacoes = [];
        if (!empty($participacoesData)) {
            $item = [];
            foreach ($participacoesData as $participacao) {
                $item['Selecao'] = Helper::h($participacao['Selecao']);
                $item['Participacoes'] = (int)$participacao['Participacoes'];
                if (!empty($item)) {
                    $participacoes[] = $item;
                    $item = []; // Reseta o item para a próxima iteração
                }
            }
        }

        // 2. DADOS PARA A VIEW (ViewModel)
        $viewData = [
            'titulo' => 'Campeões Históricos',
            'campeoes' => $campeoes,
            'pageTitle' => 'Histórico de Campeões da Copa do Mundo',
            'pageSubtitle' => 'Análise das seleções mais vitoriosas e suas conquistas.',
            'pageDetail' => null,
            'participacoesData' => $participacoes,
        ];

        // 3. CHAMA O MÉTODO DE RENDERIZAÇÃO
        $this->render('home', $viewData);
    }

}