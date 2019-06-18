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

namespace Mageplaza\Security\Observer;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\Security\Block\Adminhtml\Dashboard\LoginLog\Grid;
use Mageplaza\Security\Helper\Data;

/**
 * Class CardsManageFactory
 * @package Mageplaza\Security\Observer
 */
class CardsManageFactory implements ObserverInterface
{
    /**
     * @var Data
     */
    public $_helperData;

    /**
     * @var AuthorizationInterface
     */
    protected $_authorization;

    /**
     * CardsManageFactory constructor.
     *
     * @param Data $data
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        Data $data,
        AuthorizationInterface $authorization
    ) {
        $this->_helperData = $data;
        $this->_authorization = $authorization;
    }

    /**
     * @param Observer $observer
     *
     * @return Observer|void
     */
    public function execute(Observer $observer)
    {
        if ($this->_helperData->isEnabled() && $this->_authorization->isAllowed('Mageplaza_Security::grid')) {
            $security = ['security' => Grid::class];
            $observer['cards']->addData($security);
        }

        return $observer;
    }
}
