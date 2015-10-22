<?php

/**
 * !!! phpwsdl bug !!!
 *
 * Please read when installing phpwsdl from its orginal repository
 * https://code.google.com/p/php-wsdl-creator/issues/detail?id=1
 *
 * The phpwsdl lib included in this project has been already patched!
 *
 * @todo to check the new version (?) of Phpwsdl: https://github.com/RockPhp/PhpWsdl
 *
 */

set_include_path(get_include_path() . PATH_SEPARATOR . '../lib/php-wsdl-2.3');

/**
 * please copy the config.local.php.dist as config.local.php 
 */
include 'config.local.php';
