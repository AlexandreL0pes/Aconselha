-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema sadc
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema sadc
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `sadc` DEFAULT CHARACTER SET utf8 ;
-- -----------------------------------------------------
-- Schema sadc
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema sadc
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `sadc` DEFAULT CHARACTER SET utf8 ;
USE `sadc` ;
USE `sadc` ;

-- -----------------------------------------------------
-- Table `sadc`.`Acao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sadc`.`Acao` (
  `idAcao` INT(11) NOT NULL,
  `nome` VARCHAR(45) NULL DEFAULT NULL,
  `descricao` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`idAcao`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sadc`.`Reuniao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sadc`.`Reuniao` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `COD_TURMA` VARCHAR(50) NOT NULL,
  `data` DATE NULL DEFAULT NULL,
  `etapaConselho` VARCHAR(20) NULL DEFAULT NULL,
  `finalizado` INT(11) NULL DEFAULT '0',
  `memoria` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 30
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sadc`.`Diagnostica`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sadc`.`Diagnostica` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idReuniao` INT(11) NOT NULL,
  `COD_PROFESSOR` VARCHAR(50) NOT NULL,
  `COD_MATRICULA` VARCHAR(50) NOT NULL,
  `data` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_Diagnostica_Reuniao1_idx` (`idReuniao` ASC),
  CONSTRAINT `fk_Diagnostica_Reuniao1`
    FOREIGN KEY (`idReuniao`)
    REFERENCES `sadc`.`Reuniao` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 20
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sadc`.`Perfil`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sadc`.`Perfil` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(30) NULL DEFAULT NULL,
  `descricao` VARCHAR(70) NULL DEFAULT NULL,
  `tipo` BINARY NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 9
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sadc`.`Analise`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sadc`.`Analise` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idDiagnostica` INT(11) NOT NULL,
  `idPerfil` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_Analise_Diagnostica1_idx` (`idDiagnostica` ASC),
  INDEX `fk_Analise_Perfil1_idx` (`idPerfil` ASC),
  CONSTRAINT `fk_Analise_Diagnostica1`
    FOREIGN KEY (`idDiagnostica`)
    REFERENCES `sadc`.`Diagnostica` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Analise_Perfil1`
    FOREIGN KEY (`idPerfil`)
    REFERENCES `sadc`.`Perfil` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 101
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sadc`.`Aprendizado`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sadc`.`Aprendizado` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idReuniao` INT(11) NOT NULL,
  `COD_PAUTA` VARCHAR(50) NOT NULL,
  `data` DATETIME NULL DEFAULT NULL,
  `observacao` VARCHAR(300) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_Aprendizado_Reuniao1_idx` (`idReuniao` ASC),
  CONSTRAINT `fk_Aprendizado_Reuniao1`
    FOREIGN KEY (`idReuniao`)
    REFERENCES `sadc`.`Reuniao` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 10
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sadc`.`Atendimento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sadc`.`Atendimento` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idReuniao` INT(11) NOT NULL,
  `COD_MATRICULA` VARCHAR(50) NOT NULL,
  `data` DATETIME NULL DEFAULT NULL,
  `observacao` VARCHAR(300) NULL DEFAULT NULL,
  `idAcao` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_Atendimento_Reuniao1_idx` (`idReuniao` ASC),
  INDEX `fk_Atendimento_Acao1_idx` (`idAcao` ASC),
  CONSTRAINT `fk_Atendimento_Acao1`
    FOREIGN KEY (`idAcao`)
    REFERENCES `sadc`.`Acao` (`idAcao`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Atendimento_Reuniao1`
    FOREIGN KEY (`idReuniao`)
    REFERENCES `sadc`.`Reuniao` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sadc`.`Classificacao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sadc`.`Classificacao` (
  `idClassificacao` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(50) NULL DEFAULT NULL,
  `descricao` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`idClassificacao`))
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sadc`.`Experiencia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sadc`.`Experiencia` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idReuniao` INT(11) NOT NULL,
  `titulo` VARCHAR(45) NULL DEFAULT NULL,
  `observacao` VARCHAR(1000) NULL DEFAULT NULL,
  `data` DATETIME NULL DEFAULT NULL,
  `idClassificacao` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_Experiencia_Reuniao1_idx` (`idReuniao` ASC),
  INDEX `fk_Experiencia_Classificacao1_idx` (`idClassificacao` ASC),
  INDEX `fk_Experiencia_Classificacao1_idx1` (`idClassificacao` ASC),
  CONSTRAINT `fk_Experiencia_Classificacao1`
    FOREIGN KEY (`idClassificacao`)
    REFERENCES `sadc`.`Classificacao` (`idClassificacao`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Experiencia_Reuniao1`
    FOREIGN KEY (`idReuniao`)
    REFERENCES `sadc`.`Reuniao` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 23
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sadc`.`DisciplinaExperiencia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sadc`.`DisciplinaExperiencia` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `COD_PAUTA` INT(11) NULL DEFAULT NULL,
  `idExperiencia` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_DisciplinasExperiencia_Experiencia1_idx` (`idExperiencia` ASC),
  CONSTRAINT `fk_DisciplinasExperiencia_Experiencia1`
    FOREIGN KEY (`idExperiencia`)
    REFERENCES `sadc`.`Experiencia` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 75
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sadc`.`Encaminhamento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sadc`.`Encaminhamento` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `COD_PROFESSOR` VARCHAR(50) NOT NULL,
  `observacao` VARCHAR(100) NULL DEFAULT NULL,
  `idAtendimento` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_Encaminhamento_Atendimento1_idx` (`idAtendimento` ASC),
  CONSTRAINT `fk_Encaminhamento_Atendimento1`
    FOREIGN KEY (`idAtendimento`)
    REFERENCES `sadc`.`Atendimento` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sadc`.`EstudanteAprendizado`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sadc`.`EstudanteAprendizado` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `COD_MATRICULA` VARCHAR(45) NULL DEFAULT NULL,
  `idAprendizado` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_EstudanteAvaliacao_Aprendizado1_idx` (`idAprendizado` ASC),
  CONSTRAINT `fk_EstudanteAvaliacao_Aprendizado1`
    FOREIGN KEY (`idAprendizado`)
    REFERENCES `sadc`.`Aprendizado` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 38
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sadc`.`Usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sadc`.`Usuario` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `COD_MATRICULA` VARCHAR(50) NULL DEFAULT NULL,
  `COD_TURMA` VARCHAR(45) NULL DEFAULT NULL,
  `COD_CURSO` VARCHAR(45) NULL DEFAULT NULL,
  `data_inicio` DATE NULL DEFAULT NULL,
  `data_fim` DATE NULL DEFAULT NULL,
  `senha` VARCHAR(255) NULL DEFAULT NULL,
  `COD_PESSOA` INT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sadc`.`Permissao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sadc`.`Permissao` (
  `usuario` INT(11) NOT NULL,
  `acesso` INT NOT NULL,
  INDEX `fk_Permissao_Usuario1_idx` (`usuario` ASC),
  PRIMARY KEY (`acesso`, `usuario`),
  CONSTRAINT `fk_Permissao_Usuario1`
    FOREIGN KEY (`usuario`)
    REFERENCES `sadc`.`Usuario` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
