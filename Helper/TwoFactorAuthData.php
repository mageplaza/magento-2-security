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

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use DateTime;
use DateTimeZone;
use Exception;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\HTTP\Header;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\Security\BrowserDetector\Browser;
use Mageplaza\Security\BrowserDetector\Os;
use Mageplaza\Security\BrowserDetector\UserAgent;
use Mageplaza\Security\Model\ResourceModel\Trusted\Collection;
use Mageplaza\Security\Model\TrustedFactory;

/**
 * Class Data
 * @package Mageplaza\Security\Helper
 */
class TwoFactorAuthData extends AbstractData
{
    const CONFIG_MODULE_PATH    = 'security';
    const CONFIG_GROUP_PATH     = 'mptwofactorauth';
    const XML_PATH_FORCE_2FA    = 'force_2fa';
    const XML_PATH_WHITELIST_IP = 'whitelist_ip';
    const MP_GOOGLE_AUTH        = 'mp_google_auth';

    /**
     * @var TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var Header
     */
    protected $header;

    /**
     * @var TrustedFactory
     */
    protected $_trustedFactory;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $timezone
     * @param Header $header
     * @param TrustedFactory $trustedFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        TimezoneInterface $timezone,
        TrustedFactory $trustedFactory
    ) {
        $this->_localeDate     = $timezone;
        $this->_trustedFactory = $trustedFactory;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param $userId
     *
     * @return Collection
     */
    public function getTrustedCollection($userId)
    {
        /** @var Collection $trustedCollection */
        $trustedCollection = $this->_trustedFactory->create()->getCollection();
        $trustedCollection->addFieldToFilter('user_id', $userId);

        return $trustedCollection;
    }

    /**
     * @param $date
     *
     * @return DateTime
     * @throws Exception
     */
    public function convertTimeZone($date)
    {
        $dateTime = new DateTime($date, new DateTimeZone('UTC'));
        $dateTime->setTimezone(new DateTimeZone($this->_localeDate->getConfigTimezone()));

        return $dateTime;
    }

    /**
     * @param $storeId
     *
     * @return array|bool|mixed
     */
    public function isEnabled($storeId = null)
    {
        return $this->getModuleConfig(self::CONFIG_GROUP_PATH . '/general/enabled', $storeId);
    }

    /**
     * @param $code
     * @param $scopeId
     *
     * @return array|mixed
     */
    public function getTwoFactorAuthConfigGeneral($code, $scopeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getModuleConfig(self::CONFIG_GROUP_PATH . '/general' . $code, $scopeId);
    }
    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getForceTfaConfig($scopeId = null)
    {
        return $this->getTwoFactorAuthConfigGeneral(self::XML_PATH_FORCE_2FA, $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return mixed
     */
    public function getWhitelistIpsConfig($scopeId = null)
    {
        $whitelistIp = $this->getTwoFactorAuthConfigGeneral(self::XML_PATH_WHITELIST_IP, $scopeId);

        return explode(',', (string) $whitelistIp);
    }

    /**
     * @param $secret
     *
     * @return string
     */
    public function generateUri($secret)
    {
        $renderer = new ImageRenderer(
            new RendererStyle(171, 0),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        return $writer->writeString($secret);
    }

    /**
     * Check Ip
     *
     * @param $ip
     * @param $range
     *
     * @return bool
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function checkIp($ip, $range)
    {
        if (strpos($range, '*') !== false) {
            $low = $high = $range;
            if (strpos($range, '-') !== false) {
                [$low, $high] = explode('-', (string) $range, 2);
            }
            $low   = str_replace('*', '0', $low);
            $high  = str_replace('*', '255', $high);
            $range = $low . '-' . $high;
        }
        if (strpos($range, '-') !== false) {
            [$low, $high] = explode('-', (string) $range, 2);

            return $this->ipCompare($ip, $low, 1) && $this->ipcompare($ip, $high, -1);
        }

        return $this->ipCompare($ip, $range);
    }

    /**
     * @param $ip1
     * @param $ip2
     * @param int $op
     *
     * @return bool
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    private function ipCompare($ip1, $ip2, $op = 0)
    {
        $ip1Arr = explode('.', (string) $ip1);
        $ip2Arr = explode('.', (string) $ip2);

        for ($i = 0; $i < 4; $i++) {
            if ($ip1Arr[$i] < $ip2Arr[$i]) {
                return ($op === -1);
            }
            if ($ip1Arr[$i] > $ip2Arr[$i]) {
                return ($op === 1);
            }
        }

        return ($op === 0);
    }

    /**
     * @return string
     */
    public function getDeviceName()
    {
        $userAgent = new UserAgent(
            $this->getObject(Header::class)->getHttpUserAgent()
        );
        $os        = new Os($userAgent);
        $browser   = new Browser($userAgent);

        return implode('-', [$os->getName(), $browser->getName(), $browser->getVersion()]);
    }
}
