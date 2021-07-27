<?php 
namespace IncubatorLLC\PriorNotify\Model\ResourceModel;
class DataExample extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb{
    public function _construct(){
        $this->_init("prior_notify","id");
    }
}
?>