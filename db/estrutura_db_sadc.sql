-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema myDb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema myDb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `myDb` DEFAULT CHARACTER SET utf8 ;
USE `myDb` ;

-- -----------------------------------------------------
-- Table `myDb`.`TurmaConselho`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `myDb`.`TurmaConselho` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `COD_TURMA` INT NOT NULL,
  `data` DATETIME NULL,
  `etapaConselho` VARCHAR(20) NULL,
  `finalizado` INT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myDb`.`Perfil`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `myDb`.`Perfil` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(30) NULL,
  `descricao` VARCHAR(70) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myDb`.`Acao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `myDb`.`Acao` (
  `id` INT NOT NULL,
  `nome` VARCHAR(45) NULL,
  `descricao` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myDb`.`Avaliacao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `myDb`.`Avaliacao` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idTurmaConselho` INT NULL,
  `COD_PROFESSOR` INT NULL,
  `COD_MATRICULA` INT NULL,
  `idPerfil` INT NULL,
  `data` DATE NULL,
  `observacao` VARCHAR(300) NULL,
  `COD_PAUTA` INT NULL,
  `idAcao` INT NULL,
  INDEX `fk_Conselho_has_Turma_has_Aluno_Conselho_has_Turma1_idx` (`idTurmaConselho` ASC) ,
  PRIMARY KEY (`id`),
  INDEX `fk_Avaliacao_Diagnostica_ClassificacaoEstudante1_idx` (`idPerfil` ASC) ,
  INDEX `fk_Avaliacao_acao1_idx` (`idAcao` ASC) ,
  CONSTRAINT `fk_Conselho_has_Turma_has_Aluno_Conselho_has_Turma1`
    FOREIGN KEY (`idTurmaConselho`)
    REFERENCES `myDb`.`TurmaConselho` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Avaliacao_Diagnostica_ClassificacaoEstudante1`
    FOREIGN KEY (`idPerfil`)
    REFERENCES `myDb`.perfil (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Avaliacao_acao1`
    FOREIGN KEY (`idAcao`)
    REFERENCES `myDb`.`Acao` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myDb`.`Classificacao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `myDb`.`Classificacao` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NULL,
  `descricao` VARCHAR(100) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myDb`.`Assunto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `myDb`.`Assunto` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idTurmaConselho` INT NOT NULL,
  `idClassificacao` INT NOT NULL,
  `observacao` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_Assunto_Conselho_has_Turma1_idx` (`idTurmaConselho` ASC) ,
  INDEX `fk_Assunto_Classificacao_Assunto1_idx` (`idClassificacao` ASC) ,
  CONSTRAINT `fk_Assunto_Conselho_has_Turma1`
    FOREIGN KEY (`idTurmaConselho`)
    REFERENCES `myDb`.`TurmaConselho` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Assunto_Classificacao_Assunto1`
    FOREIGN KEY (`idClassificacao`)
    REFERENCES `myDb`.`Classificacao` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myDb`.`Encaminhamento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `myDb`.`Encaminhamento` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idAvaliacao` INT NOT NULL,
  `COD_PROFESSOR` INT NOT NULL,
  `observacao` VARCHAR(100) NULL,
  INDEX `fk_Encaminhamento_Atendimento_Avaliacao_Diagnostica1_idx` (`idAvaliacao` ASC) ,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_Encaminhamento_Atendimento_Avaliacao_Diagnostica1`
    FOREIGN KEY (`idAvaliacao`)
    REFERENCES `myDb`.`Avaliacao` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myDb`.`Conselheiro`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `myDb`.`Conselheiro` (
  `id` INT NOT NULL,
  `COD_PROFESSOR` INT NULL,
  `COD_TURMA` INT NULL,
  `data_inicio` DATE NULL,
  `data_fim` DATE NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myDb`.`Representante`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `myDb`.`Representante` (
  `id` INT NOT NULL,
  `COD_MATRICULA` INT NULL,
  `COD_TURMA` VARCHAR(50) NULL,
  `data_inicio` DATE NULL,
  `data_fim` DATE NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myDb`.`Coordenador`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `myDb`.`Coordenador` (
  `ID` INT NOT NULL,
  `COD_PROFESSOR` INT NULL,
  `COD_CURSO` INT NULL,
  `data_inicio` DATE NULL,
  `data_fim` DATE NULL,
  PRIMARY KEY (`ID`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
