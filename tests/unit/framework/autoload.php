<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

$magentoDir = require __DIR__ . '/../../../etc/magento_path.php';
require_once $magentoDir . '/app/autoload.php';

$vendorDir = require $magentoDir . '/app/etc/vendor_path.php';
$vendorAutoload = require $magentoDir . "/{$vendorDir}/autoload.php";
$testsBaseDir = dirname(__DIR__);
$toolBaseDir = dirname($testsBaseDir, 2);
$vendorAutoload->add('Migration\\Test\\', "{$testsBaseDir}/testsuite/Migration");
$vendorAutoload->addPsr4('Migration\\TestFramework\\', "{$testsBaseDir}/framework");
$vendorAutoload->addPsr4('Migration\\', "{$toolBaseDir}/src/Migration", true);
