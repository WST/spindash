<?php

/**
* SpinDash — A web development framework
* © 2007–2013 Ilya I. Averkov <admin@jsmart.web.id>
*
* Contributors:
* Irfan Mahfudz Guntur <ayes@bsmsite.com>
* Evgeny Bulgakov <evgeny@webline-masters.ru>
*/

namespace SpinDash;

// Core paths
define('SPINDASH_ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('SPINDASH_CORE', SPINDASH_ROOT . 'core' . DIRECTORY_SEPARATOR);
define('SPINDASH_INTERFACES', SPINDASH_ROOT . 'interfaces' . DIRECTORY_SEPARATOR);
define('SPINDASH_ABSTRACT', SPINDASH_ROOT . 'abstract' . DIRECTORY_SEPARATOR);
define('SPINDASH_APIS', SPINDASH_ROOT . 'apis' . DIRECTORY_SEPARATOR);
define('SPINDASH_CACHE', SPINDASH_ROOT . 'cache' . DIRECTORY_SEPARATOR);
define('SPINDASH_DB', SPINDASH_ROOT . 'db' . DIRECTORY_SEPARATOR);
define('SPINDASH_HTTP', SPINDASH_ROOT . 'http' . DIRECTORY_SEPARATOR);
define('SPINDASH_MISC', SPINDASH_ROOT . 'misc' . DIRECTORY_SEPARATOR);
define('SPINDASH_TEXTPROC', SPINDASH_ROOT . 'textproc' . DIRECTORY_SEPARATOR);
define('SPINDASH_XML', SPINDASH_ROOT . 'xml' . DIRECTORY_SEPARATOR);
define('SPINDASH_FILEIO', SPINDASH_ROOT . 'fileio' . DIRECTORY_SEPARATOR);

// Other core definitions and initialization
define('SPINDASH_NOW', time());
mb_internal_encoding('UTF-8');

// Interfaces
require SPINDASH_INTERFACES . 'imodule.inc.php';
require SPINDASH_INTERFACES . 'icache-engine.inc.php';
require SPINDASH_INTERFACES . 'iapplication.inc.php';

// Abstract core classes
require SPINDASH_ABSTRACT . 'module.inc.php';
require SPINDASH_ABSTRACT . 'core-module.inc.php';
require SPINDASH_ABSTRACT . 'database.inc.php';
require SPINDASH_ABSTRACT . 'application.inc.php';
require SPINDASH_ABSTRACT . 'cache-engine.inc.php';

// Core modules
require SPINDASH_CORE . 'exceptions.inc.php';
require SPINDASH_CORE . 'directory.inc.php';
require SPINDASH_CORE . 'text-file.inc.php';
require SPINDASH_CORE . 'ip.inc.php';

// HTTP subsystem
require SPINDASH_HTTP . 'session.inc.php';
require SPINDASH_HTTP . 'request.inc.php';
require SPINDASH_HTTP . 'response.inc.php';

// Core API
require SPINDASH_CORE . 'api.inc.php';

// Left for compatibility reasons
class ATS extends API {}
