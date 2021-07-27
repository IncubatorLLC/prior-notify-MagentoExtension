<?php 
namespace IncubatorLLC\PriorNotify\Model\ResourceModel\DataExample;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection{
	public function _construct(){
		$this->_init("IncubatorLLC\PriorNotify\Model\DataExample","IncubatorLLC\PriorNotify\Model\ResourceModel\DataExample");
	}
}
?>