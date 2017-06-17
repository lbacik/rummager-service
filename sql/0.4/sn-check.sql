--
-- We have to resign with forign key here as 'net' column can be 'null' for SENDER module's check.
--

ALTER TABLE `check`
  DROP FOREIGN KEY `fk_net`;

ALTER TABLE `check`
  CHANGE COLUMN `net` `net` INT(11) NULL ,
  DROP INDEX `fk_net_idx` ;
