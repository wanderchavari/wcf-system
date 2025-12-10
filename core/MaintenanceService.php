<?php

namespace Core;

use Core\Database;

abstract class MaintenanceService
{
    protected string $tableName;
    protected string $primaryKey = 'id';
    protected array $searchableFields = [];

    /**
     * Construtor para definir a tabela e a chave primária específicas.
     * * @param string $tableName O nome da tabela no banco de dados (Ex: 'wcf_confederacao').
     * @param string $primaryKey O nome da chave primária (Ex: 'id_confederacao').
     */
    public function __construct(string $tableName, string $primaryKey = 'id')
    {
        $this->tableName = $tableName;
        $this->primaryKey = $primaryKey;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }
    
    public function getSearchableFields(): array
    {
        return $this->searchableFields;
    }

    // =================================================================
    // CRUD: READ (Listar Todos com Ordenação e Busca)
    // =================================================================
    /**
     * Retorna todos os registros da tabela, com suporte a ordenação e busca.
     * A busca é feita nos campos definidos em $thisableFields na classe filha.
     */
    public function getAll(string $sort = 'id', string $dir = 'asc', string $searchTerm = ''): array
    {
        // 1. Validação de segurança para ordenação
        $allowedSortFields = array_merge([$this->primaryKey], $this->searchableFields);
        if (!in_array($sort, $allowedSortFields)) {
            $sort = $this->primaryKey;
        }
        $dir = (strtolower($dir) === 'desc') ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM {$this->tableName}";
        $params = [];
        $whereClauses = [];
        
        // 2. Lógica de Filtro (Busca)
        if (!empty($searchTerm) && !empty($this->searchableFields)) {
            
            // Constrói a cláusula WHERE LIKE para cada campo pesquisável
            foreach ($this->searchableFields as $field) {
                $whereClauses[] = "{$field} LIKE :search";
            }
            
            if (!empty($whereClauses)) {
                $sql .= " WHERE " . implode(" OR ", $whereClauses);
                $params[':search'] = "%{$searchTerm}%";
            }
        }

        // 3. Aplica Ordenação
        $sql .= " ORDER BY {$sort} {$dir}";
        // var_dump($sql, $params);
        // die();
        
        // 4. Executa e retorna
        return Database::query($sql, $params);
    }

    // =================================================================
    // CRUD: READ (Obter Por ID)
    // =================================================================
    /**
     * Retorna um único registro baseado na chave primária.
     */
    public function getById(int $id): ?array
    {
        // Usa a chave primária dinâmica
        $sql = "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = :id";
        $result = Database::query($sql, [':id' => $id]);
        // var_dump($result);
        // die();
        
        return $result[0] ?? null;
    }
    
    // =================================================================
    // CRUD: DELETE
    // =================================================================
    /**
     * Exclui um registro baseado na chave primária.
     * Retorna true em sucesso, ou uma string de erro em falha.
     */
    public function delete(int $id): bool|string
    {
        // Usa a chave primária dinâmica
        $sql = "DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = :id";
        
        try {
            // Assume que o Database::query executa comandos UPDATE/DELETE/INSERT
            Database::query($sql, [':id' => $id]);
            return true; 
        } catch (\Exception $e) {
            // Útil para capturar erros de integridade (Foreign Key)
            return "Falha ao excluir. Erro de Banco de Dados: " . $e->getMessage();
        }
    }

    // =================================================================
    // NOVO MÉTODO: EXPORTAÇÃO GENÉRICA PARA JSON
    // =================================================================
    /**
     * Exporta todos os dados da tabela para um arquivo JSON no caminho especificado.
     * @param string $filePath O caminho completo onde o arquivo JSON deve ser salvo.
     * @return bool True em caso de sucesso, ou string com a mensagem de erro.
     */
    abstract public function exportToJson(string $filePath): bool|string;

    abstract public function getDataToExport(): array;

}