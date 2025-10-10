<?php

namespace Core;

class Router
{
    private array $routes = [];

    /**
     * Define uma rota GET.
     * @param string $uri A URI que o usuário acessa (ex: '/')
     * @param string $controllerMethod O Controller e método a ser chamado (ex: 'HomeController@index')
     */
    public function add(string $uri, string $controllerMethod): void
    {
        // Armazena a rota no mapa
        // Ex: $this->routes['/'] = 'HomeController@index'
        $this->routes[$uri] = $controllerMethod;
    }

    /**
     * Processa a URI atual e executa o Controller/Método correspondente.
     */
    public function dispatch(): void
    {
        $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        $uri = $uri === '' ? '/' : $uri;

        // 1. Procura a rota estática (ex: '/')
        if (array_key_exists($uri, $this->routes)) {
            $this->executeRoute($this->routes[$uri]);
            return;
        }

        // 2. Procura a rota dinâmica (com parâmetros como {ano})
        foreach ($this->routes as $routePattern => $controllerMethod) {
            
            // Converte a rota de pattern (ex: /copas/{ano}) para regex
            $regex = $this->createRegexFromRoute($routePattern);
            
            if (preg_match($regex, $uri, $matches)) {
                
                // Extrai os nomes dos parâmetros (ex: ['ano'])
                $paramNames = $this->extractParamNames($routePattern);
                
                // Cria um array associativo de parâmetros (ex: ['ano' => 2022])
                $params = array_combine($paramNames, array_slice($matches, 1));
                
                $this->executeRoute($controllerMethod, $params);
                return;
            }
        }

        // 3. Se não encontrou, exibe 404
        $this->handleNotFound();
    }

    private function createRegexFromRoute(string $routePattern): string
    {
        // Substitui {param} por um grupo de captura (ex: ([^/]+))
        // O $1 garante que o conteúdo do grupo seja salvo.
        $regex = preg_replace('/\{([a-zA-Z0-9]+)\}/', '([^/]+)', $routePattern);
        return "#^$regex$#"; // Delimitadores #
    }

    private function extractParamNames(string $routePattern): array
    {
        preg_match_all('/\{([a-zA-Z0-9]+)\}/', $routePattern, $matches);
        return $matches[1] ?? [];
    }

    private function executeRoute(string $controllerMethod, array $params = []): void
    {
        [$controllerName, $methodName] = explode('@', $controllerMethod);
        $controllerClass = "App\\Controller\\{$controllerName}";

        if (!class_exists($controllerClass) || !method_exists($controllerClass, $methodName)) {
            error_log("Controller ou método não encontrado: {$controllerClass}@{$methodName}");
            $this->handleError();
            return;
        }

        $controllerInstance = new $controllerClass();
        // 💡 Passa os valores dos parâmetros (como 2022) para o método
        $controllerInstance->$methodName(...array_values($params));
    }

    private function handleNotFound(): void
    {
        http_response_code(404);
        // Em um projeto real, você renderizaria uma view 404
        echo "<h1>404 Not Found</h1><p>A página <strong>{$_SERVER['REQUEST_URI']}</strong> não foi encontrada.</p>";
    }
    
    private function handleError(): void
    {
        http_response_code(500);
        echo "<h1>500 Internal Server Error</h1><p>Ocorreu um erro interno na aplicação.</p>";
    }
}