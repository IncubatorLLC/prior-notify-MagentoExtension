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

namespace IncubatorLLC\PriorNotify\Model\ResourceModel\Hook;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use IncubatorLLC\PriorNotify\Model\ResourceModel\Hook;

/**
 * Class Collection
 * @package IncubatorLLC\PriorNotify\Model\ResourceModel\Hook
 */
class Collection extends AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'hook_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_webhook_hook_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'hook_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\IncubatorLLC\PriorNotify\Model\Hook::class, Hook::class);
    }
}
