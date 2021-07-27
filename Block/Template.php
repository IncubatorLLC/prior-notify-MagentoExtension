<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace IncubatorLLC\PriorNotify\Block;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Directory\Helper\Data as DirectoryHelper;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @api
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @since 100.0.2
 */
class Template extends \Magento\Framework\View\Element\Template
{
    const PRIOR_NOTIFY_TABLE = 'prior_notify';
    const FRONT_URL = 'prior_notify/plugin_config/front_url';
    const API_URL = 'prior_notify/plugin_config/api_url';

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * @var \IncubatorLLC\PriorNotify\Model\Session
     */
    protected $_backendSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var \Magento\Framework\Code\NameBuilder
     */
    protected $nameBuilder;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \IncubatorLLC\PriorNotify\Block\Template\Context $context
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        Curl $curl,
        ResourceConnection $resourceConnection,
        // ScopeConfigInterface $scopeConfig,
        \IncubatorLLC\PriorNotify\Block\Template\Context $context,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        $this->_localeDate = $context->getLocaleDate();
        $this->_authorization = $context->getAuthorization();
        $this->mathRandom = $context->getMathRandom();
        $this->_backendSession = $context->getBackendSession();
        $this->formKey = $context->getFormKey();
        $this->nameBuilder = $context->getNameBuilder();
        $this->scopeConfig = $context->getScopeConfig();
        $this->curl = $curl;
        $this->resourceConnection = $resourceConnection;
        $data['jsonHelper'] = $jsonHelper ?? ObjectManager::getInstance()->get(JsonHelper::class);
        $data['directoryHelper']= $directoryHelper ?? ObjectManager::getInstance()->get(DirectoryHelper::class);
        parent::__construct($context, $data);
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * Check whether or not the module output is enabled.
     *
     * Because many module blocks belong to Backend module,
     * the feature "Disable module output" doesn't cover Admin area.
     *
     * @param string $moduleName Full module name
     * @return boolean
     * @deprecated 100.2.0 Magento does not support disabling/enabling modules output from the Admin Panel since 2.2.0
     * version. Module output can still be enabled/disabled in configuration files. However, this functionality should
     * not be used in future development. Module design should explicitly state dependencies to avoid requiring output
     * disabling. This functionality will temporarily be kept in Magento core, as there are unresolved modularity
     * issues that will be addressed in future releases.
     */
    public function isOutputEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->getModuleName();
        }

        return !$this->_scopeConfig->isSetFlag(
            'advanced/modules_disable_output/' . $moduleName,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Make this public so that templates can use it properly with template engine
     *
     * @return \Magento\Framework\AuthorizationInterface
     */
    public function getAuthorization()
    {
        return $this->_authorization;
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->_eventManager->dispatch('adminhtml_block_html_before', ['block' => $this]);
        return parent::_toHtml();
    }

    /**
     * Return toolbar block instance
     *
     * @return bool|\Magento\Framework\View\Element\BlockInterface
     */
    public function getToolbar()
    {
        return $this->getLayout()->getBlock('page.actions.toolbar');
    }

    /**
     * Return PriorNotify token
     *
     * @return bool
     */
    public function getStatus()
    {
        try {
            $connection  = $this->resourceConnection->getConnection();
            $tableName = $connection->getTableName(self::PRIOR_NOTIFY_TABLE);

            $query = 'SELECT value FROM ' . $tableName . ' ORDER BY id DESC limit 1';
            $token =$this->resourceConnection->getConnection()->fetchOne($query);

            if (!$token) {
                return false;
            }

            $url = "https://dev-api.priornotify.com/users/me/magento-connection";

            $this->curl->addHeader("x-magento-plugin-token", "Token ".$token);
            $this->curl->get($url);
            $response = json_decode($this->curl->getBody(), true);
            $result = $this->curl->getBody();

            if (isset($response['data']['host'])) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Return plugin URL
     *
     * @return string
     */
    public function getBaseUrl() {
        $storeManager = ObjectManager::getInstance()->get(StoreManagerInterface::class);
        return $storeManager->getStore()->getBaseUrl();
    }

    /**
     * Return PriorNotify URL
     *
     * @return string
     */
    public function getFrontUrl() {
        return $this->scopeConfig->getValue(self::FRONT_URL);
    }

    /**
     * Return PriorNotify API URL
     *
     * @return string
     */
    public function getApiUrl() {
        return $this->scopeConfig->getValue(self::API_URL);
    }
}
