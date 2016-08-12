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

class RummagerTest extends PHPUnit_Extensions_Database_TestCase
{
    protected $rum = null;
    private $conn = null;

    public static function setUpBeforeClass()
    {
        global $dbname;

        $pdo = new PDO('mysql:host=localhost;', 'root', '');
        if($pdo->exec('USE ' . $dbname)) {
            $pdo->exec('DROP DATABASE ' . $dbname);
        }
        $pdo->exec('CREATE DATABASE ' . $dbname);

        chdir('../sql');
        exec('mysql -u root ' . $dbname.' < db-source.sql');
        chdir('../tests');
    }

    public function setUp()
    {
        $this->rum = $this->initianizateRummager();
    }

    public static function tearDownAfterClass()
    {
    }

    public function tearDown()
    {
    }

    public function getConnection()
    {
        global $dbname;
        if ($this->conn === null) {
            try {
                $pdo = new PDO('mysql:host=localhost;dbname='. $dbname, 'root', '');
                $this->conn = $this->createDefaultDBConnection($pdo, $dbname);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        return $this->conn;
    }

    public function getDataSet()
    {
        return new PHPUnit_Extensions_Database_DataSet_CompositeDataSet(array());
    }

    protected function initianizateRummager()
    {
         return new Rummager();
    }


    /**
     * @todo exeption
     */
    public function test_getHostID()
    {
        global $hostname;

        $conn = $this->getConnection()->getConnection();

        // whether the hostname already exists in db?
        $sql = "SELECT * FROM host WHERE name = '".$hostname."'";
        $query = $conn->query($sql);
        $results = $query->fetchAll(PDO::FETCH_COLUMN);
        $this->assertEquals(0, count($results));

        $hostId = $this->rum->getHostID($hostname);

        // NOW the hostname should exists in db already
        $query = $conn->query($sql);
        $results = $query->fetchAll(PDO::FETCH_COLUMN);
        $this->assertEquals(1, count($results));
        $dbHostId = (int)$results[0];

        $this->assertGreaterThan(0, $hostId);
        $this->assertEquals($hostId, $dbHostId);
        $this->assertEquals($hostId, $this->rum->getHostID($hostname));

        // db add the same hostname
        // test exception

        return $hostId;
    }

    /**
     * @depends test_getHostID
     */
    public function test_getHostMaxNodes($hostId)
    {
        $conn = $this->getConnection()->getConnection();

        $sql = "SELECT n FROM host WHERE id = $hostId";
        $query = $conn->query($sql);
        $results = $query->fetchAll(PDO::FETCH_COLUMN);

        $n = $this->rum->getHostMaxNodes($hostId);

        $this->assertEquals($n, (int)$results[0]);
    }

    /**
     * @depends test_getHostID
     */
    public function test_getHostMaxThreads($hostId)
    {
        $conn = $this->getConnection()->getConnection();

        $sql = "SELECT t FROM host WHERE id = $hostId";
        $query = $conn->query($sql);
        $results = $query->fetchAll(PDO::FETCH_COLUMN);

        $t = $this->rum->getHostMaxThreads($hostId);

        $this->assertEquals($t, (int)$results[0]);
    }

    /**
     * @depends test_getHostID
     */
    public function test_getNodesRunning($hostId)
    {
        $n = $this->rum->getNodesRunning($hostId);
        $this->assertEquals($n, 0);
    }

    /**
     * @depends test_getHostID
     */
    public function test_getNodeId($hostId)
    {
        $nodeId = $this->rum->getNewNodeId($hostId);

        $this->assertGreaterThan(0, $nodeId);

        $n2 = $this->rum->getNewNodeId($hostId);

        $this->assertGreaterThan(0, $n2);
        $this->assertGreaterThan($nodeId, $n2);

        return $nodeId;
    }

    /**
     *
     * @depends test_getHostID
     */
    public function test_getNodesRunning2($hostId)
    {
        $n = $this->rum->getNodesRunning($hostId);
        $this->assertEquals($n, 2);
    }

    /**
     * @depends test_getNodeId
     */
    public function test_getModuleId($nodeId)
    {
        global $moduleName;
        $conn = $this->getConnection()->getConnection();

        $sql = "SELECT id FROM module WHERE name = '".$moduleName."'";
        $query = $conn->query($sql);
        $results = $query->fetchAll(PDO::FETCH_COLUMN);

        $moduleId = $this->rum->getModuleId($moduleName);

        $this->assertGreaterThan(0, $moduleId);
        $this->assertEquals($moduleId, (int)$results[0]);
        $this->assertEquals($moduleId, $this->rum->getModuleId($moduleName));

        return $moduleId;
    }

    /**
     * @depends test_getNodeId
     * @depends test_getModuleId
     */
    public function test_startNewCheck($nodeId, $moduleId)
    {
        $checkId = (int)$this->rum->startNewCheck($nodeId, $moduleId);
        $this->assertGreaterThan(0, $checkId);

        return $checkId;
    }

    /**
     * @depends test_startNewCheck
     */
    public function test_checkNetId($checkId)
    {
        global $statusTODO;
        $conn = $this->getConnection()->getConnection();

        $sql = "SELECT net FROM `check` WHERE id = $checkId";
        $query = $conn->query($sql);
        $results = $query->fetchAll(PDO::FETCH_COLUMN);

        $netId = (int)$results[0];
        $this->assertGreaterThan(0, $netId);

        $sql = "SELECT status FROM ipv4class WHERE id = $netId";
        $query = $conn->query($sql);
        $results = $query->fetchAll(PDO::FETCH_COLUMN);
        $this->assertSame($statusTODO, $results[0]);

        return $netId;
    }

    /**
     * @depends test_checkNetId
     */
    public function test_NetString($netId)
    {
        $conn = $this->getConnection()->getConnection();
        $sql = "SELECT ip, mask FROM ipv4class WHERE id = $netId";
        $query = $conn->query($sql);
        $results = $query->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($results['ip']);
        $this->assertNotEmpty($results['mask']);

        return $results;
    }

    /**
     * @depends test_startNewCheck
     * @depends test_checkNetId
     */
    public function test_getNetworkId($checkId, $dbnetId)
    {
        $netId = $this->rum->getNetworkId($checkId);
        $this->assertGreaterThan(0, $netId);
        $this->assertEquals($netId, $dbnetId);

        return $netId;
    }

    /**
     * @depends test_getNetworkId
     * @depends test_NetString
     */
    public function test_getNetwork($netId, $netStr)
    {
        $network = $this->rum->getNetwork($netId);
        $this->assertSame($network, $netStr['ip'].'/'.$netStr['mask']);
    }

    /**
     * @depends test_startNewCheck
     * @depends test_NetString
     * @depends test_getModuleId
     */
    public function test_addHostInfo($checkId, $netStr, $moduleId)
    {
        $conn = $this->getConnection()->getConnection();
        $testCode = 100;
        $testText = 'Test Text';
        $testTime = '10:01:15';

        $data = new struct1();
        $data->checkid = $checkId;
        $data->ip = $netStr['ip'];
        $data->port = 25;
        $data->gcode = $testCode;
        $data->gtext = $testText;
        $data->hcode = $testCode;
        $data->htext = $testText;
        $data->ecode = $testCode;
        $data->etext = $testText;
        $data->time_start = $testTime;
        $data->time_con = $testTime;
        $data->time_end = $testTime;

        $id = $this->rum->addHostInfo($data);

        // module table
        $sql = "SELECT results_tab FROM module WHERE id = $moduleId";
        $query = $conn->query($sql);
        $results = $query->fetch(PDO::FETCH_ASSOC);
        $sql = "SELECT * FROM `".$results['results_tab']."` WHERE id = $id";
        $query = $conn->query($sql);
        $results = $query->fetch(PDO::FETCH_ASSOC);

        $sql = "SELECT inet_aton('".$data->ip."') as ip";
        $query = $conn->query($sql);
        $ip = $query->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals($data->checkid, $results['checkid']);
        $this->assertEquals($ip['ip'], $results['ip']);
        $this->assertEquals($data->port, $results['port']);
        $this->assertEquals($data->gcode, $results['greetings-code']);
        $this->assertEquals($data->gtext, $results['greetings-text']);
        $this->assertEquals($data->hcode, $results['helo-code']);
        $this->assertEquals($data->htext, $results['helo']);
        $this->assertEquals($data->ecode, $results['ehlo-code']);
        $this->assertEquals($data->etext, $results['ehlo']);
        $this->assertEquals($data->time_start, $results['tstart']);
        $this->assertEquals($data->time_con, $results['tcon']);
        $this->assertEquals($data->time_end, $results['tend']);
    }

    /**
     * @depends test_getNetworkId
     * @depends test_NetString
     */
    public function test_updateNetworkStatus($netId, $netStr)
    {
        global $statusFINISHED;
        $conn = $this->getConnection()->getConnection();

        $this->rum->updateNetworkStatus($netStr['ip'], $netStr['mask'], $statusFINISHED);

        $sql = "SELECT status FROM ipv4class WHERE id = $netId";
        $query = $conn->query($sql);
        $results = $query->fetchAll(PDO::FETCH_COLUMN);
        $this->assertSame($statusFINISHED, $results[0]);
    }
}
