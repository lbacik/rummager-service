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

namespace Rumsrv;

use Rumsrv\Logging;

/**
 * Simple class to handle all DB related communication - it allows to save some
 * code lines in service files :)
 */
class DBCommunication {

    /**
     * There are no setters and getters (not needed for the time being)
     */
    private $db = null;
    private $logging = null;

    /**
     * Connect to the given db ($pdo_sn) using the given credentials (db_*)
     * and save a session in $db field.
     *
     * Instantiate the Logging class.
     *
     * @fixme What if there are problems during db connection?..
     * @fixme In such situation the constructor's parameters can be visible in log/exception output!!!
     *
     * @param type $pdo_sn
     * @param type $db_user
     * @param type $db_pwd
     */
    public function __construct($pdo_sn, $db_user, $db_pwd)
    {
        $this->db = new \PDO($pdo_sn, $db_user, $db_pwd);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->logging = new Logging();
    }

    /**
     * Execute given query using PDO library.
     *
     * PDO
     * http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers
     *
     * @param string $sql query or DML instruction
     *
     * @param array $params array of parameters to bind with query - please read
     * http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers#Running_Statements_With_Parameters
     * - !NOTE! this method supports only "array binding"
     *
     * @param string $resultType It can be: ASSOC, ID or whatether but please
     * use "null" instead of whatever :) This parameter determines what
     * will be returned - ASSOC is a default value and it is only one that should
     * be used for SELECT statment. ID is a good choise for INSERT instructions
     * (the method will return then the ID of the inserted row). And "null" can
     * be used if we are not intrested in the results (null will be returned).
     * @todo To change the $resultType param to use constans defined in the calss instead of strings.
     *
     * @return mix array|int|null|false - false will be returned in case of any
     * error that caused that the exception has been raised - the exeptin msg, sql
     * and its params are then logged using log method of the local instantiate
     * of Logging class.
     */
    public function query($sql, array $params = array(), $resultType = 'ASSOC') {

        $result = null;

        try {

            $r = $this->db->prepare($sql);
            $r->execute($params);

            switch($resultType) {
                case 'ID':
                    if($r->rowCount() > 0) {
                        $result = $this->db->lastInsertId();
                    }
                    break;
                case 'ASSOC':
                    $result = $r->fetchAll(\PDO::FETCH_ASSOC);
                    break;
            }

        } catch (\PDOException $ex) {
            $this->log($ex->getCode() . " " . $ex->getMessage()
                    . " SQL: " . $sql . " PARAMS: " . implode(", ",$params));
            $result = false;
        }

        return $result;
    }

    /**
     * Log the message!
     *
     * @param type $msg
     */
    protected function log($msg) {
        $this->logging->log($msg);
    }

    protected function getDB()
    {
        return $this->db;
    }
}
