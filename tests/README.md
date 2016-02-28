
#DB Unit

Dbunit is required and should be installed during box's provision process.

#Database

Please add required db access rights manually before tests start. 

    $ mysql -u root -e 'grant all on test.* to "sn"@"%"'

#Base tests

    $ phpunit --bootstrap bootstrap.php rumsrvTest.php

#Service tests

You will have to use `config.local.test.php` file to indicate the right test database (please add this file to the root project directory where other config*.php files are stored).

    <?php
    
        $pdo_sn = 'mysql:host=localhost;dbname=test;charset=utf8';

Now you can start SOAP tests:

    $ phpunit --bootstrap bootstrap.php rumsrvSOAPTest.php

