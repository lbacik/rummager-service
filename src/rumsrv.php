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
 * Structure used to pass data to Rummager::addHostInfo method
 *
 * @pw_element integer $checkid
 * @pw_element string $ip
 * @pw_element integer $port
 * @pw_element integer $gcode
 * @pw_element string $gtext
 * @pw_element integer $hcode
 * @pw_element string $htext
 * @pw_element integer $ecode
 * @pw_element string $etext
 * @pw_element string $time_start
 * @pw_element string $time_con
 * @pw_element string $time_end
 * @pw_complex struct1
 */
class struct1 {
	public $checkid = 0;
	public $ip = '';
	public $port = 0;
	public $gcode = 0;
	public $gtext = '';
	public $hcode = 0;
	public $htext = '';
	public $ecode = 0;
	public $etext = '';
	public $time_start = null;
	public $time_con = null;
	public $time_end = null;
}

/**
 * Service used by Rummager Workers/Worker_Manager to read/write data from/to database
 *
 * @service Rummager
 */
class Rummager extends \Rumsrv\DBCommunication
{
    public function __construct()
    {
        global $pdo_sn, $db_user, $db_pwd;

        /**
         * @todo It is not the best solution :( I noticed that in the case of problems,
         * when an exeption is thrown, the parent::constructor parameters can be
         * visible on error stack trace - so this should be "exchange" to something
         * more secure in the future
         */
        parent::__construct($pdo_sn, $db_user, $db_pwd);
    }

    /**
     * HostID value identifies the Worker_Manager process.
     * The hostname have to be unique - if it already exist in the "host" table
     * the ID assigned to it will be returned. If there is no such hostname
     * new ID is created (and returned).
     *
     * @param string $hostname
     * @return integer hostid
     */
    public function getHostID($hostname)
    {
        $sql = "SELECT id FROM host WHERE name = ?";

        $n = $this->query($sql, array($hostname));

        if(count($n) == 1) {
            // exists
            $result = $n[0]['id'];
        } elseif (count($n) > 1) {
            throw new Exception('The hostname "'.$hostname.'" exists more that once.');
        } else {
            // create
            $sql = "INSERT INTO host(name, create_time) VALUES (?, NOW())";
            $result = $this->query($sql, array($hostname), 'ID');
        }
        return (int)$result;
    }

    /**
     * In fact this function is returning the number of threads run by particular
     * host (identified by hostid parameter).
     *
     * @todo change to "getThreadsRunning"
     * @todo how to determines the Worker_Manager process if there are more then
     * one running on the same host?
     *
     * @param integer $hostid
     * @return integer
     */
    public function getNodesRunning($hostid)
    {
        $sql = "SELECT count(*) AS q
                FROM node
                WHERE hid = ? AND status = 'running'";
        $result = 0;
        $n = $this->query($sql, array($hostid));
        if(isset($n[0]['q'])) {
                $result = $n[0]['q'];
        }
        return $result;
    }

    /**
     * "getter" for "n" column value in host table
     *
     * @for_future_use (used in the past before implementing threads
     * - not used for the time being). This value can be used by watchdog process
     * to determine how many Worker_Manager processes should be run.
     *
     * @todo this value is the only one that should be used by "watchdog" process
     * - it should determines how many Worker_Manager processes should be started
     *
     * @param integer $hostid
     * @return integer
     */
    public function getHostMaxNodes($hostid)
    {
        $sql = "SELECT n FROM host WHERE id = ?";
        $result = 0;
        $n = $this->query($sql, array($hostid));
        if(isset($n[0]['n'])) {
                $result = $n[0]['n'];
        }
        return $result;
    }

    /**
     * "getter" for "t" column value in host table - it determines how many
     * worker threads Worker_Manager process should start.
     *
     * @param integer $hostid
     * @return integer
     */
    public function getHostMaxThreads($hostid)
    {
        $sql = "SELECT t FROM host WHERE id = ?";

        $result = 0;
        $n = $this->query($sql, array($hostid));
        if(isset($n[0]['t'])) {
                $result = $n[0]['t'];
        }
        return $result;
    }

    /**
     * Returns module's id for given module name
     *
     * @param string $module
     * @return integer
     */
    public function getModuleId($module) {
        $sql = "SELECT id FROM module WHERE name = ?";

        $result = $this->query($sql, array($module));
        return intval($result[0]['id']);
    }

    /**
     * Each Worker has assigned unique node id.
     *
     * @param integer $hostid
     * @return integer
     */
    public function getNewNodeId($hostid)
    {
        $sql = "INSERT INTO node (stime, hid) VALUES ( NOW(), ?)";
        $result = $this->query($sql, array($hostid), 'ID');
        // result can be null - cast to int
        return (int)$result;
    }

