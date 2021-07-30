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

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use \Magento\Framework\App\ResponseInterface as AppResponseInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\Security\Controller\Adminhtml\AbstractLog;
use Mageplaza\Security\Model\LoginLog;
use Mageplaza\Security\Model\LoginLogFactory;
use Mageplaza\Security\Model\ResourceModel\LoginLog\CollectionFactory;

/**
 * Class Edit
 * @package Mageplaza\Security\Controller\Adminhtml\LoginLog
 */
class Edit extends AbstractLog
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var LoginLogFactory
     */
    protected $_logFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param LoginLogFactory $_logFactory
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Filter $filter,
        CollectionFactory $collectionFactory,
        LoginLogFactory $_logFactory,
        Registry $registry
    ) {
        $this->_logFactory = $_logFactory;
        $this->registry    = $registry;

        parent::__construct($context, $resultPageFactory, $filter, $collectionFactory);
    }

    /**
     * @return ResultPage|AppResponseInterface|Redirect|ResultInterface|Page
     */
    public function execute()
    {
        $log = $this->initLog();
        if (!$log) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('mpsecurity/loginlog/');

            return $resultRedirect;
        }

        $this->registry->register('mageplaza_security_loginlog', $log);

        /** @var ResultPage|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Login record details'));
        $resultPage->getConfig()->getTitle()->prepend(__('Login record details'));

        return $resultPage;
    }

    /**
     * @param bool $register
     *
     * @return bool|LoginLog
     */
    protected function initLog($register = false)
    {
        $logId = (int) $this->getRequest()->getParam('id');
        $log   = $this->_logFactory->create();

        if ($logId) {
            $log = $log->load($logId);
            if (!$log->getId()) {
                $this->messageManager->addErrorMessage(__('This log no longer exists.'));

                return false;
            }
        }

        if ($register) {
            $this->registry->register('mageplaza_security_loginlog', $log);
        }

        return $log;
    }
}
