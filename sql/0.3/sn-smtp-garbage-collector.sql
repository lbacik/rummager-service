DROP PROCEDURE IF EXISTS `smtp_garbage_collector`;
DELIMITER ;;
CREATE PROCEDURE `smtp_garbage_collector`()
  BEGIN
    DELETE s
    FROM smtp s
      LEFT JOIN `check` c ON c.id = s.checkid
      LEFT JOIN ipv4class i ON c.net = i.id
    WHERE
      i.status = 'FINISHED'
      AND s.tcon IS NULL
      AND cast( s.checktime AS date )
          <= (SELECT `date` FROM dw_f_ip ORDER BY `date` DESC LIMIT 1);
  END ;;
DELIMITER ;