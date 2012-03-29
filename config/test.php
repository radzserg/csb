<?php

$basePath = realpath(dirname(__FILE__) . '/../../..');

return array(
    'basePath' => $basePath,

     'import' => array(
        'application.modules.csb.models.*',
        'application.modules.csb.components.*',
    ),

    'components'=>array(
        'fixture'=>array(
            'class'=>'system.test.CDbFixtureManager',
            'basePath' => realpath(dirname(__FILE__) . '/../tests/fixtures'),
        ),
        'db'=>array(
            'connectionString' => 'mysql:host=127.0.0.1;dbname=excursion_search_test',
            'username' => 'root',
            'password' => 'root',
            'enableProfiling'=>true,
            'enableParamLogging'=>true,

        ),
    ),
    'params'=>array(
        'debugMails' => true,
        
        'csb' => array(
            // specify all time in seconds
            'shortIntervals' => array(
                array('time' => 5, 'count' => 5, 'blockTime' => 600),
                array('time' => 60, 'count' => 30, 'blockTime' => 600),
            ),
            'longIntervals' => array(
                array('time' => 600, 'count' => 100, 'blockTime' => 3600)
            ),
            'checkScriptSchedule' => 600, // seconds, should be equal cron schedule
            'notifyAdmin' => true,
            'adminEmail' => 'radzserg@gmail.com',
            'debugMode' => false,
            'httpBlAccessKey' => 'jznvcpdrjnsh',
        ),
    ),

);
