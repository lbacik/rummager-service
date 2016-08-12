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

require_once 'rumsrvTest.php';

/**
 * This class implements test of SOAP service.
 *
 * All tests are the same as in RummagerTest - only used interface has been changed.
 *
 * Please remember to add config.local.test.php file and override $pdo_sn value
 * to indicate test database instead of production.
 *
 */
class RummagerSOAPTest extends RummagerTest
{
    public static function setUpBeforeClass()
    {
        // test config
        if (!file_exists('../config.local.test.php')) {
            echo 'ERROR! No test config file found...' . PHP_EOL;
            exit(0);
        }

        parent::setUpBeforeClass();
    }

    protected function initianizateRummager()
    {
        return new SoapClient('http://rumsrv.local/soap.php?WSDL');
    }
}
