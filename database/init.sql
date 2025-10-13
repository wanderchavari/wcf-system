-- #################################################################
-- # PASSO 1: Configuração Inicial e Limpeza (Clean-up)
-- #################################################################

-- Define o nome do banco de dados a ser criado
SET @DATABASE_NAME = 'worldcup_data';

-- Opicional: Exclui o banco de dados se ele já existir (útil para desenvolvimento)
-- DROP DATABASE IF EXISTS worldcup_data;
DROP DATABASE IF EXISTS @DATABASE_NAME;

-- Cria o Banco de Dados
CREATE DATABASE @DATABASE_NAME
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

-- Seleciona o Banco de Dados para que as próximas operações sejam aplicadas a ele
USE @DATABASE_NAME;


-- #################################################################
-- # PASSO 2: Criação das Tabelas (Schema)
-- #################################################################
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


-- #################################################################
-- # PASSO 3: Inserção de Dados Iniciais (Seed)
-- #################################################################

-- 3.1: Inserção de Confederações
INSERT INTO wcf_confederacao (sigla,nome_completo,url_logo) VALUES
	 ('CONMEBOL','Confederação Sul-Americana de Futebol',NULL),
	 ('UEFA','União das Associações Europeias de Futebol',NULL),
	 ('CONCACAF','Confederação de Futebol da América do Norte, Central e Caribe',NULL),
	 ('AFC','Confederação Asiática de Futebol',NULL),
	 ('CAF','Confederação Africana de Futebol',NULL),
	 ('OFC','Confederação de Futebol da Oceania',NULL);

-- 3.2: Inserção de Torneios
INSERT INTO wcf_torneio (ano_torneio,sede,ponto_por_vitoria,genero,url_cartaz,url_mascote) VALUES
	 (1930,'Uruguai',2,'M',NULL,NULL),
	 (1934,'Itália',2,'M',NULL,NULL),
	 (1938,'França',2,'M',NULL,NULL),
	 (1950,'Brasil',2,'M',NULL,NULL),
	 (1954,'Suíça',2,'M',NULL,NULL),
	 (1958,'Suécia',2,'M',NULL,NULL),
	 (1962,'Chile',2,'M',NULL,NULL),
	 (1966,'Inglaterra',2,'M',NULL,NULL),
	 (1970,'México',2,'M',NULL,NULL),
	 (1974,'Alemanha Ocidental',2,'M',NULL,NULL);
INSERT INTO wcf_torneio (ano_torneio,sede,ponto_por_vitoria,genero,url_cartaz,url_mascote) VALUES
	 (1978,'Argentina',2,'M',NULL,NULL),
	 (1982,'Espanha',2,'M',NULL,NULL),
	 (1986,'México',2,'M',NULL,NULL),
	 (1990,'Itália',2,'M',NULL,NULL),
	 (1994,'EUA',3,'M',NULL,NULL),
	 (1998,'França',3,'M',NULL,NULL),
	 (2002,'Coréia do Sul e Japão',3,'M',NULL,NULL),
	 (2006,'Alemanha',3,'M',NULL,NULL),
	 (2010,'África do Sul',3,'M',NULL,NULL),
	 (2014,'Brasil',3,'M',NULL,NULL);
INSERT INTO wcf_torneio (ano_torneio,sede,ponto_por_vitoria,genero,url_cartaz,url_mascote) VALUES
	 (2018,'Rússia',3,'M',NULL,NULL),
	 (2022,'Catar',3,'M',NULL,NULL),
	 (2026,'Canadá, Estados Unidos e México',3,'M',NULL,NULL);

-- 3.3: Inserção de Seleções
INSERT INTO wcf_selecao (sigla_iso,nome_selecao,url_bandeira,fk_confederacao,fk_selecao_atual) VALUES
	 ('URU','Uruguai',NULL,1,NULL),
	 ('ARG','Argentina',NULL,1,NULL),
	 ('CHL','Chile',NULL,1,NULL),
	 ('BRA','Brasil',NULL,1,NULL),
	 ('PAR','Paraguai',NULL,1,NULL),
	 ('PER','Peru',NULL,1,NULL),
	 ('COL','Colômbia',NULL,1,NULL),
	 ('ECU','Equador',NULL,1,NULL),
	 ('BOL','Bolívia',NULL,1,NULL),
	 ('USA','Estados Unidos',NULL,3,NULL);
