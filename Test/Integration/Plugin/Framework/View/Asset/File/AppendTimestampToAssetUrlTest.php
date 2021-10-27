<?php

namespace MageSuite\Frontend\Test\Integration\Plugin\Framework\View\Asset\File;

class AppendTimestampToAssetUrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface|mixed
     */
    protected $configWriter;

    /**
     * @var \Magento\Framework\View\Asset\Repository|mixed
     */
    protected $assetRepository;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->configWriter = $this->objectManager->create(\Magento\Framework\App\Config\Storage\WriterInterface::class);
        $this->assetRepository = $this->objectManager->create(\Magento\Framework\View\Asset\Repository::class);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoAppArea frontend
     * @dataProvider cases
     */
    public function testItGeneratesCorrectUrl($timestamp, $file, $expectedUrl) {
        if($timestamp > 0) {
            $this->configWriter->save(
                \MageSuite\Frontend\Helper\Configuration::XML_PATH_ASSETS_URL_TIMESTAMP,
                $timestamp
            );
        }
        else {
            $this->configWriter->delete(\MageSuite\Frontend\Helper\Configuration::XML_PATH_ASSETS_URL_TIMESTAMP);
        }

        $file = $this->assetRepository->createAsset($file);

        $this->assertMatchesRegularExpression(
            $this->escapeRegEx($expectedUrl),
            $file->getUrl()
        );
    }

    public static function cases() {
        return [
            'js_file_with_timestamp' => [
                1634822082,
                'require/requirejs.js',
                'http://localhost/static/version([0-9]+)/frontend/Magento/luma/en_US/require/requirejs.js\?t=1634822082'
            ],
            'css_file_with_timestamp' => [
                1634822082,
                'style.css',
                'http://localhost/static/version1634822211/frontend/Magento/luma/en_US/style.css\?t=1634822082'
            ],
            'js_file_without_timestamp' => [
                0,
                'require/requirejs.js',
                'http://localhost/static/version([0-9]+)/frontend/Magento/luma/en_US/require/requirejs.js'
            ],
            'css_file_without_timestamp' => [
                0,
                'style.css',
                'http://localhost/static/version1634822211/frontend/Magento/luma/en_US/style.css'
            ],
            'image_without_timestamp' => [
                1634822082,
                'image.jpg',
                'http://localhost/static/version1634822211/frontend/Magento/luma/en_US/image.jpg'
            ],
        ];
    }

    public function escapeRegEx($regEx) {
        return sprintf('/^%s$/', str_replace('/', '\/', $regEx));
    }
}
