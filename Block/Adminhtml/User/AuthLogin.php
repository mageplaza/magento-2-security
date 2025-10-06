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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Security\Block\Adminhtml\User;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Mageplaza\Security\Helper\TwoFactorAuthData as HelperData;

/**
 * Class AuthLogin
 * @package Mageplaza\Security\Block\Adminhtml\User
 */
class AuthLogin extends Template
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * AuthLogin constructor.
     *
     * @param Context $context
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        array $data = []
    ) {
        $this->_helperData = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function enableTrustedDevice()
    {
        return $this->_helperData->getTwoFactorAuthConfigGeneral('trust_device');
    }

    /**
     * @return mixed|string
     */
    public function getTrustedLifetime()
    {
        $lifetime = $this->enableTrustedDevice()
            ? ($this->_helperData->getTwoFactorAuthConfigGeneral('trust_time') ?: 30)
            : 0;

        return $lifetime;
    }
}
