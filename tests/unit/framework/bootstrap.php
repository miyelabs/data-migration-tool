<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

require_once __DIR__ . '/autoload.php';

if (!defined('TESTS_TEMP_DIR')) {
    define('TESTS_TEMP_DIR', dirname(__DIR__) . '/tmp');
}
// Magento 2.4.9+ removed app/functions.php; __() is autoloaded via magento/framework registration.php
if (file_exists(BP . '/app/functions.php')) {
    require BP . '/app/functions.php';
}

if (is_dir(TESTS_TEMP_DIR)) {
    $filesystemAdapter = new \Magento\Framework\Filesystem\Driver\File();
    $filesystemAdapter->deleteDirectory(TESTS_TEMP_DIR);
}
mkdir(TESTS_TEMP_DIR);

// PHPUnit 10+ cannot create test doubles for undefined classes; generate factories and proxies on demand
$generatorIo = new \Magento\Framework\Code\Generator\Io(
    new \Magento\Framework\Filesystem\Driver\File(),
    TESTS_TEMP_DIR . '/generated/code'
);
$generatedCodeAutoloader = new \Magento\Framework\TestFramework\Unit\Autoloader\GeneratedClassesAutoloader(
    [
        new \Magento\Framework\TestFramework\Unit\Autoloader\ExtensionAttributesGenerator(),
        new \Magento\Framework\TestFramework\Unit\Autoloader\ExtensionAttributesInterfaceGenerator(),
        new \Magento\Framework\TestFramework\Unit\Autoloader\FactoryGenerator(),
        new \Magento\Framework\TestFramework\Unit\Autoloader\ProxyGenerator(),
    ],
    $generatorIo
);
spl_autoload_register([$generatedCodeAutoloader, 'load']);

\Magento\Framework\Phrase::setRenderer(new \Magento\Framework\Phrase\Renderer\Placeholder());

set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');
