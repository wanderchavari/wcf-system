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

}