    /**
     * Check id identifies the node (Worker), module and network (which is checked)
     * and it is used in module's result table to store the "check" results.
     *
     * Each Worker has to get (its) "check id" before starts its work.
     *
     * This method is chosing network (network id) that worker will work with,
     * howether information about "choosen" network has to be got by worker
     * via separate method - getNetworkId
     *
     * @param integer $nodeid
     * @param integer $moduleid
     * @return integer check id
     */
    public function startNewCheck($nodeid, $moduleid) {
        $sql = "SELECT id, ip
                FROM ipv4class
                WHERE status = 'TODO'
                    AND id not IN
                        (SELECT c.net
                            FROM `check` c
                                JOIN node n ON c.node = n.id
                                JOIN ipv4class i ON c.net = i.id
                            WHERE i.status = 'TODO'
                                AND n.status = 'running')
                LIMIT 1";

        $result = 0;

        $tmp = $this->query($sql);

        $netid = $tmp[0]['id'];

        //syslog(LOG_NOTICE, "netid: " . $netid);

        $sql = "INSERT INTO `check` (node, net, module, create_time)
                VALUES ( ? , ? , ? , NOW())";

        $result = $this->query($sql, array($nodeid, $netid, $moduleid), 'ID');
        //syslog(LOG_NOTICE, "checkid: " . $result);

        return $result;
    }


    /**
     * "getter" for "net" column in "check" table
     *
     * @param integer $checkid
     * @return integer
     */
    public function getNetworkId($checkid) {

        $sql = "SELECT net FROM `check` WHERE id = ?";
        $result = $this->query($sql, array($checkid));
        return $result[0]['net'];
    }

    /**
     * "getter" for ip/mask values from ipv4class table
     *
     * @param integer $id
     * @return string
     */
    public function getNetwork($id) {

        $sql = "SELECT ip, mask FROM ipv4class WHERE id = ?";
        $n = $this->query($sql, array($id));
        return $n[0]['ip'] . "/" . $n[0]['mask'];
    }

    /**
     * Not the best but the only one for the time being method to determine
     * what left to do (to check) in given address scope (network)
     *
     * @param string $ip
     * @param string $brd
     * @return string
     */
    public function getLastIP($ip, $brd)
    {
        $sql = "SELECT inet_ntoa(max(ip)) AS ip
                FROM smtp
                WHERE ip >= inet_aton(:ip)
                    AND ip <= inet_aton(:brd)";

        $n = $this->query($sql, array(':ip' => $ip, ':brd' => $brd));

        return $n[0]['ip'];
    }

    /**
     * After check is finished - the Worker has to change network status
     *
     * @todo method should return status of operation
     * (Maybe there should be added query to check if status has been changed?)
     *
     * @param string $network
     * @param string $mask as a 4 bytes value
     * @param string $newstatus
     */
    public function updateNetworkStatus($network, $mask, $newstatus)
    {
        $sql = "UPDATE ipv4class
                SET status = :status
                WHERE ip = :ip AND mask = :mask";

        $this->query($sql,
                array(':status' => $newstatus, ':ip' => $network, ':mask' => $mask),
                null);
    }

    /**
     * This method is little strange - it is returning to the Worker his
     * "status" from db - the goal is to have a possibility to control Workers
     * from service level but it should be thought over yet... :)
     *
     * @param integer $nodeid
     * @return boolean
     */
    public function checkNodeIsRunning($nodeid)
    {
        $sql = "SELECT status FROM node WHERE id = ?";
        $result = false;

        $n = $this->query($sql, array($nodeid));
        if(isset($n[0]['status']) && $n[0]['status'] == 'running') {
            $result = true;
        }
        return $result;
    }

    /**
     * Save data
     *
     * @param struct1 $data
     * @ return integer status - 0 = OK, -1 = PDO error
     * @return string
     */
    public function addHostInfo($data)
    {

        $sql = "INSERT INTO smtp (  checkid,
                                    ip,
                                    port,
                                    `helo-code`,
                                    helo,
                                    `ehlo-code`,
                                    ehlo,
                                    `greetings-code`,
                                    `greetings-text`,
                                    `tstart`,
                                    `tcon`,
                                    `tend`,
                                    `checkTime`)
                        VALUES (    :checkid,
                                    inet_aton(:ip),
                                    :port,
                                    :heloCode,
                                    :helo,
                                    :ehloCode,
                                    :ehlo,
                                    :greetingsCode,
                                    :greetingsText,
                                    :time_start,
                                    :time_con,
                                    :time_end,
                                    NOW())";

        $result = 0;
        $data2sql = array(
                ':checkid'          => $data->checkid,
                ':ip'               => $data->ip,
                ':port'             => $data->port,
                ':heloCode'         => $data->hcode,
                ':helo'             => $data->htext,
                ':ehloCode'         => $data->ecode,
                ':ehlo'             => $data->etext,
                ':greetingsCode'    => $data->gcode,
                ':greetingsText'    => $data->gtext,
                ':time_start'       => null,
                ':time_con'         => null,
                ':time_end'         => null
            );

        if(!empty($data->time_start))
                $data2sql[':time_start'] = $data->time_start;
        if(!empty($data->time_con))
                $data2sql[':time_con'] = $data->time_con;
        if(!empty($data->time_end))
                $data2sql[':time_end'] = $data->time_end;

        $result = $this->query($sql, $data2sql, 'ID');

        return $result;
    }

}
