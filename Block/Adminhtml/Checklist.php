<?php

/**
 * Created by PhpStorm.
 * User: nghia
 * Date: 14/03/2018
 * Time: 10:57
 */

namespace Mageplaza\Security\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Config\ConfigOptionsListConstants;


class Checklist extends \Magento\Backend\Block\Template
{

    protected $_template = 'Mageplaza_Security::checklist/index.phtml';

    protected $_userFactory;

    protected $_helper;

    protected $_deploymentConfig;
    protected $_metadata;

    protected $commonNames = ['admin', 'root', 'test', 'magento'];

    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $metadata,
        \Magento\User\Model\UserFactory $userFactory,
        \Mageplaza\Security\Helper\Data $helper,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        Context $context,
        array $data = [])
    {
        $this->_metadata = $metadata;
        $this->_helper = $helper;
        $this->_userFactory = $userFactory;
        $this->_deploymentConfig = $deploymentConfig;
        parent::__construct($context, $data);
    }
    public function checkAdminUserName()
    {
        $userCollection = $this->_userFactory->create()->getCollection();
        $unSecureNames = [];
        foreach ($userCollection as $user) {
            if (in_array($user->getUserName(), $this->commonNames)) {
                $unSecureNames[] = [
                    'username' => $user->getUserName(),
                    'user_id' => $user->getUserId()
                ];
            }
        }
        return $unSecureNames;
    }

    public function checkFrontendCaptcha()
    {
        $customerCaptcha = $this->_helper->getConfigValue('customer/captcha/enable');
        return $customerCaptcha;

    }

    public function checkBackendCaptcha()
    {

        $adminCaptcha = $this->_helper->getConfigValue('admin/captcha/enable');
        return $adminCaptcha;
    }

    public function checkLatestVersion()
    {
        $realeases = file_get_contents('https://raw.githubusercontent.com/mageplaza/magento-versions/master/releases/releases.json');
        $arr = json_decode($realeases);

        $versionArr = [];
        foreach ($arr as $ver => $item) {

            list($major, $minor, $patch) = explode('.', $ver);
            if ($major == 2) {
                $versionArr[$major . '.' . $minor][] = $ver;
            }
        }
        $currentVersion = $this->_metadata->getVersion();
        list($currentMajor, $currentMinor, $currentPatch) = explode('.', $currentVersion);

        $lastestVer = $currentVersion;
        foreach ($versionArr[$currentMajor . '.' . $currentMinor] as $version) {
            if (version_compare($lastestVer, $version, '<')) {
                $lastestVer = $version;
            }
        }

        return [
            'lastestVer' => $lastestVer,
            'currentVersion' => $currentVersion
        ];

    }

    public function getDatabasePrefix()
    {
        return (string)$this->_deploymentConfig->get(ConfigOptionsListConstants::CONFIG_PATH_DB_PREFIX);
    }
}