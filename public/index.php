<?php

// -----------------------------------------------------------
// 1. Configuração e Autoloading
// -----------------------------------------------------------

date_default_timezone_set('America/Sao_Paulo');

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Carrega o Autoloader do Composer
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    echo "<h1>Erro Crítico: Autoloader do Composer não encontrado.</h1>";
    exit;
}

// Carrega as variáveis de ambiente (.env)
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->safeLoad();
} catch (\Exception $e) {
    error_log("Erro ao carregar .env: " . $e->getMessage());
}

// -----------------------------------------------------------
// 2. Roteamento
// -----------------------------------------------------------

use Core\Router;

// Inicializa o Roteador
$router = new Router();

// Define as Rotas da Aplicação
$router->add('/', 'HomeController@index');
// 💡 Rotas de exemplo que podemos criar depois
$router->add('/copas', 'CopasController@index'); 
$router->add('/copas/{ano}', 'CopasController@detalhes'); 
$router->add('/admin', 'AdminController@dashboard');
$router->add('/ranking', 'RankingController@ranking');
$router->add('/ranking/{year}', 'RankingController@ranking');
$router->add('/estatisticas', 'AnaliseController@estatisticas');

// Despacha a requisição (faz o Controller/Método rodar)
$router->dispatch();

// FIM DO SCRIPT