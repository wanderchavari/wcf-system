<?php

namespace Backend\Service;

use Core\Database;

class GameService
{
    /**
     * Busca a lista de jogos realizados em um torneio específico.
     * @param int $year Ano do torneio.
     * @return array Lista de jogos.
     */
    public function getGamesByYear(int $year): array
    {
        $sql = "
            SELECT 
                wg.data_jogo, 
                wg.fase, 
                wg.grupo,          
                wg.estadio,        
                wg.cidade,         
                wg.pais_sede,      
                wg.gols_casa, 
                wg.gols_fora,
                wg.vitoria_penaltis,
                wg.gp_casa,
                wg.gp_fora,
                wg.observacao,       
                s1.nome_selecao AS selecao_casa,
                s1.url_bandeira AS bandeira_casa,
                s2.nome_selecao AS selecao_fora,
                s2.url_bandeira AS bandeira_fora,
                p.nome_completo AS arbitro,
                wg.publico
            FROM wcf_jogo wg
            INNER JOIN wcf_selecao s1 ON s1.id_selecao = wg.fk_selecao_casa
            INNER JOIN wcf_selecao s2 ON s2.id_selecao = wg.fk_selecao_fora
             LEFT JOIN wcf_pessoas p ON p.id_pessoa = wg.fk_arbitro_principal
            WHERE wg.fk_ano_torneio = :year
            ORDER BY wg.data_jogo ASC;
        ";

        try {
            return Database::query($sql, ['year' => $year]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar jogos: " . $e->getMessage());
            return [];
        }
    }
}