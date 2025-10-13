<?php
// backend/Service/AnaliseService.php

namespace Backend\Service;

use Core\Database;

class AnaliseService
{
    /**
     * Retorna o ranking de uma copa específica ($year) ou o ranking histórico geral (null).
     * @param int|null $year O ano da copa, ou null para ranking geral.
     * @return array
     */
    public function getRanking(?int $year = null, ?string $copas = 'all'): array
    {
        $params = [];
        
        // =================================================================
        // 💡 1. LÓGICA PARA RANKING ESPECÍFICO POR ANO
        // =================================================================
        if ($year !== null) {
            $sql = "
                SELECT 
                    COALESCE(wsa.nome_selecao, wsh.nome_selecao) AS nome_selecao,
                    COALESCE(wsa.sigla_iso, wsh.sigla_iso) AS sigla_iso,
                    p.classificacao_final,
                    p.vitorias,
                    p.empates,
                    p.derrotas,
                    p.gols_feitos,
                    p.gols_sofridos,
                    (p.gols_feitos - p.gols_sofridos) AS saldo_gols,
                    (p.vitorias * wt.ponto_por_vitoria + p.empates * 1) AS pontos_torneio
                FROM wcf_participacao p
                JOIN wcf_selecao wsh ON p.fk_selecao = wsh.id_selecao
                LEFT JOIN wcf_selecao wsa ON wsh.fk_selecao_atual = wsa.id_selecao
                JOIN wcf_torneio wt ON wt.ano_torneio = p.fk_ano_torneio
                WHERE p.fk_ano_torneio = :year
                ORDER BY p.classificacao_final ASC;
            ";
            $params['year'] = $year;

        // =================================================================
        // 💡 2. LÓGICA PARA RANKING HISTÓRICO GERAL
        // =================================================================
        } else {
            $sql = "
                SELECT
                    COALESCE(wsa.nome_selecao, wsh.nome_selecao) AS Selecao_Consolidada,
                    SUM((wp.vitorias * wt.ponto_por_vitoria) + wp.empates) AS Total_Pontos,
                    SUM(wp.vitorias + wp.empates + wp.derrotas) AS Total_Jogos,
                    SUM(wp.vitorias) AS Total_Vitorias,
                    SUM(wp.empates) AS Total_Empates,
                    SUM(wp.derrotas) AS Total_Derrotas,
                    SUM(wp.gols_feitos) AS Total_Gols_Feitos,
                    SUM(wp.gols_sofridos) AS Total_Gols_Sofridos,
                    SUM(wp.gols_feitos - wp.gols_sofridos) AS saldo_gols
                FROM wcf_participacao wp
                     INNER JOIN wcf_torneio wt ON wt.ano_torneio = wp.fk_ano_torneio 
                     INNER JOIN wcf_selecao wsh ON wsh.id_selecao = wp.fk_selecao
                     LEFT JOIN wcf_selecao wsa ON wsa.id_selecao = wsh.fk_selecao_atual
            ";
            if ($copas == 'old') {
                $sql .= "
                    WHERE wp.fk_ano_torneio < 1994
                    ";
            } elseif ($copas == 'modern') {
                $sql .= "
                    WHERE wp.fk_ano_torneio >= 1994
                    ";
            }
            $sql .= "
                GROUP BY Selecao_Consolidada
                ORDER BY
                    Total_Pontos DESC,
                    Total_Vitorias DESC,
                    saldo_gols DESC;
                ";
        }

        try {
            // Executa a consulta, usando parâmetros apenas se existirem
            $result = Database::query($sql, $params);
            return $result ?? [];
        } catch (\Exception $e) {
            error_log("Erro no AnaliseService::getRanking: " . $e->getMessage());
            return [];
        }
    }

}