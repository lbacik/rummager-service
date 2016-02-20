<?php

require_once '../config.php';
require_once '../classes/dbcommunication.php';
require_once '../classes/logging.php';
require_once '../classes/rumsrv.php';

/**
 * Description of rumsrvTest
 *
 * @author Lukasz Bacik <mail@luka.sh>
 */
class RummagerTest extends PHPUnit_Framework_TestCase
{
    protected static $rum;

    public static function setUpBeforeClass()
    {
        self::$rum = new Rummager();
    }

    public static function tearDownAfterClass()
    {
        self::$rum = NULL;
    }

    /**
     * exeption ?
     *
     */
    public function test_getHostID()
    {
        $hostname = 'phpunit.test';

        $hostId = self::$rum->getHostID($hostname);

        $this->assertGreaterThan(0, $hostId);
        $this->assertEquals($hostId, self::$rum->getHostID($hostname));

        return $hostId;
    }

    /**
     *
     * @depends test_getHostID
     */
    public function test_getHostMaxNodes($hostId)
    {
        $n = self::$rum->getHostMaxNodes($hostId);
        $this->assertEquals($n, 1);
    }

    /**
     * @depends test_getHostID
     */
    public function test_getHostMaxThreads($hostId)
    {
        $t = self::$rum->getHostMaxThreads($hostId);
        $this->assertEquals($t, 1);
    }

    /**
     * @depends test_getHostID
     */
    public function test_getNodesRunning($hostId)
    {
        $n = self::$rum->getNodesRunning($hostId);
        // $this->assertEquals($n, 0);
    }

    /**
     *
     * @depends test_getHostID
     */
    public function test_getNodeId($hostId)
    {
        $nodeId = self::$rum->getNewNodeId($hostId);

        $this->assertGreaterThan(0, $nodeId);

        $n2 = self::$rum->getNewNodeId($hostId);
        $this->assertGreaterThan(0, $n2);
        $this->assertGreaterThan($nodeId, $n2);

        return $nodeId;
    }

    /**
     * (?)
     *
     * @depends test_getHostID
     */
    /*
    public function test_getNodesRunning2($hostId)
    {
        $r = new Rummager();
        $n = $r->getNodesRunning($hostId);
        $this->assertEquals($n, 2);
    }
    */

    /**
     * @depends test_getNodeId
     */
    public function test_getModuleId($nodeId)
    {
        $moduleName = 'smtp';
        $moduleId = self::$rum->getModuleId($moduleName);

        $this->assertGreaterThan(0, $moduleId);
        $this->assertEquals($moduleId, self::$rum->getModuleId($moduleName));

        return $moduleId;
    }

    /**
     * @depends test_getNodeId
     * @depends test_getModuleId
     */
    public function test_startNewCheck($nodeId, $moduleId)
    {
        $checkId = self::$rum->startNewCheck($nodeId, $moduleId);
        $this->assertGreaterThan(0, $checkId);

        return $checkId;
    }

    /**
     * @depends test_startNewCheck
     */
    public function test_getNetworkId($checkId)
    {
        $netId = self::$rum->getNetworkId($checkId);
        $this->assertGreaterThan(0, $netId);
        return $netId;
    }

    /**
     * @depends test_getNetworkId
     */
    public function test_getNetwork($netId)
    {
        $network = self::$rum->getNetwork($netId);

        $this->assertStringEndsWith('0.0/16', $network);
    }

    /**
     * addHostInfo($data)
     */
    public function _test_addHostInfo()
    {

    }

    /**
     * updateNetworkStatus($network, $mask, $newstatus)
     */
    public function _test_updateNetworkStatus()
    {

    }

}
