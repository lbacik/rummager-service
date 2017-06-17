
DROP TABLE IF EXISTS `smtp_sender`;
CREATE TABLE `smtp_sender` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `checkid` int(11) NOT NULL,
  `smtpid` int(11) NOT NULL,
  `status` VARCHAR(20) NOT NULL,
  `msg` TEXT NULL,
  `conn_log` TEXT NULL,
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `last_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_smtp_sender_check_idx` (`checkid` ASC),
  INDEX `fk_smtp_sender_smtp_idx` (`smtpid` ASC),
  CONSTRAINT `fk_smtp_sender_check` FOREIGN KEY (`checkid`) REFERENCES `check` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_smtp_sender_smtp` FOREIGN KEY (`smtpid`) REFERENCES `smtp` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

