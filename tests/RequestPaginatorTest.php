<?php

class RequestPaginatorTest extends PHPUnit\Framework\TestCase
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

    /**
     *  Get all records
     */
    public function testGetAll()
    {
        $leads = $this->zohoInstance->leads->getAll();

        $this->assertInternalType('array', $leads->fetchAll());
        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $leads->getAggregatedResponse());
        $this->assertInstanceOf(Zoho\CRM\Api\Request::class, $leads->getRequest());
        $this->assertInternalType('bool', $leads->hasMoreData());
        $this->assertInternalType('int', $leads->getNumberOfPagesFetched());
        $this->assertInternalType('array', $leads->getResponses());
    }

}