INSERT INTO wcf_selecao (sigla_iso,nome_selecao,url_bandeira,fk_confederacao,fk_selecao_atual) VALUES
	 ('MEX','México',NULL,3,NULL),
	 ('CUB','Cuba',NULL,3,NULL),
	 ('HON','Honduras',NULL,3,NULL),
	 ('SLV','El Salvador',NULL,3,NULL),
	 ('HAI','Haiti',NULL,3,NULL),
	 ('CAN','Canadá',NULL,3,NULL),
	 ('CRC','Costa Rica',NULL,3,NULL),
	 ('JAM','Jamaica',NULL,3,NULL),
	 ('TRI','Trinidad e Tobago',NULL,3,NULL),
	 ('PAN','Panamá',NULL,3,NULL);
INSERT INTO wcf_selecao (sigla_iso,nome_selecao,url_bandeira,fk_confederacao,fk_selecao_atual) VALUES
	 ('EGY','Egito',NULL,5,NULL),
	 ('MAR','Marrocos',NULL,5,NULL),
	 ('ZAR','Zaire',NULL,5,87),
	 ('TUN','Tunísia',NULL,5,NULL),
	 ('ALG','Argélia',NULL,5,NULL),
	 ('CMR','Camarões',NULL,5,NULL),
	 ('NGA','Nigéria',NULL,5,NULL),
	 ('RSA','África do Sul',NULL,5,NULL),
	 ('SEN','Senegal',NULL,5,NULL),
	 ('GHA','Gana',NULL,5,NULL);
INSERT INTO wcf_selecao (sigla_iso,nome_selecao,url_bandeira,fk_confederacao,fk_selecao_atual) VALUES
	 ('CIV','Costa do Marfim',NULL,5,NULL),
	 ('ANG','Angola',NULL,5,NULL),
	 ('TOG','Togo',NULL,5,NULL),
	 ('KOR','Coréia do Sul',NULL,4,NULL),
	 ('IRN','Irã',NULL,4,NULL),
	 ('KUW','Kuwait',NULL,4,NULL),
	 ('UAE','Emirados Árabes Unidos',NULL,4,NULL),
	 ('KSA','Arábia Saudita',NULL,4,NULL),
	 ('JPN','Japão',NULL,4,NULL),
	 ('CHN','China',NULL,4,NULL);
INSERT INTO wcf_selecao (sigla_iso,nome_selecao,url_bandeira,fk_confederacao,fk_selecao_atual) VALUES
	 ('QAT','Catar',NULL,4,NULL),
	 ('IDN','Indonésia',NULL,4,NULL),
	 ('IRQ','Iraque',NULL,4,NULL),
	 ('AUS','Austrália',NULL,4,NULL),
	 ('NZL','Nova Zelândia',NULL,6,NULL),
	 ('FRA','França',NULL,2,NULL),
	 ('ITA','Itália',NULL,2,NULL),
	 ('GER','Alemanha',NULL,2,NULL),
	 ('AUT','Áustria',NULL,2,NULL),
	 ('ESP','Espanha',NULL,2,NULL);
INSERT INTO wcf_selecao (sigla_iso,nome_selecao,url_bandeira,fk_confederacao,fk_selecao_atual) VALUES
	 ('HUN','Hungria',NULL,2,NULL),
	 ('SUI','Suíça',NULL,2,NULL),
	 ('SWE','Suécia',NULL,2,NULL),
	 ('NED','Holanda',NULL,2,NULL),
	 ('POL','Polônia',NULL,2,NULL),
	 ('NOR','Noruega',NULL,2,NULL),
	 ('ENG','Inglaterra',NULL,2,NULL),
	 ('SCO','Escócia',NULL,2,NULL),
	 ('NIR','Irlanda do Norte',NULL,2,NULL),
	 ('WAL','País de Gales',NULL,2,NULL);
INSERT INTO wcf_selecao (sigla_iso,nome_selecao,url_bandeira,fk_confederacao,fk_selecao_atual) VALUES
	 ('BUL','Bulgária',NULL,2,NULL),
	 ('POR','Portugal',NULL,2,NULL),
	 ('GRE','Grécia',NULL,2,NULL),
	 ('CRO','Croácia',NULL,2,NULL),
	 ('SVN','Eslovênia',NULL,2,NULL),
	 ('UKR','Ucrânia',NULL,2,NULL),
	 ('SVK','Eslováquia',NULL,2,NULL),
	 ('BIH','Bósnia e Herzegovina',NULL,2,NULL),
	 ('ISL','Islândia',NULL,2,NULL),
	 ('SRB','Sérvia',NULL,2,NULL);
