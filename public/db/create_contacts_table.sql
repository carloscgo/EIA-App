-- -----------------------------------------------------
-- Table `db`.`contacts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db`.`contacts` ;

CREATE TABLE IF NOT EXISTS `db`.`contacts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `firstname` VARCHAR(45) NOT NULL,
  `lastname` VARCHAR(45) NOT NULL,
  `email` VARCHAR(200) NOT NULL,
  `phone` VARCHAR(15) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha creación',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha última modificación',
  `deleted_at` DATETIME NULL COMMENT 'Fecha de eliminacion',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Contactos';
