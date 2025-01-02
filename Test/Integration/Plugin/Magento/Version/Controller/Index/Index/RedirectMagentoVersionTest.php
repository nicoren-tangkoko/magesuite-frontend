<?php

declare(strict_types=1);

namespace MageSuite\Frontend\Test\Integration\Plugin\Magento\Version\Controller\Index\Index;

class RedirectMagentoVersionTest extends \Magento\TestFramework\TestCase\AbstractController
{
    protected ?\Magento\Framework\App\Http $app;

    public function setUp(): void
    {
        parent::setUp();
        $this->app = $this->_objectManager->get(\Magento\Framework\App\Http::class);
    }

    /**
     * @magentoAppArea frontend
     */
    public function testMagentoVersionRedirect(): void
    {
        $this->getRequest()->setRequestUri('/magento_version');
        $this->app->launch();

        $this->assertEquals(404, $this->getResponse()->getHttpResponseCode(), 'Expected status code is 404.');

        $this->assertStringContainsString(
            '404 Not Found',
            $this->getResponse()->getBody(),
            'Expected response body contains "404 Not Found".'
        );
    }
}
