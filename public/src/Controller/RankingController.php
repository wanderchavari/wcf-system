<?php
// public/src/Controller/RankingController.php

namespace App\Controller;

use Core\BaseController;

class RankingController extends BaseController 
{

    public function ranking(?int $year = null)
    {
        $viewFile = 'ranking/ranking';

        $params = $this->getRankingParamsAndData($year); 

        $viewData = [
            'pageTitle' => $params['pageTitle'],
            'pageSubtitle' => $params['pageSubtitle'],
            'pageDetail' => $params['pageDetail'],
            'rankingData' => $params['rankingData'],
            'year' => $params['year'],
            'listMode' => $params['listMode'],
            'isGeneralRanking' => $params['isGeneralRanking'],
        ];

        $this->render($viewFile, $viewData);
        
    }

}