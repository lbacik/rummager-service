<?php
// Copyright (C) 2017 Lukasz Bacik <mail@luka.sh>
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

use Rumsrv\SenderService;

/**
 * Class resultStruct
 * @pw_element integer $sendId
 * @pw_element string $addr
 * @pw_complex resultStruct
 */
class resultStruct {
    public $sendId;
    public $addr;
}


/**
 * @service SenderSOAP
 */
class SenderSOAP
{
    private $senderSrv;

    public function __construct()
    {
        global $pdo_sn, $db_user, $db_pwd;
        $this->senderSrv = new SenderService($pdo_sn, $db_user, $db_pwd);
    }

    /**
     * @param integer $checkId
     * @return resultStruct
     */
    public function getAddressToCheck($checkId)
    {
        $sendId = $this->senderSrv->startNewSendProcess($checkId);
        $sendId !== 0 ? $addr = $this->senderSrv->getAddress($sendId) : $addr = '';

        $result = new resultStruct();
        $result->sendId = $sendId;
        $result->addr = $addr;

        return $result;
    }

    /**
     * @param integer $sendId
     * @param string $msg
     * @param string $conn_log
     */
    public function updateSendInfo($sendId, $msg, $connLog)
    {
        $this->senderSrv->updateSendInfo($sendId, $msg, $connLog);
    }
}
