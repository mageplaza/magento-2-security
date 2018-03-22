<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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
 * @copyright   Copyright (c) 2018 Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Security\Plugin;

use Magento\Framework\App\Area;
use Magento\Backend\Model\UrlInterface;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\User\Model\ResourceModel\User;
use Magento\Framework\Exception\MailException;

/**
 * Class LockUser
 * @package Mageplaza\Security\Plugin
 */
class LockUser
{
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Mageplaza\Security\Helper\Data
     */
    protected $_helper;

    /**
     * LockUser constructor.
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct
    (
        UrlInterface $backendUrl,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        \Mageplaza\Security\Helper\Data $helper
    )
    {
        $this->_backendUrl = $backendUrl;
        $this->_logger = $logger;
        $this->_storeManager = $storeManager;
        $this->_transportBuilder = $transportBuilder;
        $this->_helper = $helper;
    }

    /**
     * @param User $userModel
     * @param $user
     * @param $setLockExpires
     * @param $setFirstFailure
     */
    public function beforeUpdateFailure(User $userModel, $user, $setLockExpires, $setFirstFailure)
    {
        if ($setLockExpires) {
            //send mail if user is locked
            $storeUrl = parse_url($this->_backendUrl->getBaseUrl(), PHP_URL_HOST);
            $sendTo = explode(',', $this->_helper->getConfigGeneral('email'));
            $sendTo = array_map('trim', $sendTo);
            try {
                $store = $this->_storeManager->getStore();
                $templateVars = [
                    'logo_url' => 'https://www.mageplaza.com/media/mageplaza-security-email.png',
                    'logo_alt' => 'Mageplaza',
                    'store_url' => $storeUrl,
                    'user_name' => $user->getUserName()
                ];

                $this->_transportBuilder
                    ->setTemplateIdentifier('mp_lock_user_email_template')
                    ->setTemplateOptions([
                        'area' => Area::AREA_FRONTEND,
                        'store' => $store->getId()
                    ])
                    ->setTemplateVars($templateVars)
                    ->setFrom('general')
                    ->addTo($sendTo);
                $transport = $this->_transportBuilder->getTransport();
                $transport->sendMessage();
            } catch (MailException $e) {
                $this->_logger->critical($e->getLogMessage());
            }
        }
    }
}

