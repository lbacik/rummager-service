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

namespace Rumsrv;

class SenderService extends DBCommunication
{
    const STATUS_INPROGRESS = 'INPROGRESS';
    const STATUS_DONE = 'DONE';

    public function __construct(type $pdo_sn, type $db_user, type $db_pwd)
    {
        parent::__construct($pdo_sn, $db_user, $db_pwd);
    }

    public function startNewSendProcess($checkId)
    {
        $this->getDB()->beginTransaction();

        $smtpData = $this->query(
            "SELECT s.id
                  FROM `smtp` s
	                LEFT JOIN `smtp_sender` ss ON s.id = ss.smtpid
                  WHERE s.tcon IS NOT NULL AND ss.id IS NULL
                  LIMIT 1"
        );

        if (empty($smtpData)) {
            $this->getDB()->rollBack();
            return 0;
        }

        $sendId = $this->query("
            INSERT INTO `smtp_sender` (`checkid`, `smtpid`, `status`)
            VALUES(?, ?, ?)",
            array($checkId, $smtpData[0]['id'], self::STATUS_INPROGRESS),
            "ID"
        );

        $this->getDB()->commit();

        return $sendId;
    }

    public function getAddress($sendId)
    {
        $result = $this->query("
            SELECT inet_ntoa(s.ip) as ip, s.port 
            FROM `smtp_sender` ss 
                INNER JOIN `smtp` s ON ss.`smtpid` = s.`id`
            WHERE ss.`id` = ? ",
            array($sendId)
        );

        return $result[0]['ip'] . ":" . $result[0]['port'];
    }

    public function updateSendInfo($sendId, $msg, $connLog)
    {
        $this->query("
            UPDATE `smtp_sender` 
            SET `msg` = ?, `conn_log` = ?, `status` = ?
            WHERE `id` = ? ",
            array($msg, $connLog, self::STATUS_DONE, $sendId)
        );
    }
}
