<?php

/** @var modX $modx */
$modx =& $object->xpdo;

$success = false;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $success = true;

        // check if php is at least 8.1
        if (PHP_VERSION_ID < 80100) {
            $modx->log(xPDO::LOG_LEVEL_ERROR, 'PHP version 8.1 or higher is required by MediaScanner.');
            $success = false;
        }

        if (!$success) {
            $modx->log(xPDO::LOG_LEVEL_ERROR, 'Requirements not met. MediaScanner can\'t be installed.');
        }

        break;
    case xPDOTransport::ACTION_UNINSTALL:
        $success = true;
        break;
}

return $success;
