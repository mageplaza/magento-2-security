Browser Detector
================

[![Build Status](https://travis-ci.org/sinergi/php-browser-detector.svg?branch=master)](https://travis-ci.org/sinergi/php-browser-detector)
[![StyleCI](https://styleci.io/repos/3752453/shield?style=flat)](https://styleci.io/repos/3752453)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sinergi/php-browser-detector/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sinergi/php-browser-detector/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/sinergi/php-browser-detector/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/sinergi/php-browser-detector/?branch=master)
[![Latest Stable Version](http://img.shields.io/packagist/v/sinergi/browser-detector.svg?style=flat)](https://packagist.org/packages/sinergi/browser-detector)
[![Total Downloads](https://img.shields.io/packagist/dt/sinergi/browser-detector.svg?style=flat)](https://packagist.org/packages/sinergi/browser-detector)
[![License](https://img.shields.io/packagist/l/sinergi/browser-detector.svg?style=flat)](https://packagist.org/packages/sinergi/browser-detector)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1865a02e-284c-428a-a2b4-091c997e5935/mini.png)](https://insight.sensiolabs.com/projects/1865a02e-284c-428a-a2b4-091c997e5935)
[![Join the chat at https://gitter.im/sinergi/php-browser-detector](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/sinergi/php-browser-detector?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Detecting the user's browser, operating system, device and language from PHP. Because browser detection is not always
reliable and evolves at all time, use with care and feel free to contribute.


## Credits

- Sinergi: https://github.com/sinergi/php-browser-detector


## Install 

Download [this lib](https://github.com/mageplaza/magento-2-security/archive/library.zip) and extract to `lib/internal/Mageplaza/Security`

After uploaded, the source code path should be `lib/internal/Mageplaza/Security/browser-detector/src`, files: http://prntscr.com/iwvb2i


## Update

**PHP Browser Detector**:  You can update latest version od PHP Browser Detector [here](https://github.com/sinergi/php-browser-detector/releases)
and paste into `lib/internal/Mageplaza/Security/browser-detector`

After uploaded, the source code path should be `lib/internal/Mageplaza/Security/browser-detector/src`, files: http://prntscr.com/iwvb2i

We hightly recommend that you should install Mageplaza Security via composer command line:

```
composer require mageplaza/module-security
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```
