DROP DATABASE IF EXISTS LUDIFY;

CREATE DATABASE LUDIFY;

USE LUDIFY;

CREATE TABLE Usuario (
    ID_Usuario INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Email VARCHAR(80),
    Senha VARCHAR(20)
);

CREATE TABLE Jogo (
    ID_Jogo INT AUTO_INCREMENT PRIMARY KEY,
    Titulo VARCHAR(100),
    ID_Classificacao INT,
    ID_Desenvolvedora INT,
    ID_Genero INT,
    Imagem VARCHAR(255)
);

CREATE TABLE Aluguel (
    ID_Aluguel INT PRIMARY KEY,
    Data_Inicio DATE,
    Data_Fim DATE,
    ID_Jogo INT,
    ID_Usuario INT
);

CREATE TABLE Admin (
    ID_Admin INT PRIMARY KEY,
    Email VARCHAR(50),
    Senha VARCHAR(8)
);

CREATE TABLE Classificacao (
    ID_Classificacao INT PRIMARY KEY,
    Descricao VARCHAR(100)
);

CREATE TABLE Desenvolvedora (
    ID_Desenvolvedora INT PRIMARY KEY,
    Nome VARCHAR(50)
);

CREATE TABLE Genero (
    ID_Genero INT PRIMARY KEY,
    Nome VARCHAR(50)
);

CREATE TABLE Gerencia (
    ID_Gerencia INT PRIMARY KEY,
    ID_Admin INT,
    ID_Jogo INT,
    Tipo VARCHAR(20)
);

CREATE TABLE Pertence (
    ID_Pertence INT PRIMARY KEY,
    ID_Genero INT,
    ID_Jogo INT
);

-- Definição de chaves estrangeiras

ALTER TABLE Jogo ADD CONSTRAINT FK_Jogo_Classificacao
    FOREIGN KEY (ID_Classificacao)
    REFERENCES Classificacao (ID_Classificacao)
    ON DELETE SET NULL;

ALTER TABLE Jogo ADD CONSTRAINT FK_Jogo_Desenvolvedora
    FOREIGN KEY (ID_Desenvolvedora)
    REFERENCES Desenvolvedora (ID_Desenvolvedora)
    ON DELETE CASCADE;

ALTER TABLE Aluguel ADD CONSTRAINT FK_Aluguel_Jogo
    FOREIGN KEY (ID_Jogo)
    REFERENCES Jogo (ID_Jogo);

ALTER TABLE Aluguel ADD CONSTRAINT FK_Aluguel_Usuario
    FOREIGN KEY (ID_Usuario)
    REFERENCES Usuario (ID_Usuario);

ALTER TABLE Gerencia ADD CONSTRAINT FK_Gerencia_Admin
    FOREIGN KEY (ID_Admin)
    REFERENCES Admin (ID_Admin)
    ON DELETE RESTRICT;

ALTER TABLE Gerencia ADD CONSTRAINT FK_Gerencia_Jogo
    FOREIGN KEY (ID_Jogo)
    REFERENCES Jogo (ID_Jogo)
    ON DELETE SET NULL;

ALTER TABLE Pertence ADD CONSTRAINT FK_Pertence_Genero
    FOREIGN KEY (ID_Genero)
    REFERENCES Genero (ID_Genero)
    ON DELETE RESTRICT;

ALTER TABLE Pertence ADD CONSTRAINT FK_Pertence_Jogo
    FOREIGN KEY (ID_Jogo)
    REFERENCES Jogo (ID_Jogo)
    ON DELETE SET NULL;
    
INSERT INTO Genero (ID_Genero, Nome)
VALUES
    (1, 'Ação e Aventura'),
    (2, 'Terror e Suspense'),
    (3, 'Fantasia'),
    (4, 'Guerra'),
    (5, 'Esporte'),
    (6, 'Família'),
    (7, 'Mistério');

INSERT INTO Classificacao (ID_Classificacao, Descricao) 
VALUES 
    (1, 'Somente para maiores de 18'),
    (2, 'Livre'),
    (3, 'Somente para maiores de 16'),
    (4, 'Somente para maiores de 14'),
    (5, 'Somente para maiores de 13'),
    (6, 'Somente para maiores de 12'),
    (7, 'Somente para maiores de 10');


INSERT INTO Desenvolvedora (ID_Desenvolvedora, Nome)
VALUES
    (1, 'RiotGames'),
    (2, 'Mojang'),
    (3, 'Blizzard')