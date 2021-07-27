<?php
namespace IncubatorLLC\PriorNotify\Controller\Adminhtml\UpwardConnector;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 */
class Index extends Action implements HttpGetActionInterface
{
    const MENU_ID = 'IncubatorLLC_PriorNotify::system_prior_notify';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Load the page defined in view/adminhtml/layout/exampleadminnewpage_upwardconnector_index.xml
     *
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(static::MENU_ID);
        $resultPage->getConfig()->getTitle()->prepend(__(''));

        return $resultPage;


         /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $this->_view->loadLayout();

        $post = $this->getRequest()->getPostValue();
        $we =  $this->getRequest()->getParams();         

    // echo "<pre>";print_r($we);        
        if(!empty($we['ripple_email'])){
            $register_email= $we['ripple_email']; 
            if(!class_exists('afclass'))
            include_once 'afclass.php';  
            // $curl_info = new afclass;
            $asset_id = Afclass::before_registration($register_email); 
            $value =225; 
            $block = $this->_view->getLayout()->createBlock('module\Block\Adminhtml\CustomBlock');

            $block->setFeedback($value);

        }

    $this->_view->renderLayout();
    }
}