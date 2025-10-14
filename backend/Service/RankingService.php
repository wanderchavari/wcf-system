<?php
// backend/Service/RankingService.php

namespace Backend\Service;

use Core\Database;

class RankingService
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
                    COALESCE(wsa.nome_selecao, wsh.nome_selecao) AS selecao,
                    COALESCE(wsa.sigla_iso, wsh.sigla_iso) AS sigla,
                    p.classificacao_final AS posicao,
                    (p.vitorias + p.empates + p.derrotas) AS jogos,
                    (p.vitorias * wt.ponto_por_vitoria + p.empates) AS pontos,
                    p.vitorias,
                    p.empates,
                    p.derrotas,
                    p.gols_feitos,
                    p.gols_sofridos,
                    (p.gols_feitos - p.gols_sofridos) AS saldo_gols
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
                    COALESCE(wsa.nome_selecao, wsh.nome_selecao) AS selecao,
                    COALESCE(wsa.sigla_iso, wsh.sigla_iso) AS sigla,
                    SUM(wp.vitorias + wp.empates + wp.derrotas) AS jogos,
                    SUM((wp.vitorias * wt.ponto_por_vitoria) + wp.empates) AS pontos,
                    SUM(wp.vitorias) AS vitorias,
                    SUM(wp.empates) AS empates,
                    SUM(wp.derrotas) AS derrotas,
                    SUM(wp.gols_feitos) AS gols_feitos,
                    SUM(wp.gols_sofridos) AS gols_sofridos,
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
                GROUP BY selecao, sigla
                ORDER BY
                    pontos DESC,
                    vitorias DESC,
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

    /**
     * Retorna a média histórica da classificação final de cada seleção.
     * Quanto menor a média, melhor o desempenho.
     * @return array
     */
    public function getMediaClassificacaoFinal(): array
    {
        $sql = "
            SELECT 
                COALESCE(wsa.nome_selecao, wsh.nome_selecao) AS selecao,
                COALESCE(wsa.sigla_iso, wsh.sigla_iso) AS sigla,
                AVG(wp.classificacao_final) AS media_classificacao,
                COUNT(wp.fk_ano_torneio) AS total_participacoes
            FROM wcf_participacao wp
                INNER JOIN wcf_selecao wsh ON wsh.id_selecao = wp.fk_selecao
                LEFT JOIN wcf_selecao wsa ON wsa.id_selecao = wsh.fk_selecao_atual
            WHERE wp.classificacao_final IS NOT NULL 
            GROUP BY 
                selecao, sigla
            ORDER BY 
                media_classificacao ASC, 
                total_participacoes DESC;
        ";

        try {
            // Assume-se que você tem uma classe Database que executa a query
            return Database::query($sql, []);
        } catch (\Exception $e) {
            error_log("Erro ao buscar Média de Classificação Final: " . $e->getMessage());
            return [];
        }
    }

}