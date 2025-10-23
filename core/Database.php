<?php

namespace Core;

use PDO;
use PDOException;
use Core\Helper;

class Database
{
    private static ?PDO $pdoInstance = null;

    /**
     * Obtém a instância única da conexão PDO (Singleton).
     * @return PDO
     */
    public static function getConnection(): PDO
    {
        if (self::$pdoInstance === null) {

            if (Helper::isDevEnvironment()) {
                $dbHost = 'mysql_db';
                $dbPort = $_ENV['DB_PORT'] ?? '3306';
                $dbName = $_ENV['DB_NAME'] ?? 'test';
                $dbUser = $_ENV['DB_USER'] ?? 'root';
                $dbPass = $_ENV['DB_PASS'] ?? '';
            } else {
                $dbHost = 'sql100.infinityfree.com';
                $dbPort = $_ENV['DB_PORT'] ?? '3306';
                $dbName = 'if0_40222953_worldcup_data';
                $dbUser = 'if0_40222953';
                $dbPass = 'OLX8kY9lumjZ';
            }
            $dbCharset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
            
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $dbHost,
                $dbPort,
                $dbName,
                $dbCharset
            );

            try {
                self::$pdoInstance = new PDO($dsn, $dbUser, $dbPass);
                
                // Configurações para lançamento de exceções em erros de SQL
                self::$pdoInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // Retorna resultados como array associativo por padrão
                self::$pdoInstance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                // Em um sistema real, você registraria este erro
                die("Erro de Conexão com o Banco de Dados: " . $e->getMessage());
            }
        }
        return self::$pdoInstance;
    }

    /**
     * Executa uma query SQL e retorna os resultados.
     * * @param string $sql A query SQL a ser executada.
     * @param array $params [Opcional] Array de parâmetros para Prepared Statements.
     * @return array Os dados retornados pelo banco.
     */
    public static function query(string $sql, array $params = []): array
    {
        $pdo = self::getConnection();
        
        // 1. Prepara a query com placeholders
        $stmt = $pdo->prepare($sql);
        
        // 2. Executa a query, passando o array de parâmetros ($params)
        // 💡 ISSO FAZ A SUBSTITUIÇÃO DO :year PELO VALOR DE $year
        $success = $stmt->execute($params); 
        
        if (!$success) {
            // Tratar erro de execução, se necessário
            error_log("Database Error: " . json_encode($stmt->errorInfo()));
            return [];
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Recomendo usar FETCH_ASSOC
    }

}