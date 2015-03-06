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

// SpinDash root directory
define('SPINDASH_ROOT', __DIR__ . DIRECTORY_SEPARATOR);

// Some basic setup
define('SPINDASH_NOW', time());
define('SPINDASH_VERSION', '2.0.0-git');
mb_internal_encoding('UTF-8');

// Third-party components
require SPINDASH_ROOT . 'vendor/autoload.php';

// System paths definition
define('SPINDASH_CORE', SPINDASH_ROOT . 'core' . DIRECTORY_SEPARATOR);

// Core API
require SPINDASH_CORE . 'api.inc.php';
require SPINDASH_CORE . 'webapp.inc.php';

echo "success\n";
