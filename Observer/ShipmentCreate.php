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
 * @package     Mageplaza_Webhook
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace IncubatorLLC\PriorNotify\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Config\ScopeConfigInterface;
/**
 * Class ShipmentCreate
 * @package IncubatorLLC\PriorNotify\Observer
 */
class ShipmentCreate implements ObserverInterface
{
    const API_URL = 'prior_notify/plugin_config/api_url';

    const PRIOR_NOTIFY = 'prior_notify';

    const SALES_SHIPMENT = 'sales_shipment';

    /**
     * CURL client
     *
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * AfterSave constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->resourceConnection = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $this->logger = $logger;
        $this->curl = new Curl();
    }
    
    /**
     * @param Observer $observer
     *
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $track = $observer->getEvent()->getTrack()->getData();
        $connection = $this->resourceConnection->getConnection();

        $priorNotifyTableName = $connection->getTableName(self::PRIOR_NOTIFY);
        $query = 'SELECT value FROM ' . $priorNotifyTableName . ' ORDER BY id DESC limit 1';
        $token =$this->resourceConnection->getConnection()->fetchOne($query);

        if (!$token) {
            $this->logger->debug('ShipmentCreate token is not valid');
            return false;
        }

        $storeManager = ObjectManager::getInstance()->get(StoreManagerInterface::class);
        $baseUrl = $storeManager->getStore()->getBaseUrl();

        $scopeConfig = ObjectManager::getInstance()->get(ScopeConfigInterface::class);

        $apiUrl = $scopeConfig->getValue(self::API_URL);
        
        $url = $apiUrl . "/webhooks/magento_order_updated";

        $salesShipmentTablesName = $connection->getTableName(self::SALES_SHIPMENT);
        $selectSalesShipment = $connection->select()->from($salesShipmentTablesName)->where('entity_id = ?', $track['parent_id']);
        $resultSalesShipment = $connection->fetchAll($selectSalesShipment);

        $body = array(
            'shipment' => $resultSalesShipment,
            'track' => $track,
            'base_url' => $baseUrl,
        );

        $this->curl->addHeader("x-magento-plugin-token", "Token ".$token);
        $this->curl->post($url, ['data' =>json_encode($body)]);
        $response = json_decode($this->curl->getBody(), true);
        $result = $this->curl->getBody();

        if (isset($response['success'])) {
            $this->logger->debug('ShipmentCreate success');
        }
    }

    /**
     * @param $observer
     *
     * @throws Exception
     */
    protected function updateObserver($observer)
    {
        $item = $observer->getDataObject();

        $body = $this->generateLiquidTemplate($item);

        $this->logger->debug('ShipmentCreate updateObserver', $body);
    }
}
