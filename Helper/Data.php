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

use DateTime;
use DateTimeZone;
use Exception;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Sinergi\BrowserDetector\Browser;
use Sinergi\BrowserDetector\Os;
use Sinergi\BrowserDetector\UserAgent;

/**
 * Class Data
 * @package Mageplaza\Security\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH        = 'security';
    const XML_PATH_BRUTE_FORCE      = 'brute_force';
    const XML_PATH_BLACK_WHITE_LIST = 'black_white_list';

    /**
     * @var Browser
     */
    protected $browserLib;

    /**
     * @var Os
     */
    protected $osLib;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        TimezoneInterface $timezone
    ) {
        $this->timezone = $timezone;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Get Brute Force Config
     *
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigBruteForce($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getModuleConfig(self::XML_PATH_BRUTE_FORCE . $code, $storeId);
    }

    /**
     * Get Black-White List Config
     *
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigBlackWhiteList($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getModuleConfig(self::XML_PATH_BLACK_WHITE_LIST . $code, $storeId);
    }

    /**
     * Check Ip
     *
     * @param string $ip
     * @param string $range
     *
     * @return bool
     */
    public function checkIp($ip, $range)
    {
        if (strpos($range, '*') !== false) {
            $low = $high = $range;
            if (strpos($range, '-') !== false) {
                list($low, $high) = explode('-', $range, 2);
            }
            $low   = str_replace('*', '0', $low);
            $high  = str_replace('*', '255', $high);
            $range = $low . '-' . $high;
        }
        if (strpos($range, '-') !== false) {
            list($low, $high) = explode('-', $range, 2);

            return $this->ipCompare($ip, $low, 1) && $this->ipcompare($ip, $high, -1);
        }

        return $this->ipCompare($ip, $range);
    }

    /**
     * @param string $ip1
     * @param string $ip2
     * @param int $op
     *
     * @return bool
     */
    private function ipCompare($ip1, $ip2, $op = 0)
    {
        $ip1Arr = explode('.', $ip1);
        $ip2Arr = explode('.', $ip2);

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

    /***
     * @param $userAgent
     * @param null $full
     *
     * @return array|string
     */
    public function getBrowser($userAgent, $full = null)
    {
        $userAgent = new UserAgent($userAgent);
        $os        = new Os($userAgent);
        $browser   = new Browser($userAgent);

        if ($full) {
            return [
                'browser'           => $browser->getName(),
                'browser_version'   => $browser->getVersion(),
                'plat_form'         => $os->getName(),
                'plat_form_version' => $os->getVersion()
            ];
        }

        return $browser->getName();
    }

    /**
     * @return bool
     */
    public function isReports()
    {
        return $this->isModuleOutputEnabled('Mageplaza_Reports')
            && $this->getConfigValue('mageplaza_reports/general/enabled');
    }

    /**
     * @param string $time
     * @param string $format
     *
     * @return string
     */
    public function convertToLocaleTime($time, $format = 'Y-m-d H:i:s')
    {
        try {
            $localTime   = new DateTime($time, new DateTimeZone('UTC'));
            $localTime->setTimezone(new DateTimeZone($this->timezone->getConfigTimezone()));
            $currentTime = $localTime->format($format);
        } catch (Exception $e) {
            $currentTime = '';
        }

        return $currentTime;
    }
}
