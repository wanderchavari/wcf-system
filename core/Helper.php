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
}