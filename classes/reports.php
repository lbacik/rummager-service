<?php
// Copyright (C) 2015 Lukasz Bacik <mail@luka.sh>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

/**
 * All reports methods will return its results as an array of array of string
 * (no matter if there is only one row in a result or more, it alway be an array
 * of array of string - see the DBComunication::query method)
 *
 * @pw_complex stringArray An array of string
 */

/**
 * The service defines some reports.
 *
 * @service Reports
 */
class Reports extends DBCommunication {

    public function __construct() {
        global $pdo_sn, $db_user, $db_pwd;
        parent::__construct($pdo_sn, $db_user, $db_pwd);
    }

    /**
     * Report GetProgress
     *
     * One row (three columns) as result:
     *  1. (A) how many B class networks is defined to scan
     *  2. (B) how many of those networks has been already scanned and has
     *      the FINISHED stauts.
     *  3. percentage ratio: 100 * (B) / (A)
     *
     * @return stringArray
     */
    public function getProgress() {

        $sql = "
            SELECT A AS 'all', F AS 'finished', (100 * F / A) AS 'percent'
                FROM (
                    SELECT  (SELECT count(*) FROM ipv4class) AS A,
                            (SELECT count(*) FROM ipv4class
                                WHERE status = 'FINISHED') AS F
                ) AS S1
        ";

        $result = $this->query($sql);

        return $result;
    }

    /**
     * Report GetScanStats
     *
     * Many rows as a result, one row for each month-year pair that contains:
     *  1. date (year-month)
     *  2. hosts number - how many different hosts has been registered (worked)
     *      at that month
     *  3. addresses - how many addresses has been scaned by all hosts
     *      at this particular month
     *  4. classb - the scanned addresses converted to quantity of the classes B (approximately)
     *  5. percentage ratio of that month scaned addresses to the all needed to be scanned
     *
     * @return stringArray
     */
    public function getScanStats() {

        $sql = "
            SELECT date_format(`date`, '%Y-%m') AS `date`
                ,count(distinct `hostid`) AS 'hosts'
                ,sum(`hosts`) AS 'addresses'
                ,sum((`hosts`) / 65536) AS 'classb'
                ,(100 * sum((`hosts`) / 65536) / (SELECT count(*) FROM ipv4class)) AS 'percent'
            FROM dw_f_ip
            GROUP BY date_format(`date`, '%Y-%m')
        ";

        $result = $this->query($sql);

        return $result;
    }

    /**
     * Report GetCurrentHostStatus
     *
     * Many rows as a result, one for each currently working host:
     *  1. host - the host id
     *  2. threads - the number of currently working threads that has been
     *      registered using this host id
     *
     * @return stringArray
     */
    public function getCurrentHostStatus() {

        $sql = "
            SELECT `host`, count(*) AS threads
            FROM node_last_check
            WHERE `minutes ago` = 0
            GROUP BY `host`
        ";

        $result = $this->query($sql);
        return $result;
    }
}
