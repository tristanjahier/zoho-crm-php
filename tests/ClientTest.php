<?php

class ClientTest extends PHPUnit\Framework\TestCase
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

    public function testAttachModule()
    {
        $module = $this->zohoInstance->attachModule('Zoho\CRM\Api\Modules\Leads');

        $this->assertInstanceOf(Zoho\CRM\Api\Modules\Leads::class, $module);

        $this->assertTrue($module->hasAssociatedEntity());
        $this->assertEquals(Zoho\CRM\Entities\Lead::class, $module->associatedEntity());
        $this->assertEquals($module->attachedClient(), $this->zohoInstance);
        $this->assertInternalType('array', $module->supportedMethods());

        $fields = $module->fields();

        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $fields->getAll());
        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $fields->getNative());
        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $fields->getSummary());
        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $fields->getCustom());
        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $fields->getMandatory());
    }

    public function testFailedAttachModule()
    {
        $this->expectException(Zoho\CRM\Exception\ModuleNotFoundException::class);
        $this->zohoInstance->attachModule('Fail');
    }

    public function testAttachModules()
    {
        $module = $this->zohoInstance->attachModules(['Zoho\CRM\Api\Modules\Leads', 'Zoho\CRM\Api\Modules\Users']);

        $this->assertNull($module);
    }

    public function testDefaultModules()
    {
        $modules = Zoho\CRM\Client::defaultModules();

        $this->assertInternalType('array', $modules);
    }

    public function testPreferences()
    {
        $preferences = $this->zohoInstance->preferences();

        $this->assertInstanceOf(Zoho\CRM\ClientPreferences::class, $preferences);
    }

    public function testGetAuthToken()
    {
        $token = $this->zohoInstance->getAuthToken();

        $this->assertInternalType('string', $token);
        $this->assertEquals(getenv('AUTH_TOKEN'), $token);
    }


    public function testFailedSetAuthToken()
    {
        $this->expectException(Zoho\CRM\Exception\NullAuthTokenException::class);
        $token = $this->zohoInstance->setAuthToken(null);
    }

    public function testGetDefaultParameters()
    {
        $parameters = $this->zohoInstance->getDefaultParameters();

        $this->assertInternalType('array', $parameters);
    }

    public function testSetDefaultParameters()
    {
        $this->zohoInstance->setDefaultParameters(['PARAM' => 'VALUE']);

        $this->assertEquals(['PARAM' => 'VALUE'],         $this->zohoInstance->getDefaultParameters());
    }

    public function testSetDefaultParameter()
    {
        $this->zohoInstance->setDefaultParameter('PARAM', 'VALUE');

        $this->assertEquals('VALUE',         $this->zohoInstance->getDefaultParameters()['PARAM']);

        $this->zohoInstance->unsetDefaultParameter('PARAM');
        $this->assertArrayNotHasKey('PARAM', $this->zohoInstance->getDefaultParameters());
    }
}