INSERT INTO wcf_selecao (sigla_iso,nome_selecao,url_bandeira,fk_confederacao,fk_selecao_atual) VALUES
	 ('IRL','Irlanda',NULL,2,NULL),
	 ('DEN','Dinamarca',NULL,2,NULL),
	 ('TUR','Turquia',NULL,2,NULL),
	 ('ISR','Israel',NULL,2,NULL),
	 ('YUG','Iugoslávia',NULL,2,70),
	 ('SCG','Sérvia e Montenegro',NULL,2,70),
	 ('TCH','República Tcheca',NULL,2,NULL),
	 ('FRG','Alemanha Ocidental',NULL,2,48),
	 ('URS','Rússia',NULL,2,NULL),
	 ('GDR','Alemanha Oriental',NULL,2,NULL);
INSERT INTO wcf_selecao (sigla_iso,nome_selecao,url_bandeira,fk_confederacao,fk_selecao_atual) VALUES
	 ('ROU','Romênia',NULL,2,NULL),
	 ('BEL','Bélgica',NULL,2,NULL),
	 ('RUS','União Soviética',NULL,2,79),
	 ('IND','Índias Orientais',NULL,4,42),
	 ('CZE','Tchecoslováquia',NULL,2,77),
	 ('PKR','Coréia do Norte',NULL,4,NULL),
	 ('RDC','República Democrática do Congo',NULL,5,NULL);

