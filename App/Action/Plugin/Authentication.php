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
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Security\App\Action\Plugin;

/**
 * Class Authentication
 * @package Mageplaza\Security\App\Action\Plugin
 */
class Authentication extends \Magento\Backend\App\Action\Plugin\Authentication
{
    /**
     * @var string[]
     */
    protected $_openActions = [
        'authindex',
        'authpost',
        'forgotpassword',
        'resetpassword',
        'resetpasswordpost',
        'logout',
        'refresh', // captcha refresh
    ];
}
