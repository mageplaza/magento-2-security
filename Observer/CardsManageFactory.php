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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
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
     * CardsManageFactory constructor.
     * @param Data $data
     */
    public function __construct
    (
        Data $data
    )
    {
        $this->_helperData = $data;
    }

    /**
     * @param Observer $observer
     * @return Observer|void
     */
    public function execute(Observer $observer)
    {
        if ($this->_helperData->isReports()){
            $security = [ 'security' => 'Mageplaza\Security\Block\Adminhtml\Dashboard\LoginLog\Grid'];
            $observer['cards']->addData($security);
        }
        return $observer;
    }
}
