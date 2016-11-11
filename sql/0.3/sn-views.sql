CREATE VIEW `dw_f_ip_d` AS
  SELECT
    DATE_FORMAT(`dw_f_ip`.`date`, '%Y-%m-%d') AS `date`,
    FORMAT(SUM(`dw_f_ip`.`hosts`), 0) AS `hosts`,
    FORMAT(SUM(`dw_f_ip`.`smtp`), 0) AS `smtp25`
  FROM
    `dw_f_ip`
  GROUP BY DATE_FORMAT(`dw_f_ip`.`date`, '%Y-%m-%d');

CREATE VIEW `dw_f_ip_m` AS
  SELECT
    DATE_FORMAT(`dw_f_ip`.`date`, '%Y-%m') AS `date`,
    FORMAT(SUM(`dw_f_ip`.`hosts`), 0) AS `hosts`
  FROM
    `dw_f_ip`
  GROUP BY DATE_FORMAT(`dw_f_ip`.`date`, '%Y-%m');

CREATE VIEW `nlc_active_dead` AS
  SELECT
    `node_last_check`.`host` AS `hostid`,
    COUNT(IF((`node_last_check`.`minutes ago` = 0),
             1,
             NULL)) AS `activ_nodes`,
    COUNT(IF((`node_last_check`.`minutes ago` > 0),
             1,
             NULL)) AS `dead_nodes`
  FROM
    `node_last_check`
  GROUP BY `node_last_check`.`host`;