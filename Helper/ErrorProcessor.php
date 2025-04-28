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

namespace Mageplaza\Security\Helper;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Config\Model\Config\Reader\Source\Deployed\DocumentRoot;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Escaper;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template\File\Resolver;
use Magento\Framework\View\LayoutInterface;
use Mageplaza\Security\Error\Processor;
use Psr\Log\LoggerInterface;

/**
 * Class ErrorProcessor
 * @package Mageplaza\Security\Helper
 */
class ErrorProcessor extends Processor
{
    /**
     * @var Resolver
     */
    protected $_resolver;

    /**
     * @var string
     */
    protected $errorCode;

    /**
     * @param Http $response
     * @param RequestInterface $request
     * @param TransportBuilder $transportBuilder
     * @param LoggerInterface $logger
     * @param LayoutInterface $layout
     * @param File $file
     * @param Json|null $serializer
     * @param Escaper|null $escaper
     * @param DocumentRoot|null $documentRoot
     */
    public function __construct(
        Http $response,
        RequestInterface $request,
        TransportBuilder $transportBuilder,
        LoggerInterface $logger,
        LayoutInterface $layout,
        File $file,
        ?Json $serializer = null,
        ?Escaper $escaper = null,
        ?DocumentRoot $documentRoot = null
    ) {
        parent::__construct(
            $response,
            $request,
            $transportBuilder,
            $logger,
            $layout,
            $file,
            $serializer,
            $escaper,
            $documentRoot
        );
    }

    /**
     * Process security report
     *
     * @param string $errorCode
     * @param string $reportData
     * @param string $title
     *
     * @return null
     */
    public function processSecurityReport($errorCode = '', $reportData = '', $title = '')
    {
        $this->pageTitle  = $title ?: __('You don\'t have permission to access this page');
        $this->pageTitle  = $title ?: __('You don\'t have permission to access this page');
        $this->reportData = $reportData;
        $this->errorCode  = $errorCode;
        $this->errorCode  = $errorCode;

        $html = $this->_renderPage('security_report');

        $this->_response->setHttpResponseCode(401);
        $this->_response->setBody($html);
        $this->_response->sendResponse();

        return null;
    }

    /**
     * Find template path
     *
     * @param string $template
     *
     * @return string
     */
    protected function _getTemplatePath($template)
    {
        if ($template === 'security_report') {
            return $this->_resolver->getTemplateFileName(
                'report.phtml',
                ['module' => 'Mageplaza_Security', 'area' => FrontNameResolver::AREA_CODE]
            );
        }

        return parent::_getTemplatePath($template);
    }
}
