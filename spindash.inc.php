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
define('ATS_ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('ATS_CORE', ATS_ROOT . 'core' . DIRECTORY_SEPARATOR);
define('ATS_INTERFACES', ATS_ROOT . 'interfaces' . DIRECTORY_SEPARATOR);
define('ATS_ABSTRACT', ATS_ROOT . 'abstract' . DIRECTORY_SEPARATOR);
define('ATS_APIS', ATS_ROOT . 'apis' . DIRECTORY_SEPARATOR);
define('ATS_CACHE', ATS_ROOT . 'cache' . DIRECTORY_SEPARATOR);
define('ATS_DB', ATS_ROOT . 'db' . DIRECTORY_SEPARATOR);
define('ATS_HTTP', ATS_ROOT . 'http' . DIRECTORY_SEPARATOR);
define('ATS_MISC', ATS_ROOT . 'misc' . DIRECTORY_SEPARATOR);
define('ATS_TEXTPROC', ATS_ROOT . 'textproc' . DIRECTORY_SEPARATOR);
define('ATS_XML', ATS_ROOT . 'xml' . DIRECTORY_SEPARATOR);

// Other core definitions and initialization
define('ATS_NOW', time());
mb_internal_encoding('UTF-8');

// Interfaces
require ATS_INTERFACES . 'imodule.inc.php';
require ATS_INTERFACES . 'icache-engine.inc.php';
require ATS_INTERFACES . 'iapplication.inc.php';

// Abstract core classes
require ATS_ABSTRACT . 'module.inc.php';
require ATS_ABSTRACT . 'core-module.inc.php';
require ATS_ABSTRACT . 'application.inc.php';
require ATS_ABSTRACT . 'cache-engine.inc.php';

// Core modules
require ATS_CORE . 'exceptions.inc.php';
require ATS_CORE . 'directory.inc.php';
require ATS_CORE . 'text-file.inc.php';
require ATS_CORE . 'ip.inc.php';

// HTTP subsystem
require ATS_HTTP . 'session.inc.php';
require ATS_HTTP . 'request.inc.php';
require ATS_HTTP . 'response.inc.php';

// Core API
require ATS_CORE . 'api.inc.php';

// Left for compatibility reasons
class ATS extends API {}
