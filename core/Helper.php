<?php

namespace Core;

class Helper
{
    /**
     * Retorna a versão da aplicação.
     */
    public static function getAppVersion(): string
    {
        // Em um projeto real, isso viria de um arquivo de configuração/ambiente
        return "1.0.0"; 
    }

    /**
     * Verifica se o ambiente está em desenvolvimento.
     */
    public static function isDevEnvironment(): bool
    {
        return ($_ENV['APP_ENV'] ?? 'dev') === 'dev';
    }
    
    /**
     * Aplica htmlspecialchars() para evitar ataques Cross-Site Scripting (XSS).
     * Deve ser usada em TODO dado de string que sai do DB/URL para o HTML.
     * @param mixed $value O valor a ser sanitizado.
     * @return string O valor seguro (escapado).
     */
    public static function h($value): string
    {
        // 1. Converte para string (se for null, int, float, etc.)
        $value = (string) $value;

        // 2. Aplica a sanitização
        // ENT_QUOTES: Trata aspas simples e duplas
        // 'UTF-8': Garante o encoding correto
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Gera o HTML para um cabeçalho de coluna clicável para ordenação.
     * * @param string $label O texto a ser exibido no cabeçalho (ex: 'Nome').
     * @param string $field O nome do campo no banco de dados (ex: 'nome_completo').
     * @param string $currentSort O campo que está atualmente ordenado (ex: 'id_confederacao').
     * @param string $currentDirection A direção atual (ex: 'asc' ou 'desc').
     * @param string $baseUri A URI base para o link (ex: '/manutencao/torneios').
     * @return string O HTML completo do cabeçalho de ordenação.
     */
    public static function sortableHeader(string $label, string $field, string $currentSort, string $currentDirection, string $baseUri = ''): string
    {
        // 1. Determina a direção oposta
        $newDirection = ($currentSort === $field && $currentDirection === 'asc') ? 'desc' : 'asc';

        // 2. Define o ícone de ordenação atual (se for a coluna ativa)
        $icon = '';
        if ($currentSort === $field) {
            $icon = $currentDirection === 'asc' 
                ? ' <i class="fas fa-arrow-up"></i>' 
                : ' <i class="fas fa-arrow-down"></i>';
        }
        
        // 3. Obtém os parâmetros de busca, se existirem
        $searchQuery = $_GET['search'] ?? '';
        $searchParam = $searchQuery ? '&search=' . self::h($searchQuery) : '';

        // 4. Constrói a URL
        $uri = self::h($baseUri) . "?sort=" . self::h($field) . "&direction=" . self::h($newDirection) . $searchParam;

        // 5. Retorna o HTML do link
        return '<a href="' . $uri . '" class="text-white text-decoration-none">' 
            . self::h($label) . $icon . 
            '</a>';
    }

}