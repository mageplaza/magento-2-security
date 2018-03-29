
<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Security
 * @copyright   Copyright (c) 2018 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
class BrowserDetector_Autoload
{
    public static function autoload ($class) {
        // project-specific namespace prefix
        $prefix = 'Sinergi\\BrowserDetector\\';
        // base directory for the namespace prefix
        $baseDir = __DIR__ . '/src/';
        // does the class use the namespace prefix?
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {

            // no, move to the next registered autoloader
            return;
        }
        // get the relative class name
        $relativeClass = substr($class, $len);
        // replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php
        $file = rtrim($baseDir, '/') . '/' . str_replace('\\', '/', $relativeClass) . '.php';
        // if the file exists, require it
        if (file_exists($file)) {
            require $file;
        }
    }
}
spl_autoload_register(['BrowserDetector_Autoload', 'autoload']);