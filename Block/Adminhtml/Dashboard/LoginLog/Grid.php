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

namespace Mageplaza\Security\Block\Adminhtml\Dashboard\LoginLog;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendData;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Mageplaza\Security\Block\Widget\Grid\Column\Renderer\Status;
use Mageplaza\Security\Block\Widget\Grid\Column\Renderer\Time;
use Mageplaza\Security\Helper\Data;
use Mageplaza\Security\Model\ResourceModel\LoginLog\CollectionFactory;

/**
 * Class Grid
 * @package Mageplaza\Security\Block\Adminhtml\Dashboard\LoginLog
 */
class Grid extends \Magento\Backend\Block\Dashboard\Grid
{
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var string
     */
    protected $_template = 'Mageplaza_Security::dashboard/grid.phtml';

    /**
     * Grid constructor.
     *
     * @param Context $context
     * @param BackendData $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackendData $backendHelper,
        CollectionFactory $collectionFactory,
        Data $helperData,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_helperData = $helperData;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('loginLogGrid');
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create()->setOrder('time', 'DESC');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepares page sizes for dashboard grid with las 5 login log
     *
     * @return void
     */
    protected function _preparePage()
    {
        $this->getCollection()
            ->setPageSize($this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
    }

    /**
     * @inheritdoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('search-query', [
            'header'   => __('User Name'),
            'sortable' => false,
            'index'    => 'user_name',
            'default'  => __('User Name')
        ]);

        $this->addColumn('num-result', [
            'header'   => __('Status'),
            'type'     => 'bool',
            'renderer' => Status::class,
            'sortable' => false,
            'index'    => 'status'
        ]);

        $this->addColumn('popularity', [
            'header'   => __('Time'),
            'sortable' => false,
            'renderer' => Time::class,
            'type'     => 'datetime',
            'index'    => 'time'
        ]);

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('mpsecurity/loginlog/edit', ['id' => $row->getId()]);
    }

    /**
     * @return bool
     */
    public function canShowDetail()
    {
        return false;
    }

    /**
     * @return Phrase
     */
    public function getTitle()
    {
        return __('Security');
    }

    /**
     * @return string
     */
    public function getRate()
    {
        return '';
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function getContentHtml()
    {
        return $this->getLayout()->createBlock(self::class)->setArea('adminhtml')
            ->toHtml();
    }

    /**
     * @return bool
     */
    public function isReports()
    {
        return $this->_helperData->isReports();
    }
}
