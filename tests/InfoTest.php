<?php

class InfoTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var Zoho\CRM\Client
     */
    private $zohoInstance = null;

    protected function setUp()
    {
        if (!$this->zohoInstance)
            $this->zohoInstance = new Zoho\CRM\Client(getenv('AUTH_TOKEN'));
    }

    public function testGetModules()
    {
        $modules = $this->zohoInstance->info->getModules();

        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $modules);
        $this->assertInternalType('array', $modules->getContent());
    }

}