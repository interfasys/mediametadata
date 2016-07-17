<?php
/**
 * ownCloud - mediametadata
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Jalpreet Singh Nanda (:imjalpreet) <jalpreetnanda@gmail.com>
 * @copyright Jalpreet Singh Nanda (:imjalpreet) 2016
 */

use Codeception\Util\Autoload;

define('PHPUNIT_RUN', 1);

// Add core
require_once __DIR__ . '/../../../lib/base.php';

// Add core tests to the list of valid paths
OC::$loader->addValidRoot(OC::$SERVERROOT . '/tests');

// Give access to core tests to Codeception
Autoload::addNamespace('Test', '/../../../tests/lib');

// Load all apps
OC_App::loadApps();

OC_Hook::clear();