-- ============================================================
-- SISTEMA ACADEMICO - Base de Dados
-- Compativel com XAMPP (MySQL 5.7+ / MariaDB 10.3+)
-- IMPORTAR: phpMyAdmin > Import > selecionar este ficheiro
-- OU terminal: mysql -u root < database.sql
-- Password de todos os utilizadores de demo: password
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `sistema_academico`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sistema_academico`;

CREATE TABLE `utilizadores` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `perfil` ENUM('aluno','funcionario','gestor','admin') NOT NULL DEFAULT 'aluno',
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cursos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(200) NOT NULL,
  `codigo` VARCHAR(20) NOT NULL,
  `descricao` TEXT,
  `duracao_anos` TINYINT UNSIGNED NOT NULL DEFAULT 3,
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  `criado_por` INT UNSIGNED NOT NULL,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_codigo_curso` (`codigo`),
  FOREIGN KEY (`criado_por`) REFERENCES `utilizadores`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `unidades_curriculares` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(200) NOT NULL,
  `codigo` VARCHAR(20) NOT NULL,
  `descricao` TEXT,
  `creditos` TINYINT UNSIGNED NOT NULL DEFAULT 6,
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  `criado_por` INT UNSIGNED NOT NULL,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_codigo_uc` (`codigo`),
  FOREIGN KEY (`criado_por`) REFERENCES `utilizadores`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `plano_estudos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `curso_id` INT UNSIGNED NOT NULL,
  `uc_id` INT UNSIGNED NOT NULL,
  `ano` TINYINT UNSIGNED NOT NULL COMMENT '1,2 ou 3',
  `semestre` TINYINT UNSIGNED NOT NULL COMMENT '1 ou 2',
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_plano` (`curso_id`,`uc_id`,`ano`,`semestre`),
  FOREIGN KEY (`curso_id`) REFERENCES `cursos`(`id`),
  FOREIGN KEY (`uc_id`) REFERENCES `unidades_curriculares`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fichas_aluno` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `utilizador_id` INT UNSIGNED NOT NULL,
  `curso_id` INT UNSIGNED NOT NULL,
  `data_nascimento` DATE,
  `nif` VARCHAR(20),
  `telefone` VARCHAR(20),
  `morada` VARCHAR(255),
  `foto` VARCHAR(255) DEFAULT NULL,
  `estado` ENUM('rascunho','submetida','aprovada','rejeitada') NOT NULL DEFAULT 'rascunho',
  `observacoes` TEXT,
  `validado_por` INT UNSIGNED DEFAULT NULL,
  `validado_em` DATETIME DEFAULT NULL,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ficha` (`utilizador_id`),
  FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores`(`id`),
  FOREIGN KEY (`curso_id`) REFERENCES `cursos`(`id`),
  FOREIGN KEY (`validado_por`) REFERENCES `utilizadores`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `matriculas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `utilizador_id` INT UNSIGNED NOT NULL,
  `curso_id` INT UNSIGNED NOT NULL,
  `ano_letivo` VARCHAR(9) NOT NULL,
  `estado` ENUM('pendente','aprovada','rejeitada') NOT NULL DEFAULT 'pendente',
  `observacoes` TEXT,
  `decidido_por` INT UNSIGNED DEFAULT NULL,
  `decidido_em` DATETIME DEFAULT NULL,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores`(`id`),
  FOREIGN KEY (`curso_id`) REFERENCES `cursos`(`id`),
  FOREIGN KEY (`decidido_por`) REFERENCES `utilizadores`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pautas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uc_id` INT UNSIGNED NOT NULL,
  `ano_letivo` VARCHAR(9) NOT NULL,
  `epoca` ENUM('Normal','Recurso','Especial') NOT NULL DEFAULT 'Normal',
  `criado_por` INT UNSIGNED NOT NULL,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pauta` (`uc_id`,`ano_letivo`,`epoca`),
  FOREIGN KEY (`uc_id`) REFERENCES `unidades_curriculares`(`id`),
  FOREIGN KEY (`criado_por`) REFERENCES `utilizadores`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pauta_id` INT UNSIGNED NOT NULL,
  `utilizador_id` INT UNSIGNED NOT NULL,
  `nota_final` DECIMAL(4,1) DEFAULT NULL,
  `registado_por` INT UNSIGNED DEFAULT NULL,
  `registado_em` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_nota` (`pauta_id`,`utilizador_id`),
  FOREIGN KEY (`pauta_id`) REFERENCES `pautas`(`id`),
  FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores`(`id`),
  FOREIGN KEY (`registado_por`) REFERENCES `utilizadores`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DADOS DE DEMONSTRACAO (password de todos: password)
-- ============================================================
INSERT INTO `utilizadores` (`nome`,`email`,`password_hash`,`perfil`) VALUES
('Administrador Sistema','admin@academico.pt','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin'),
('Prof. Maria Gestora','gestor@academico.pt','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','gestor'),
('Joao Funcionario','funcionario@academico.pt','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','funcionario'),
('Ana Aluno Silva','aluno@academico.pt','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','aluno');

INSERT INTO `cursos` (`nome`,`codigo`,`descricao`,`duracao_anos`,`criado_por`) VALUES
('Licenciatura em Engenharia Informatica','LEI','Curso de engenharia com foco em software e sistemas.',3,2),
('Licenciatura em Gestao','LGE','Curso de gestao empresarial e administracao.',3,2),
('Licenciatura em Design Multimedia','LDM','Curso de design e comunicacao visual digital.',3,2);

INSERT INTO `unidades_curriculares` (`nome`,`codigo`,`descricao`,`creditos`,`criado_por`) VALUES
('Programacao I','PROG1','Fundamentos de programacao em Python.',6,2),
('Programacao II','PROG2','Programacao orientada a objetos.',6,2),
('Bases de Dados','BD1','Modelacao e SQL.',6,2),
('Redes de Computadores','RC1','Protocolos e arquitetura de redes.',6,2),
('Calculo','CALC','Analise matematica e calculo diferencial.',6,2),
('Gestao de Empresas','GE1','Fundamentos de gestao organizacional.',6,2);

INSERT INTO `plano_estudos` (`curso_id`,`uc_id`,`ano`,`semestre`) VALUES
(1,1,1,1),(1,5,1,1),(1,2,1,2),(1,3,2,1),(1,4,2,2),
(2,6,1,1),(2,5,1,2),
(3,1,1,1),(3,3,1,2);
