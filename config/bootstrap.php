<?php
declare(strict_types=1);

use Cake\Core\Configure;


Configure::load('EaglenavigatorSystem/Wopi.wopi');

dump('-- config --');
dump(Configure::read('Wopi.config'));
collection((array)Configure::read('Wopi.config'))->each(function ($merge, $file) {
	if (is_int($file)) {
		$file = $merge;
		$merge = true;
	}
    Configure::load($file, 'default', $merge);
});
