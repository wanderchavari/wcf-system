<?php

namespace Backend\Service;

use Core\MaintenanceService;

class TorneioService extends MaintenanceService
{
    public function __construct()
    {
        $this->tableName = 'wcf_torneio';
        $this->primaryKey = 'ano_torneio';
        $this->searchableFields = ['ano_torneio', 'sede', 'genero'];
    }

    public function create(array $data): bool|string
    {
        $sql = "INSERT INTO wcf_torneio (ano_torneio, sede, ponto_por_vitoria, genero, url_cartaz, url_mascote) 
                VALUES (:ano_torneio, :sede, :ponto_por_vitoria, :genero, :url_cartaz, :url_mascote)";

        $params = [
            ':ano_torneio' => $data['ano_torneio'],
            ':sede' => $data['sede'],
            ':ponto_por_vitoria' => $data['ponto_por_vitoria'],
            ':genero' => $data['genero'],
            ':url_cartaz' => $data['url_cartaz'],
            ':url_mascote' => $data['url_mascote'],
        ];

        try {
            \Core\Database::execute($sql, $params);
            return true;
        } catch (\PDOException $e) {
            return "Erro ao cadastrar: " . $e->getMessage();
        }
    }
    
    public function update($data): bool|string
    {
        if (!isset($data['ano_torneio'])) {
            return "Erro: Ano do torneio é obrigatório para atualização.";
        }

        $sql = "UPDATE wcf_torneio SET 
                    sede = :sede, 
                    ponto_por_vitoria = :ponto_por_vitoria, 
                    genero = :genero, 
                    url_cartaz = :url_cartaz, 
                    url_mascote = :url_mascote
                WHERE ano_torneio = :ano_torneio";

        $params = [
            ':sede' => $data['sede'],
            ':ponto_por_vitoria' => $data['ponto_por_vitoria'],
            ':genero' => $data['genero'],
            ':url_cartaz' => $data['url_cartaz'],
            ':url_mascote' => $data['url_mascote'],
            ':ano_torneio' => $data['ano_torneio'],
        ];

        try {
            \Core\Database::execute($sql, $params);
            return true;
        } catch (\PDOException $e) {
            return "Erro ao atualizar: " . $e->getMessage();
        }

    }

    // =================================================================
    // IMPLEMENTAÇÃO DA EXPORTAÇÃO GENÉRICA PARA JSON
    // =================================================================
    public function exportToJson(string $filePath): bool|string
    {
        try {
            $data = $this->getDataToExport(); 
            
            if (empty($data)) {
                return "Nenhum dado encontrado na tabela {$this->tableName} para exportação.";
            }

            $jsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            if ($jsonContent === false) {
                return "Erro ao codificar dados para JSON.";
            }

            $result = file_put_contents($filePath, $jsonContent, LOCK_EX);

            if ($result === false) {
                return "Falha ao salvar o arquivo em: {$filePath}. Verifique permissões.";
            }

            return true;
            
        } catch (\Exception $e) {
            return "Erro inesperado na exportação: " . $e->getMessage();
        }
    }

    public function getDataToExport(): array
    {
        $sql = "SELECT ano_torneio, sede, ponto_por_vitoria, genero, url_cartaz, url_mascote 
                  FROM {$this->tableName} order by {$this->primaryKey} ASC";
        return \Core\Database::query($sql);
    }
    
}