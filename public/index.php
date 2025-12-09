<?php

// -----------------------------------------------------------
// 1. Configuração e Autoloading
// -----------------------------------------------------------

date_default_timezone_set('America/Sao_Paulo');

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Carrega o Autoloader do Composer
// Aqui, para subir em produção, precisa ser o caminho correto: '/vendor/autoload.php' -->> APENAS NO SERVIDOR - 
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

if (!isset($_ENV['APP_ENV']) || empty($_ENV['APP_ENV'])) {
    putenv('APP_ENV=prod'); 
    $_ENV['APP_ENV'] = 'prod'; // Garante que o $_ENV também seja setado para o Helper
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
$router->add('/jogos/{ano}', 'CopasController@jogos');
$router->add(uri: '/manutencao/confederacoes', controllerMethod: 'ConfederationController@save');
$router->add(uri: '/manutencao/confederacoes/editar/{id}', controllerMethod: 'ConfederationController@save'); 
$router->add(uri: '/manutencao/confederacoes/excluir/{id}', controllerMethod: 'ConfederationController@delete');
$router->add(uri: '/manutencao/confederacoes/export', controllerMethod: 'ConfederationController@exportData');


// Despacha a requisição (faz o Controller/Método rodar)
$router->dispatch();

// FIM DO SCRIPT