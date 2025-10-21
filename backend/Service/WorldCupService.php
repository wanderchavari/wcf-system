<?php

namespace Backend\Service;

use Core\Database;

class WorldCupService
{
    /**
     * Busca a lista de seleções campeãs agrupadas pelo número total de títulos.
     * @return array Array no formato: [titulos => [selecao1, selecao2, ...]]
     */
    public function getCampeoesPorTitulo(): array
    {
        // 1. QUERY SQL (Permanente)
        $sqlCampeoes = "
            SELECT 
                T2.titulos,
                GROUP_CONCAT(T2.selecao ORDER BY T2.selecao ASC) AS selecoes
            FROM (
                SELECT 
                    COALESCE(wsa.nome_selecao, wsh.nome_selecao) AS selecao, 
                    COUNT(wp.classificacao_final) AS titulos
                FROM wcf_participacao wp 
                INNER JOIN wcf_selecao wsh ON wsh.id_selecao = wp.fk_selecao 
                LEFT JOIN wcf_selecao wsa ON wsa.id_selecao = wsh.fk_selecao_atual
                WHERE wp.classificacao_final = 1
                GROUP BY selecao
            ) AS T2
            GROUP BY T2.titulos
            ORDER BY T2.titulos DESC
        ";

        try {
            // 2. BUSCA DE DADOS
            $dadosDoBanco = Database::query($sqlCampeoes);
            $campeoesPorTitulo = [];

            // 3. LÓGICA DE FORMATAÇÃO
            foreach ($dadosDoBanco as $linha) {
                $titulos = (int) $linha['titulos'];
                $selecoesString = $linha['selecoes'] ?? ''; 
                $selecoes = array_map('trim', explode(',', $selecoesString));
                $campeoesPorTitulo[$titulos] = $selecoes;
            }
            
            return $campeoesPorTitulo;

        } catch (\Exception $e) {
            // Em um sistema real, você registraria este erro
            error_log("Erro no WorldCupService ao buscar campeões: " . $e->getMessage());
            return []; // Retorna array vazio em caso de falha
        }
    }

    /**
     * Retorna a contagem de participações e de títulos apenas para os países que já foram campeões,
     * em formato de lista (Selecao, Participacoes) ideal para gráficos.
     *
     * @return array
     */
    public function getParticipacoes(): array
    {
        $sql = "
            SELECT 
                COALESCE(wsa.nome_selecao, wsh.nome_selecao) AS Selecao,
                COUNT(wp.fk_ano_torneio) AS Participacoes
            FROM wcf_participacao wp
            INNER JOIN wcf_selecao wsh ON wsh.id_selecao = wp.fk_selecao
            LEFT JOIN wcf_selecao wsa ON wsa.id_selecao = wsh.fk_selecao_atual
            WHERE wp.fk_selecao IN (
                SELECT DISTINCT p.fk_selecao
                FROM wcf_participacao p
                WHERE p.classificacao_final = 1
            )
            GROUP BY Selecao
            ORDER BY Participacoes DESC;
        ";

        try {
            // A consulta já retorna os dados prontos no formato que precisamos
            $dadosDoBanco = Database::query($sql);
            
            // Mapeia para garantir que Participacoes e Titulos sejam tratados como números inteiros
            $dadosFormatados = array_map(function($linha) {
                $linha['Participacoes'] = (int) $linha['Participacoes'];
                return $linha;
            }, $dadosDoBanco);

            return $dadosFormatados;

        } catch (\Exception $e) {
            error_log("Erro no WorldCupService ao buscar participações para gráfico: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca a lista de anos de todas as Copas cadastradas.
     * @return array Array de anos (ex: [1930, 1934, 1938, ...])
     */
    public function getTorneiosParaMenu(): array
    {
        // Query fornecida pelo usuário
        $sqlTorneios = "
            SELECT ano_torneio, sede
            FROM wcf_torneio
            ORDER BY ano_torneio ASC
        ";

        try {
            // A função query() já retorna um array de arrays (ou objetos) no formato que precisamos
            $dadosDoBanco = Database::query($sqlTorneios);
            
            return $dadosDoBanco;

        } catch (\Exception $e) {
            error_log("Erro no WorldCupService ao buscar torneios para o menu: " . $e->getMessage());
            // Fallback com dados mocados (em ordem crescente)
            return [
                ['ano_torneio' => 1930, 'sede' => 'Uruguai'],
                ['ano_torneio' => 1934, 'sede' => 'Itália'],
                ['ano_torneio' => 2022, 'sede' => 'Catar'],
            ]; 
        }
    }

    /**
     * Busca a Sede e os 3 primeiros colocados de cada copa informada.
     * @return array Array ()
     */
    public function getChampionByYear(int $year): ?array
    {
        $sql = "
            SELECT 
                COALESCE(wp.classificacao_final,0) AS posicao,
                wsa.nome_selecao AS selecao_atual, 
                wsh.nome_selecao AS selecao_historica,
                wt.ano_torneio,
                wt.sede
            FROM wcf_torneio wt
            LEFT JOIN wcf_participacao wp ON wt.ano_torneio = wp.fk_ano_torneio
            LEFT JOIN wcf_selecao wsh ON wsh.id_selecao = wp.fk_selecao
            LEFT JOIN wcf_selecao wsa ON wsa.id_selecao = wsh.fk_selecao_atual
            WHERE wt.ano_torneio = :year AND COALESCE(wp.classificacao_final,0) <= 3
        ";

        try {
            $result = Database::query($sql, ['year' => $year]);
            return $result ?? []; 
        } catch (\Exception $e) {
            error_log("Erro no WorldCupService ao buscar campeão por ano: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca todos os torneios com ano, sede e o respectivo campeão.
     * @return array Lista de torneios, ou array vazio.
     */
    public function getAllTorneios(): array
    {
        $sql = "
            SELECT 
                wt.ano_torneio, 
                wt.sede,
                COALESCE(wsa.nome_selecao, wsh.nome_selecao) AS campeao
            FROM wcf_torneio wt
            LEFT JOIN wcf_participacao wp ON wt.ano_torneio = wp.fk_ano_torneio AND wp.classificacao_final = 1
            LEFT JOIN wcf_selecao wsh ON wsh.id_selecao = wp.fk_selecao
            LEFT JOIN wcf_selecao wsa ON wsa.id_selecao = wsh.fk_selecao_atual
            ORDER BY wt.ano_torneio;
        ";

        try {
            $result = Database::query($sql); 
            return $result ?? [];
        } catch (\Exception $e) {
            error_log("Erro no WorldCupService ao buscar todos os torneios: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca o ranking completo de uma copa, consolidando o nome da seleção.
     * @param int $year O ano do torneio.
     * @return array O ranking completo.
     */
    public function getTorneioRankingByYear(int $year): array
    {
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
            JOIN wcf_selecao wsh ON p.fk_selecao = wsh.id_selecao -- Seleção Histórica
            LEFT JOIN wcf_selecao wsa ON wsh.fk_selecao_atual = wsa.id_selecao -- Seleção Atual
            JOIN wcf_torneio wt ON wt.ano_torneio = p.fk_ano_torneio
            WHERE p.fk_ano_torneio = :year
            ORDER BY p.classificacao_final ASC, pontos_torneio DESC;
        ";

        try {
            $result = Database::query($sql, ['year' => $year]); 
            return $result ?? [];
        } catch (\Exception $e) {
            error_log("Erro ao buscar ranking do torneio de {$year}: " . $e->getMessage());
            return [];
        }
    }

}