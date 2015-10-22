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
 * The only reason why this file is not in the 'public' directory it is a lack
 * of client authentication by main service (public/soap.php) - it causes that
 * public/soap.php service should be "hidden" from users (for those for which this
 * raporting service is available), and the simplest way to achieve that is just to
 * use different URLs for as main (soap.php) as raporting services.
 *
 * After implementation of the authentication mechanism at public/soap.php service
 * folders public & public_reports will be merged.
 */

require_once '../config.php';
require_once 'class.phpwsdl.php';
require_once '../classes/logging.php';
require_once '../classes/dbcommunication.php';

PhpWsdl::RunQuickMode ( '../classes/reports.php' );
