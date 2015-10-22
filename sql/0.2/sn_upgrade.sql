

CREATE  TABLE `sn`.`host` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NOT NULL ,
  `n` INT NOT NULL DEFAULT 2 ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
ENGINE = InnoDB;

ALTER TABLE `sn`.`host` CHANGE COLUMN `n` `n` INT(11) NOT NULL DEFAULT '2' COMMENT 'maksymalna ilość nodów'  ;

ALTER TABLE `sn`.`node` ADD COLUMN `hid` INT NOT NULL COMMENT 'host id'  AFTER `status` ;
