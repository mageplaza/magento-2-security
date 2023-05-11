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

namespace Mageplaza\Security\Controller\Adminhtml\LoginLog;

use Magento\Framework\View\Result\Page;
use Mageplaza\Security\Controller\Adminhtml\AbstractLog;

/**
 * Class Index
 * @package Mageplaza\Security\Controller\Adminhtml\LoginLog
 */
class Index extends AbstractLog
{
    protected $_publicActions = ['view', 'index'];

    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Login Log'));

        return $resultPage;
    }
}
