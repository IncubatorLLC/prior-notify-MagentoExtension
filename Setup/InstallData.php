<?php
    namespace IncubatorLLC\PriorNotify\Setup;

    use Magento\Framework\Setup\ModuleContextInterface;
    use Magento\Framework\Setup\ModuleDataSetupInterface;
    use Magento\Integration\Model\ConfigBasedIntegrationManager;
    use Magento\Framework\App\Config\Storage\WriterInterface;
    use Magento\Framework\Setup\InstallDataInterface;

    class InstallData implements InstallDataInterface
    {
        const FRONT_URL = 'prior_notify/plugin_config/front_url';
        const API_URL = 'prior_notify/plugin_config/api_url';

        /**
         * @var ConfigBasedIntegrationManager
        */
        private $integrationManager;

        /**
         * @var WriterInterface
        */
        private $configWriter;

        /**
         * @param ConfigBasedIntegrationManager $integrationManager
         * @param WriterInterface $configWriter
        */

        public function __construct(
            ConfigBasedIntegrationManager $integrationManager,
            WriterInterface $configWriter
        ) {
            $this->integrationManager = $integrationManager;
            $this->configWriter = $configWriter;
        }

        /**
         * {@inheritdoc}
        */
        public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
        {
            $this->configWriter->save(self::API_URL, "https://dev-api.priornotify.com");
            $this->configWriter->save(self::FRONT_URL, "https://dev-login.priornotify.com");

            $this->integrationManager->processIntegrationConfig(['PriorNotify Integration']);
        }
    }