-- 3.4: Inserção de participações
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1930,1,4,0,0,15,3,1),
	 (1930,2,4,0,1,18,9,2),
	 (1930,10,2,0,1,7,6,3),
	 (1930,75,2,0,1,7,7,4),
	 (1930,3,2,0,1,5,3,5),
	 (1930,4,1,0,1,5,2,6),
	 (1930,46,1,0,2,4,3,7),
	 (1930,81,1,0,1,3,5,8),
	 (1930,5,1,0,1,1,3,9),
	 (1930,6,0,0,2,1,4,10);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1930,82,0,0,2,0,4,11),
	 (1930,9,0,0,2,0,8,12),
	 (1930,11,0,0,3,4,13,13),
	 (1934,47,4,1,0,12,3,1),
	 (1934,85,3,0,1,9,6,2),
	 (1934,48,3,0,1,11,8,3),
	 (1934,49,2,0,2,7,7,4),
	 (1934,50,1,1,1,4,3,5),
	 (1934,51,1,0,1,5,4,6),
	 (1934,52,1,0,1,5,5,7);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1934,53,1,0,1,4,4,8),
	 (1934,2,0,0,1,2,3,9),
	 (1934,46,0,0,1,2,3,10),
	 (1934,54,0,0,1,2,3,11),
	 (1934,81,0,0,1,1,2,12),
	 (1934,21,0,0,1,2,4,13),
	 (1934,4,0,0,1,1,3,14),
	 (1934,82,0,0,1,2,5,15),
	 (1934,10,0,0,1,1,7,16),
	 (1938,47,4,0,0,11,5,1);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1938,51,3,0,1,15,5,2),
	 (1938,4,2,1,1,12,10,3),
	 (1938,53,2,0,2,14,9,4),
	 (1938,85,1,1,0,4,1,5),
	 (1938,46,1,0,1,4,4,6),
	 (1938,52,0,1,1,1,3,7),
	 (1938,12,0,1,1,1,9,8),
	 (1938,81,0,1,0,3,3,9),
	 (1938,48,0,1,0,1,1,10),
	 (1938,55,0,0,1,5,6,11);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1938,56,0,0,1,1,2,12),
	 (1938,82,0,0,1,1,3,13),
	 (1938,49,0,0,1,0,3,14),
	 (1938,54,0,0,1,0,3,15),
	 (1938,84,0,0,1,0,6,16),
	 (1950,1,3,1,0,15,5,1),
	 (1950,4,4,1,1,22,6,2),
	 (1950,53,2,1,2,11,15,3),
	 (1950,50,3,1,2,10,12,4),
	 (1950,75,2,0,1,7,3,5);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1950,52,1,1,1,4,6,6),
	 (1950,47,1,0,1,4,3,7),
	 (1950,57,1,0,2,2,2,8),
	 (1950,3,1,0,2,5,6,9),
	 (1950,10,1,0,2,4,8,10),
	 (1950,5,0,1,1,2,4,11),
	 (1950,11,0,0,3,2,10,12),
	 (1950,9,0,0,1,0,8,13),
	 (1954,78,5,0,1,25,14,1),
	 (1954,51,4,0,1,27,9,2);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1954,49,3,0,1,14,11,3),
	 (1954,1,3,0,2,16,9,4),
	 (1954,52,2,0,2,11,11,5),
	 (1954,4,1,1,1,8,5,6),
	 (1954,57,1,1,1,8,8,7),
	 (1954,75,1,1,1,2,3,8),
	 (1954,46,1,0,1,3,3,9),
	 (1954,73,1,0,2,10,11,10),
	 (1954,47,1,0,2,6,7,11),
	 (1954,82,0,1,1,5,8,12);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1954,11,0,0,2,2,8,13),
	 (1954,85,0,0,2,0,7,14),
	 (1954,58,0,0,2,0,8,15),
	 (1954,34,0,0,2,0,16,16),
	 (1958,4,5,1,0,16,4,1),
	 (1958,53,4,1,1,12,7,2),
	 (1958,46,4,0,2,23,15,3),
	 (1958,78,2,2,2,12,14,4),
	 (1958,83,2,1,2,5,6,5),
	 (1958,59,2,1,2,6,10,6);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1958,60,1,3,1,4,4,7),
	 (1958,75,1,2,1,7,7,8),
	 (1958,85,1,1,2,9,6,9),
	 (1958,51,1,1,2,7,5,10),
	 (1958,5,1,1,1,9,12,11),
	 (1958,57,0,3,1,4,5,12),
	 (1958,2,1,0,2,5,10,13),
	 (1958,58,0,1,2,4,6,14),
	 (1958,49,0,1,2,2,7,15),
	 (1958,11,0,1,2,1,8,16);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1962,4,5,1,0,14,5,1),
	 (1962,85,3,1,2,7,7,2),
	 (1962,3,4,0,2,10,8,3),
	 (1962,75,3,0,3,10,5,4),
	 (1962,51,2,1,1,8,3,5),
	 (1962,83,2,1,1,9,7,6),
	 (1962,78,2,1,1,4,2,7),
	 (1962,47,1,1,1,3,2,8),
	 (1962,57,1,1,2,5,6,9),
	 (1962,2,1,1,1,2,3,10);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1962,11,1,0,2,3,4,11),
	 (1962,50,1,0,2,2,3,12),
	 (1962,1,1,0,2,4,6,13),
	 (1962,7,0,1,2,5,11,14),
	 (1962,61,0,1,2,1,7,15),
	 (1962,52,0,0,3,2,8,16),
	 (1966,57,5,1,0,11,3,1),
	 (1966,78,4,1,1,15,6,2),
	 (1966,62,5,0,1,17,7,3),
	 (1966,83,4,0,2,10,6,4);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1966,2,2,1,1,4,2,5),
	 (1966,51,2,0,2,8,7,6),
	 (1966,1,1,2,1,2,5,7),
	 (1966,86,1,1,2,5,9,8),
	 (1966,47,1,0,2,2,2,9),
	 (1966,50,1,0,2,4,5,10),
	 (1966,4,1,0,2,4,6,11),
	 (1966,11,0,2,1,1,3,12),
	 (1966,3,0,1,2,2,5,13),
	 (1966,46,0,1,2,2,5,14);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1966,61,0,0,3,1,8,15),
	 (1966,52,0,0,3,1,9,16),
	 (1970,4,6,0,0,19,7,1),
	 (1970,47,3,2,1,10,8,2),
	 (1970,78,5,0,1,17,10,3),
	 (1970,1,2,1,3,4,6,4),
	 (1970,83,2,1,1,6,2,5),
	 (1970,11,2,1,1,6,4,6),
	 (1970,6,2,0,2,9,9,7),
	 (1970,57,2,0,2,4,4,8);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1970,53,1,1,1,2,2,9),
	 (1970,82,1,0,2,4,5,10),
	 (1970,81,1,0,2,4,5,11),
	 (1970,74,0,2,1,1,3,12),
	 (1970,61,0,1,2,5,9,13),
	 (1970,22,0,1,2,2,6,14),
	 (1970,85,0,0,3,2,7,15),
	 (1970,14,0,0,3,0,9,16),
	 (1974,78,6,0,1,13,4,1),
	 (1974,54,5,1,1,15,3,2);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1974,55,6,0,1,16,5,3),
	 (1974,4,3,2,3,6,4,4),
	 (1974,53,2,2,2,7,6,5),
	 (1974,80,2,2,2,5,5,6),
	 (1974,75,1,2,3,12,7,7),
	 (1974,2,1,2,3,9,12,8),
	 (1974,58,1,2,0,3,1,9),
	 (1974,47,1,1,1,5,4,10),
	 (1974,3,0,2,1,1,2,11),
	 (1974,61,0,2,1,2,5,12);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1974,1,0,1,2,1,6,13),
	 (1974,44,0,1,2,0,5,14),
	 (1974,15,0,0,3,2,14,15),
	 (1974,23,0,0,3,0,14,16),
	 (1978,2,5,1,1,15,4,1),
	 (1978,54,3,2,2,15,10,2),
	 (1978,4,4,3,0,10,3,3),
	 (1978,47,4,1,2,9,6,4),
	 (1978,55,3,1,2,6,6,5),
	 (1978,49,3,0,3,7,10,6);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1978,78,1,4,1,10,5,7),
	 (1978,6,2,1,3,7,12,8),
	 (1978,24,1,1,1,3,2,9),
	 (1978,50,1,1,1,2,2,10),
	 (1978,58,1,1,1,5,6,11),
	 (1978,46,1,0,2,5,5,12),
	 (1978,53,0,1,2,1,3,13),
	 (1978,35,0,1,2,2,8,14),
	 (1978,51,0,0,3,3,8,15),
	 (1978,11,0,0,3,2,12,16);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1982,47,4,3,0,12,6,1),
	 (1982,78,3,2,2,12,10,2),
	 (1982,55,3,3,1,11,5,3),
	 (1982,46,3,2,2,16,12,4),
	 (1982,4,4,0,1,15,6,5),
	 (1982,57,3,2,0,6,1,6),
	 (1982,83,2,2,1,7,4,7),
	 (1982,49,2,1,2,5,4,8),
	 (1982,59,1,3,1,5,7,9),
	 (1982,82,2,1,2,3,4,10);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1982,2,2,0,3,8,7,11),
	 (1982,50,1,2,2,4,5,12),
	 (1982,25,2,0,1,5,5,13),
	 (1982,51,1,1,1,12,6,14),
	 (1982,58,1,1,1,8,8,15),
	 (1982,75,1,1,1,2,2,16),
	 (1982,26,0,3,0,1,1,17),
	 (1982,13,0,2,1,2,3,18),
	 (1982,85,0,2,1,2,4,19),
	 (1982,6,0,2,1,2,6,20);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1982,36,0,1,2,2,6,21),
	 (1982,3,0,0,3,3,8,22),
	 (1982,45,0,0,3,2,12,23),
	 (1982,14,0,0,3,1,13,24),
	 (1986,2,6,1,0,14,5,1),
	 (1986,78,3,2,2,8,7,2),
	 (1986,46,4,2,1,12,6,3),
	 (1986,82,2,2,3,12,15,4),
	 (1986,4,4,1,0,10,1,5),
	 (1986,11,3,2,0,6,2,6);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1986,50,3,1,1,11,4,7),
	 (1986,57,2,1,2,7,3,8),
	 (1986,72,3,0,1,10,6,9),
	 (1986,83,2,1,1,12,5,10),
	 (1986,22,1,2,1,3,2,11),
	 (1986,47,1,2,1,5,7,12),
	 (1986,55,1,1,2,1,7,14),
	 (1986,61,0,2,2,2,6,15),
	 (1986,1,0,2,2,2,8,16),
	 (1986,62,1,0,2,2,4,17);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1986,51,1,0,2,2,9,18),
	 (1986,58,0,1,2,1,3,19),
	 (1986,5,1,2,1,4,6,19),
	 (1986,34,0,1,2,4,7,20),
	 (1986,59,0,1,2,2,6,21),
	 (1986,25,0,1,2,1,5,22),
	 (1986,43,0,0,3,1,4,23),
	 (1986,16,0,0,3,0,5,24),
	 (1990,78,5,2,0,15,5,1),
	 (1990,2,2,3,2,5,4,2);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1990,47,6,1,0,10,2,3),
	 (1990,57,3,3,1,8,6,4),
	 (1990,75,3,1,1,8,6,5),
	 (1990,85,3,0,2,10,5,6),
	 (1990,26,3,0,2,7,9,7),
	 (1990,71,0,4,1,2,3,8),
	 (1990,4,3,0,1,4,2,9),
	 (1990,50,2,1,1,6,4,10),
	 (1990,81,1,2,1,4,3,11),
	 (1990,82,2,0,2,6,4,12);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1990,17,2,0,2,4,6,13),
	 (1990,54,0,3,1,3,4,14),
	 (1990,7,1,1,2,4,4,15),
	 (1990,1,1,1,2,2,5,16),
	 (1990,83,1,0,2,4,4,17),
	 (1990,49,1,0,2,2,3,18),
	 (1990,58,1,0,2,2,3,19),
	 (1990,21,0,2,1,1,2,20),
	 (1990,53,0,0,3,3,6,21),
	 (1990,34,0,0,3,1,6,22);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1990,10,0,0,3,2,8,23),
	 (1990,37,0,0,3,2,11,24),
	 (1994,4,5,2,0,11,3,1),
	 (1994,47,4,2,1,8,5,2),
	 (1994,53,3,3,1,15,8,3),
	 (1994,61,3,1,3,10,11,4),
	 (1994,81,3,1,1,10,9,5),
	 (1994,48,3,1,1,9,7,6),
	 (1994,54,3,0,2,8,6,7),
	 (1994,50,2,2,1,10,6,8);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1994,27,2,0,2,7,4,9),
	 (1994,2,2,0,2,8,6,10),
	 (1994,82,2,0,2,4,4,11),
	 (1994,38,2,0,2,5,6,12),
	 (1994,11,1,2,1,4,4,13),
	 (1994,10,1,1,2,3,4,14),
	 (1994,52,1,1,2,5,7,15),
	 (1994,71,1,1,2,2,4,16),
	 (1994,56,1,1,1,1,1,17),
	 (1994,79,1,0,2,7,6,18);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1994,7,1,0,2,4,5,19),
	 (1994,34,0,2,1,4,5,20),
	 (1994,9,0,1,2,1,4,21),
	 (1994,26,0,1,2,3,11,22),
	 (1994,22,0,0,3,2,5,23),
	 (1994,63,0,0,3,0,10,24),
	 (1998,46,6,1,0,15,2,1),
	 (1998,4,4,1,2,14,10,2),
	 (1998,64,5,0,2,11,5,3),
	 (1998,54,3,3,1,13,7,4);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1998,47,3,2,0,8,3,5),
	 (1998,2,3,1,1,10,4,6),
	 (1998,48,3,1,1,8,6,7),
	 (1998,72,2,1,2,9,7,8),
	 (1998,57,2,1,1,7,4,9),
	 (1998,76,2,1,1,5,4,10),
	 (1998,81,2,1,1,4,3,11),
	 (1998,27,2,0,2,6,9,12),
	 (1998,11,1,2,1,8,7,13),
	 (1998,5,1,2,1,3,2,14);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1998,56,1,2,1,5,5,15),
	 (1998,3,0,3,1,5,8,16),
	 (1998,50,1,1,1,8,4,17),
	 (1998,22,1,1,1,5,5,18),
	 (1998,82,0,3,0,3,3,19),
	 (1998,35,1,0,2,2,4,20),
	 (1998,7,1,0,2,1,3,21),
	 (1998,18,1,0,2,3,9,22),
	 (1998,49,0,2,1,3,4,23),
	 (1998,28,0,2,1,3,6,24);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (1998,26,0,2,1,2,5,25),
	 (1998,24,0,1,2,1,4,26),
	 (1998,58,0,1,2,2,6,27),
	 (1998,38,0,1,2,2,7,28),
	 (1998,61,0,1,2,1,7,29),
	 (1998,34,0,1,2,2,9,30),
	 (1998,39,0,0,3,1,4,31),
	 (1998,10,0,0,3,1,5,32),
	 (2002,4,7,0,0,18,4,1),
	 (2002,48,5,1,1,14,3,2);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2002,73,4,1,2,10,6,3),
	 (2002,34,3,2,2,8,6,4),
	 (2002,50,3,2,0,10,5,5),
	 (2002,57,2,2,1,6,3,6),
	 (2002,29,2,2,1,7,6,7),
	 (2002,10,2,1,2,7,7,8),
	 (2002,39,2,1,1,5,6,9),
	 (2002,72,2,1,1,5,5,10),
	 (2002,11,2,1,1,4,4,11),
	 (2002,71,1,3,0,6,3,12);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2002,53,1,2,1,5,5,13),
	 (2002,82,1,2,1,6,7,14),
	 (2002,47,1,1,2,5,5,15),
	 (2002,5,1,1,2,6,7,16),
	 (2002,28,1,1,1,5,5,17),
	 (2002,2,1,1,1,2,2,18),
	 (2002,17,1,1,1,5,6,19),
	 (2002,26,1,1,1,2,3,20),
	 (2002,62,1,0,2,6,4,21),
	 (2002,79,1,0,2,4,4,22);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2002,64,1,0,2,2,3,23),
	 (2002,8,1,0,2,2,4,24),
	 (2002,55,1,0,2,3,7,25),
	 (2002,1,0,2,1,4,5,26),
	 (2002,27,0,1,2,1,3,27),
	 (2002,46,0,1,2,0,3,28),
	 (2002,24,0,1,2,1,5,29),
	 (2002,65,0,0,3,2,7,30),
	 (2002,40,0,0,3,0,9,31),
	 (2002,38,0,0,3,0,12,32);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2006,47,5,2,0,12,2,1),
	 (2006,46,4,3,0,9,3,2),
	 (2006,48,4,1,1,11,5,3),
	 (2006,62,4,1,2,7,5,4),
	 (2006,4,4,0,1,10,2,5),
	 (2006,2,3,2,0,11,3,6),
	 (2006,57,1,1,0,1,0,7),
	 (2006,66,2,1,2,5,7,8),
	 (2006,50,3,0,1,9,4,9),
	 (2006,52,2,2,0,4,0,10);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2006,54,2,1,1,3,2,11),
	 (2006,8,2,0,2,5,4,12),
	 (2006,30,2,0,2,4,6,13),
	 (2006,53,1,2,1,3,4,14),
	 (2006,11,1,1,2,5,5,15),
	 (2006,44,1,1,2,5,6,16),
	 (2006,34,1,1,1,3,4,17),
	 (2006,5,1,0,2,2,2,18),
	 (2006,31,1,0,2,5,6,19),
	 (2006,77,1,0,2,3,4,20);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2006,55,1,0,2,2,4,21),
	 (2006,64,0,2,1,2,3,22),
	 (2006,32,0,2,1,1,2,23),
	 (2006,24,0,1,2,3,6,24),
	 (2006,10,0,1,2,2,6,25),
	 (2006,35,0,1,2,2,6,26),
	 (2006,19,0,1,2,0,4,27),
	 (2006,38,0,1,2,2,7,28),
	 (2006,39,0,1,2,2,7,29),
	 (2006,33,0,0,3,1,6,30);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2006,17,0,0,3,3,9,31),
	 (2006,76,0,0,3,2,10,32),
	 (2010,50,6,0,1,8,2,1),
	 (2010,54,6,0,1,12,6,2),
	 (2010,48,5,0,2,16,5,3),
	 (2010,1,3,2,2,11,8,4),
	 (2010,2,4,0,1,10,6,5),
	 (2010,4,3,1,1,9,4,6),
	 (2010,30,2,2,1,5,4,7),
	 (2010,5,1,3,1,3,2,8);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2010,39,2,1,1,4,2,9),
	 (2010,3,2,0,2,3,5,10),
	 (2010,62,1,2,1,7,1,11),
	 (2010,10,1,2,1,5,5,12),
	 (2010,57,1,2,1,3,5,13),
	 (2010,11,1,1,2,4,5,14),
	 (2010,34,1,1,2,6,8,15),
	 (2010,67,1,1,2,5,7,16),
	 (2010,31,1,1,1,4,3,17),
	 (2010,65,1,1,1,3,3,18);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2010,52,1,1,1,1,1,19),
	 (2010,28,1,1,1,3,5,20),
	 (2010,44,1,1,1,3,6,21),
	 (2010,45,0,3,0,2,2,22),
	 (2010,70,1,0,2,2,3,23),
	 (2010,72,1,0,2,3,6,24),
	 (2010,63,1,0,2,2,5,25),
	 (2010,47,0,2,1,4,5,26),
	 (2010,27,0,1,2,3,5,27),
	 (2010,25,0,1,2,0,2,28);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2010,46,0,1,2,1,4,29),
	 (2010,13,0,1,2,0,3,30),
	 (2010,26,0,0,3,2,5,31),
	 (2010,86,0,0,3,1,12,32),
	 (2014,48,6,1,0,18,4,1),
	 (2014,2,5,1,1,8,4,2),
	 (2014,54,5,2,0,15,4,3),
	 (2014,4,3,2,2,11,14,4),
	 (2014,7,4,0,1,12,4,5),
	 (2014,82,4,0,1,6,3,6);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2014,46,3,1,1,10,3,7),
	 (2014,17,2,3,0,5,2,8),
	 (2014,3,2,1,1,6,4,9),
	 (2014,11,2,1,1,5,3,10),
	 (2014,52,2,0,2,7,7,11),
	 (2014,1,2,0,2,4,6,12),
	 (2014,63,1,2,1,3,5,13),
	 (2014,25,1,1,2,7,7,14),
	 (2014,10,1,1,2,5,6,15),
	 (2014,27,1,1,2,3,5,16);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2014,8,1,1,1,3,3,17),
	 (2014,62,1,1,1,4,7,18),
	 (2014,64,1,0,2,6,6,19),
	 (2014,68,1,0,2,4,4,20),
	 (2014,31,1,0,2,4,5,21),
	 (2014,47,1,0,2,2,3,22),
	 (2014,50,1,0,2,4,7,23),
	 (2014,79,0,2,1,2,3,24),
	 (2014,30,0,1,2,4,6,25),
	 (2014,57,0,1,2,2,4,26);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2014,34,0,1,2,3,6,27),
	 (2014,35,0,1,2,1,4,28),
	 (2014,39,0,1,2,2,6,29),
	 (2014,44,0,0,3,3,9,30),
	 (2014,13,0,0,3,1,8,31),
	 (2014,26,0,0,3,1,9,32),
	 (2018,46,6,1,0,14,6,1),
	 (2018,82,6,0,1,16,6,2),
	 (2018,64,5,1,1,14,9,3),
	 (2018,1,4,0,1,7,3,4);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2018,4,3,1,1,8,3,5),
	 (2018,57,3,1,3,12,8,6),
	 (2018,2,3,1,2,6,9,7),
	 (2018,53,3,0,2,6,4,8),
	 (2018,79,2,2,1,11,7,9),
	 (2018,7,2,1,1,6,3,10),
	 (2018,72,1,3,0,2,2,11),
	 (2018,11,2,0,2,3,6,12),
	 (2018,50,1,2,0,6,5,13),
	 (2018,62,1,2,1,6,6,14);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2018,52,1,2,1,5,5,15),
	 (2018,35,1,1,1,2,2,16),
	 (2018,39,1,1,1,4,4,17),
	 (2018,29,1,1,1,4,4,18),
	 (2018,34,1,0,2,3,3,19),
	 (2018,6,1,0,2,2,2,20),
	 (2018,27,1,0,2,3,4,21),
	 (2018,48,1,0,2,2,4,22),
	 (2018,70,1,0,2,2,4,23),
	 (2018,55,1,0,2,2,5,24);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2018,24,1,0,2,5,8,25),
	 (2018,38,1,0,2,2,7,26),
	 (2018,22,0,1,2,2,4,27),
	 (2018,17,0,1,2,2,5,28),
	 (2018,69,0,1,2,2,5,29),
	 (2018,44,0,1,2,2,5,30),
	 (2018,21,0,0,3,2,6,31),
	 (2018,20,0,0,3,2,11,32),
	 (2022,2,4,2,1,15,8,1),
	 (2022,46,5,1,1,16,8,2);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2022,64,2,4,1,8,7,3),
	 (2022,22,3,2,2,6,5,4),
	 (2022,54,3,2,0,10,4,5),
	 (2022,57,3,1,1,13,4,6),
	 (2022,4,3,1,1,8,3,7),
	 (2022,62,3,0,2,12,6,8),
	 (2022,39,2,1,1,5,4,9),
	 (2022,29,2,0,2,5,7,10),
	 (2022,44,2,0,2,4,6,11),
	 (2022,52,2,0,2,5,9,12);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2022,50,1,2,1,9,3,13),
	 (2022,10,1,2,1,3,4,14),
	 (2022,55,1,1,2,3,5,15),
	 (2022,34,1,1,2,5,8,16),
	 (2022,48,1,1,1,6,5,17),
	 (2022,8,1,1,1,4,3,18),
	 (2022,26,1,1,1,4,4,19),
	 (2022,1,1,1,1,2,2,20),
	 (2022,24,1,1,1,1,1,21),
	 (2022,11,1,1,1,2,3,22);
INSERT INTO wcf_participacao (fk_ano_torneio,fk_selecao,vitorias,empates,derrotas,gols_feitos,gols_sofridos,classificacao_final) VALUES
	 (2022,82,1,1,1,1,2,23),
	 (2022,30,1,0,2,5,7,24),
	 (2022,38,1,0,2,3,5,25),
	 (2022,35,1,0,2,4,7,26),
	 (2022,17,1,0,3,3,11,27),
	 (2022,72,0,1,2,1,3,28),
	 (2022,70,0,1,2,5,8,29),
	 (2022,60,0,1,2,1,6,30),
	 (2022,16,0,0,3,2,7,31),
	 (2022,41,0,0,3,1,7,32);
