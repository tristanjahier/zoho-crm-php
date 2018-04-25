<?php

class UsersTest extends PHPUnit\Framework\TestCase
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

    public function testGetAll()
    {
        $users = $this->zohoInstance->users->getAll();

        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $users);
    }

    public function testGetActives()
    {
        $users = $this->zohoInstance->users->getActives();

        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $users);
    }

    public function testGetInactives()
    {
        $users = $this->zohoInstance->users->getInactives();

        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $users);
    }

    public function testGetAdmins()
    {
        $users = $this->zohoInstance->users->getAdmins();

        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $users);
    }

    public function testGetActiveConfirmedAdmins()
    {
        $users = $this->zohoInstance->users->getActiveConfirmedAdmins();

        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $users);
    }
}