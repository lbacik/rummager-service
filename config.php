<?php

/**
 * please copy the config.local.php.dist as config.local.php
 */
include 'config.local.php';

/**
 * The config.local.test.php file is helpful in testing SOAP extension (or any
 * other tests that use service endpoints instead of direct php classes calls).
 *
 * For the time being this file is required by rumsrvSOAPTest class to avoid of
 * using production database duting the tests.
 *
 * It should override specific parameters for test purpose.
 *
 */
@include 'config.local.test.php';
