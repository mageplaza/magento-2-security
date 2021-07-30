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

namespace Mageplaza\Security\Plugin\Adminhtml;

use Closure;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\Model\Export\MetadataProvider;
use Mageplaza\Security\Helper\Data;

/**
 * Class ExportCsv
 * @package Mageplaza\Security\Plugin\Adminhtml
 */
class ExportCsv
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * ExportCsv constructor.
     *
     * @param Data $helperData
     * @param RequestInterface $request
     */
    public function __construct(
        Data $helperData,
        RequestInterface $request
    ) {
        $this->helperData = $helperData;
        $this->request    = $request;
    }

    /**
     * @param MetadataProvider $subject
     * @param Closure $proceed
     * @param DocumentInterface $document
     * @param array $fields
     * @param array $options
     *
     * @return array|mixed
     */
    public function aroundGetRowData(
        MetadataProvider $subject,
        Closure $proceed,
        DocumentInterface $document,
        $fields,
        $options
    ) {
        $namespace = $this->request->getParam('namespace');

        if ($this->helperData->isEnabled() && $namespace === 'mageplaza_security_loginlog_listing') {
            $row = [];
            foreach ($fields as $column) {
                if (isset($options[$column])) {
                    $key = $document->getCustomAttribute($column)->getValue();
                    if (isset($options[$column][$key])) {
                        $row[] = $options[$column][$key];
                    } else {
                        $row[] = '';
                    }
                } else {
                    $value = $document->getCustomAttribute($column)->getValue();
                    if ($column === 'time') {
                        $value = $this->helperData->convertToLocaleTime($value);
                    }
                    $row[] = $value;
                }
            }

            return $row;
        }

        return $proceed($document, $fields, $options);
    }
}
