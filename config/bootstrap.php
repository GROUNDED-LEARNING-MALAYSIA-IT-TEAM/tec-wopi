<?php

declare(strict_types=1);

use Cake\Core\Configure;

Configure::load('EaglenavigatorSystem/Wopi.wopi');
collection((array)Configure::read('Wopi.config'))->each(function ($merge, $file) {
    if (is_int($file)) {
        $file = $merge;
        $merge = true;
    }
    Configure::load($file, 'default', $merge);
});


//define wopi file path
define('WOPI_FILE_PATH', TMP . DS . 'wopi' . DS . 'files' . DS);
