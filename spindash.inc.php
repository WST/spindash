<?php

/**
* SpinDash — A web development framework
* © 2007–2015 Ilya I. Averkov
*
* Contributors:
* Irfan Mahfudz Guntur <ayes@bsmsite.com>
* Evgeny Bulgakov <evgeny@webline-masters.ru>
*/

namespace SpinDash;

define('SPINDASH_ROOT', __DIR__ . DIRECTORY_SEPARATOR);

require SPINDASH_ROOT . 'vendor/autoload.php';

define('SPINDASH_CORE', SPINDASH_ROOT . 'core' . DIRECTORY_SEPARATOR);

// Other core definitions and initialization
define('SPINDASH_NOW', time());
define('SPINDASH_VERSION', '2.0.0-git');
mb_internal_encoding('UTF-8');

// Core API
require SPINDASH_CORE . 'api.inc.php';

echo "success\n";
