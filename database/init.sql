-- Tabela wcf_confederacao
CREATE TABLE IF NOT EXISTS wcf_confederacao (
    id_confederacao INT AUTO_INCREMENT PRIMARY KEY,
    sigla VARCHAR(10) UNIQUE NOT NULL,
    nome_completo VARCHAR(100) UNIQUE NOT NULL,
    url_logo VARCHAR(255)
);

-- Tabela wcf_selecao
CREATE TABLE IF NOT EXISTS wcf_selecao (
    id_selecao INT AUTO_INCREMENT PRIMARY KEY,
    sigla_iso VARCHAR(3) UNIQUE NOT NULL,
    nome_selecao VARCHAR(100) NOT NULL,
    url_bandeira VARCHAR(255),
    fk_confederacao INT,
    fk_selecao_atual INT,

    FOREIGN KEY (fk_confederacao) REFERENCES wcf_confederacao(id_confederacao),
    FOREIGN KEY (fk_selecao_atual) REFERENCES wcf_selecao(id_selecao)
);

-- Tabela wcf_torneio
CREATE TABLE IF NOT EXISTS wcf_torneio (
    ano_torneio INT PRIMARY KEY,
    sede VARCHAR(100) NOT NULL,
    ponto_por_vitoria INT NOT NULL,
    -- Gênero do torneio: 'M' (Masculino) ou 'F' (Feminino)
    genero CHAR(1) NOT NULL CHECK (genero IN ('M', 'F')),
    url_cartaz VARCHAR(255),
    url_mascote VARCHAR(255)
);

-- Tabela wcf_participacao
CREATE TABLE IF NOT EXISTS wcf_participacao (
    id_participacao BIGINT AUTO_INCREMENT PRIMARY KEY,
    
    -- Chaves Estrangeiras
    fk_ano_torneio INT NOT NULL,
    fk_selecao INT NOT NULL,
    
    -- Restrições
    UNIQUE KEY uk_participacao (fk_ano_torneio, fk_selecao),

    -- Métricas Brutas
    jogos INT NOT NULL DEFAULT 0,
    vitorias INT NOT NULL DEFAULT 0,
    empates INT NOT NULL DEFAULT 0,
    derrotas INT NOT NULL DEFAULT 0,
    gols_feitos INT NOT NULL DEFAULT 0,
    gols_sofridos INT NOT NULL DEFAULT 0,
    classificacao_final INT,

    FOREIGN KEY (fk_ano_torneio) REFERENCES wcf_torneio(ano_torneio),
    FOREIGN KEY (fk_selecao) REFERENCES wcf_selecao(id_selecao)
);