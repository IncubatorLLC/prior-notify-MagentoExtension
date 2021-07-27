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

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\HTTP\Client\Curl;


/**
 * Class AfterSave
 * @package IncubatorLLC\PriorNotify\Observer
 */
abstract class AfterSave implements ObserverInterface
{
    /**
     * @param Observer $observer
     *
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $item = $observer->getDataObject();

        $body = $this->generateLiquidTemplate($item);

        // $this->logger->debug('execute', $body);

        // $this->logger->debug('execute');

        // $hookCollection = $this->hookFactory->create()->getCollection()
        //     ->addFieldToFilter('hook_type', $hookType)
        //     ->addFieldToFilter('status', 1)
        //     ->addFieldToFilter('store_ids', [
        //         ['finset' => Store::DEFAULT_STORE_ID],
        //         ['finset' => $this->getItemStore($item)]
        //     ])
        //     ->setOrder('priority', 'ASC');


        // $schedule = $this->helper->getCronSchedule();
        // if ($schedule !== Schedule::DISABLE && $schedule !== null) {
        //     $hookCollection = $this->hookFactory->create()->getCollection()
        //         ->addFieldToFilter('hook_type', $this->hookType)
        //         ->addFieldToFilter('status', 1)
        //         ->addFieldToFilter('store_ids', [
        //             ['finset' => Store::DEFAULT_STORE_ID],
        //             ['finset' => $this->helper->getItemStore($item)]
        //         ])
        //         ->setOrder('priority', 'ASC');
        //     if ($hookCollection->getSize() > 0) {
        //         $schedule = $this->scheduleFactory->create();
        //         $data = [
        //             'hook_type' => $this->hookType,
        //             'event_id' => $item->getId(),
        //             'status' => '0'
        //         ];

        //         try {
        //             $schedule->addData($data);
        //             $schedule->save();
        //         } catch (Exception $exception) {
        //             $this->messageManager->addError($exception->getMessage());
        //         }
        //     }
        // } else {


            // $this->helper->send($item, $this->hookType);
        // }
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

        // $this->logger->debug('execute', $body);

        // $this->logger->debug('execute');

        // $hookCollection = $this->hookFactory->create()->getCollection()
        //     ->addFieldToFilter('hook_type', $hookType)
        //     ->addFieldToFilter('status', 1)
        //     ->addFieldToFilter('store_ids', [
        //         ['finset' => Store::DEFAULT_STORE_ID],
        //         ['finset' => $this->getItemStore($item)]
        //     ])
        //     ->setOrder('priority', 'ASC');
        // $schedule = $this->helper->getCronSchedule();
        // if ($schedule !== Schedule::DISABLE && $schedule !== null) {
        //     $hookCollection = $this->hookFactory->create()->getCollection()
        //         ->addFieldToFilter('hook_type', $this->hookType)
        //         ->addFieldToFilter('status', 1)
        //         ->addFieldToFilter('store_ids', [
        //             ['finset' => Store::DEFAULT_STORE_ID],
        //             ['finset' => $this->helper->getItemStore($item)]
        //         ])
        //         ->setOrder('priority', 'ASC');
        //     if ($hookCollection->getSize() > 0) {
        //         $schedule = $this->scheduleFactory->create();
        //         $data = [
        //             'hook_type' => $this->hookTypeUpdate,
        //             'event_id' => $item->getId(),
        //             'status' => '0'
        //         ];
        //         try {
        //             $schedule->addData($data);
        //             $schedule->save();
        //         } catch (Exception $exception) {
        //             $this->messageManager->addError($exception->getMessage());
        //         }
        //     }
        // } else {
            // $this->helper->send($item, $this->hookTypeUpdate);
        // }
    }

    /**
     * @param $item
     *
     * @return string
     */
    public function generateLiquidTemplate($item)
    {
        try {
            if ($item instanceof Product) {
                $item->setStockItem(null);
            }

            if ($item->getShippingAddress()) {
                $item->setData('shippingAddress', $item->getShippingAddress()->getData());
            }

            if ($item->getBillingAddress()) {
                $item->setData('billingAddress', $item->getBillingAddress());
            }

            return $item->getData();
        } catch (Exception $e) {
            // $this->logger->error($e->getMessage());
        }

        return '';
    }

    // /**
    //  * @return Curl
    //  */
    // public function getCurlClient()
    // {
    //     return $this->curl;
    // }
}
