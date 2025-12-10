<?php

namespace Backend\Service;

use Core\MaintenanceService;

class ConfederationService extends MaintenanceService
{

    public function __construct()
    {
        // Passa a tabela e a chave primária para o construtor pai
        parent::__construct('wcf_confederacao', 'id_confederacao'); 
    }

    protected array $searchableFields = ['sigla', 'nome_completo'];

    /**
     * Insere uma nova confederação no banco de dados.
     * @return bool|string Retorna true em sucesso, ou a mensagem de erro.
     */
    public function createConfederation(string $sigla, string $nome_completo, string $url_logo): bool|string
    {
        $sql = "INSERT INTO wcf_confederacao (sigla, nome_completo, url_logo) 
                VALUES (:sigla, :nome, :logo)";
        
        $params = [
            ':sigla' => $sigla,
            ':nome' => $nome_completo,
            ':logo' => $url_logo
        ];

        try {
            \Core\Database::execute($sql, $params); // Usa o método de execução do seu DB Helper
            return true;
        } catch (\PDOException $e) {
            // Verifica erro de chave duplicada (Assumindo que Sigla é UNIQUE)
            if ($e->getCode() == 23000) { 
                return "Erro: Já existe uma confederação com esta Sigla.";
            }
            // Retorna a mensagem de erro detalhada, útil em manutenção
            return "Erro ao cadastrar: " . $e->getMessage();
        }
    }

    /**
     * Retorna todas as confederações cadastradas.
     */
    public function getAllConfederations(string $sort = 'id_confederacao', string $dir = 'asc', string $searchTerm = ''): array
    {
        $allowedFields = ['id_confederacao', 'sigla', 'nome_completo'];
        if (!in_array($sort, $allowedFields)) {
            $sort = 'id_confederacao';
        }
        $dir = (strtolower($dir) === 'desc') ? 'DESC' : 'ASC';
        $sql = "SELECT id_confederacao, sigla, nome_completo, url_logo FROM wcf_confederacao";
        $params = [];
        if (!empty($searchTerm)) {
            // Usa LIKE para buscar o termo em Nome Completo OU Sigla
            $sql .= " WHERE nome_completo LIKE :search OR sigla LIKE :search";
            $params[':search'] = "%{$searchTerm}%"; // % para busca parcial
        }

        $sql .= " ORDER BY {$sort} {$dir}";
        // echo "Query: " . $sql . "<br>";
        // echo "Parâmetros (Service): ";
        // var_dump($params);
        // die();
        
        try {
            // Usa o método de consulta do seu DB Helper
            return \Core\Database::query($sql, $params);
        } catch (\Exception $e) {
            error_log("Erro ao carregar lista de confederações: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retorna os dados de uma única confederação pelo ID.
     */
    public function getConfederationById(int $id): ?array
    {
        $sql = "SELECT id_confederacao, sigla, nome_completo, url_logo 
                FROM wcf_confederacao WHERE id_confederacao = :id";
        
        try {
            return \Core\Database::query($sql, [':id' => $id]);
        } catch (\Exception $e) {
            error_log("Erro ao carregar confederação ID {$id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Atualiza os dados de uma confederação.
     */
    public function update(int $id, string $sigla, string $nome_completo, string $url_logo): bool|string
    {
        $sql = "UPDATE wcf_confederacao 
                SET nome_completo = :nome, url_logo = :logo
                WHERE id_confederacao = :id";
        
        $params = [
            ':id' => $id,
            ':nome' => $nome_completo,
            ':logo' => $url_logo
        ];

        try {
            \Core\Database::execute($sql, $params);
            return true;
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) { 
                return "Erro: Já existe outra confederação com esta Sigla.";
            }
            return "Erro ao atualizar: " . $e->getMessage();
        }
    }

    // =================================================================
    // IMPLEMENTAÇÃO DA EXPORTAÇÃO GENÉRICA PARA JSON
    // =================================================================
    public function exportToJson(string $filePath): bool|string
    {
        try {
            // 1. BUSCAR TODOS OS DADOS (SELECT * FROM wcf_confederacao)
            // Assumindo que você tem um método genérico no Service ou uma conexão DB
            // que executa um SELECT *
            
            $data = $this->getDataToExport(); 
            
            if (empty($data)) {
                return "Nenhum dado encontrado na tabela {$this->tableName} para exportação.";
            }

            // 2. CONVERTER PARA JSON
            // JSON_PRETTY_PRINT para melhor legibilidade
            $jsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            if ($jsonContent === false) {
                return "Erro ao codificar dados para JSON.";
            }

            // 3. SALVAR NO ARQUIVO
            // flag FILE_APPEND: Adiciona ao arquivo (não é o ideal para exportação)
            // flag LOCK_EX: Garante escrita exclusiva
            $result = file_put_contents($filePath, $jsonContent, LOCK_EX);

            if ($result === false) {
                // Erro de permissão de escrita ou caminho inválido
                return "Falha ao salvar o arquivo em: {$filePath}. Verifique permissões.";
            }

            return true; // Sucesso
            
        } catch (\Exception $e) {
            return "Erro inesperado na exportação: " . $e->getMessage();
        }
    }

    public function getDataToExport(): array
    {
        $sql = "SELECT sigla, nome_completo, url_logo FROM {$this->tableName} order by {$this->primaryKey} ASC";
        return \Core\Database::query($sql);
    }

}