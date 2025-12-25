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

namespace Mageplaza\Security\Observer\Google;

use Exception;
use Magento\Backend\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\Security\Helper\Data;
use Mageplaza\Security\Helper\TwoFactorAuthData;
use Mageplaza\Security\Model\Config\Source\LoginLog\Status;
use Mageplaza\Security\Model\LoginLogFactory;
use Mageplaza\Security\Model\TrustedFactory;

/**
 * Class UserLoginSuccess
 * @package Mageplaza\Security\Observer\Google
 */
class UserLoginSuccess implements ObserverInterface
{
    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var TrustedFactory
     */
    protected $_trustedFactory;

    /**
     * @var TwoFactorAuthData
     */
    protected $helper;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Session
     */
    protected $_backendSession;

    /**
     * @var LoginLogFactory
     */
    protected $_loginLogFactory;

    /**
     * UserLoginSuccess constructor.
     *
     * @param DateTime $dateTime
     * @param ManagerInterface $messageManager
     * @param TrustedFactory $trustedFactory
     * @param Request $request
     * @param TwoFactorAuthData $helper
     * @param Data $helperData
     * @param Session $_backendSession
     * @param LoginLogFactory $loginLogFactory
     */
    public function __construct(
        DateTime $dateTime,
        ManagerInterface $messageManager,
        TrustedFactory $trustedFactory,
        Request $request,
        TwoFactorAuthData $helper,
        Data $helperData,
        Session $_backendSession,
        LoginLogFactory $loginLogFactory
    ) {
        $this->_dateTime       = $dateTime;
        $this->_messageManager = $messageManager;
        $this->_trustedFactory = $trustedFactory;
        $this->helper          = $helper;
        $this->request         = $request;
        $this->helperData      = $helperData;
        $this->_backendSession = $_backendSession;
        $this->_loginLogFactory = $loginLogFactory;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->helperData->isEnabled()) {
            $loginLog = [
                'time'          => time(),
                'user_name'     => $observer->getUser()->getUserName(),
                'ip'            => $this->request->getClientIp(),
                'browser_agent' => $this->_backendSession->getBrowserAgent(),
                'url'           => $this->_backendSession->getUrl(),
                'referer'       => $this->_backendSession->getRefererUrl(),
                'status'        => Status::STATUS_SUCCESS
            ];
            $this->_loginLogFactory->create()->addData($loginLog)->save();
        }

        $user      = $observer->getEvent()->getUser();
        $isTrusted = $observer->getEvent()->getMpIsTrusted();
        if ($user && $isTrusted) {
            $trusted = $this->_trustedFactory->create();
            try {
                $ipLogin = explode(',', $this->request->getClientIp());
                if (count($ipLogin) > 1) {
                    if (($key = array_search('127.0.0.1', $ipLogin)) !== false) {
                        unset($ipLogin[$key]);
                    }
                }
                $ipLogin = implode(',', $ipLogin);

                $trusted->setDeviceIp($ipLogin)
                    ->setLastLogin($this->_dateTime->date())
                    ->setName($this->helper->getDeviceName())
                    ->setUserId($user->getId())
                    ->save();
            } catch (Exception $e) {
                $this->_messageManager->addErrorMessage($e->getMessage());
            }
        }
    }
}
