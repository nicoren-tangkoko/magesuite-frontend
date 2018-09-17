<?php

namespace MageSuite\Frontend\Setup;

/**
 * This class allows to set up static_content_on_demand_in_production in env.php file using CLI parameter for
 * setup:install command.
 * static_content_on_demand_in_production must be set to 1 for integration tests to pass without static content deployed
 * in production mode
 */
class ConfigOptionsList implements \Magento\Framework\Setup\ConfigOptionsListInterface
{
    const CONFIG_PATH = 'static_content_on_demand_in_production';

    /**
     * Input key for the options
     */
    const INPUT_KEY_STATIC_ON_DEMAND = 'static-on-demand-production';

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return [
            new \Magento\Framework\Setup\Option\SelectConfigOption(
                self::INPUT_KEY_STATIC_ON_DEMAND,
                \Magento\Framework\Setup\Option\SelectConfigOption::FRONTEND_WIZARD_SELECT,
                [0, 1],
                self::CONFIG_PATH,
                'Deploy static content on demand in production mode',
                0
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function createConfig(array $options, \Magento\Framework\App\DeploymentConfig $deploymentConfig)
    {
        $configData = new \Magento\Framework\Config\Data\ConfigData(\Magento\Framework\Config\File\ConfigFilePool::APP_ENV);

        $optionValue = $this->readConfiguration($options, $deploymentConfig, self::INPUT_KEY_STATIC_ON_DEMAND);

        if($optionValue) {
            $configData->set(self::CONFIG_PATH, $optionValue);
        }

        return [$configData];
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $options, \Magento\Framework\App\DeploymentConfig $deploymentConfig)
    {
        $errors = [];

        return $errors;
    }

    private function readConfiguration(array $options, \Magento\Framework\App\DeploymentConfig $deploymentConfig, $inputKey)
    {
        $config = null;
        $option = $this->getOption($inputKey);

        if ($option) {
            $configPath = $option->getConfigPath($inputKey);
            $config = $options[$inputKey] ?? ($configPath != null ? $deploymentConfig->get($configPath) : $option->getDefault());
        }

        return $config;
    }

    private function getOption($inputKey)
    {
        $option = null;

        foreach ($this->getOptions() as $currentOption) {
            $option = $currentOption->getName() == $inputKey ? $currentOption : $option;
        }

        return $option;
    }
}
