
ALTER TABLE `ipv4class` 
CHANGE COLUMN `status` `status` ENUM('TODO','FINISHED','ONHOLD') 
CHARACTER SET 'utf8' COLLATE 'utf8_polish_ci' NOT NULL DEFAULT 'TODO' 
COMMENT 'TODO: status in separate table (?)' ,
ADD COLUMN `description` VARCHAR(45) NULL AFTER `status`;
