<?php

class LeadsTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var Zoho\CRM\Client
     */
    private $zohoInstance = null;

    /**
     * @var array
     */
    private $insertedId = [];

    protected function setUp()
    {
        if (!$this->zohoInstance)
            $this->zohoInstance = new Zoho\CRM\Client(getenv('AUTH_TOKEN'));
    }

    /**
     * Insert one record
     */
    public function testInsert()
    {
        $lead = $this->zohoInstance->leads->insert([
            'Company' => 'My company',
            'Last Name' => 'My name'
        ]);

        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $lead);
        $this->assertInternalType('array', $lead->getContent());

        $rawData = json_decode($lead->getRawData());
        $this->assertEquals('2000', $rawData->response->result->row->success->code);

        $this->insertedId[] = $lead->getContent()[0];
    }

    /**
     *  Get all records
     */
    public function testGet()
    {
        if (empty($this->insertedId)) {
            for ($i = 0; $i < 5; $i++)
                $this->testInsert();
        }

        $leads = $this->zohoInstance->leads->getByIds($this->insertedId);
        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $leads);
        $this->assertInternalType('array', $leads->getContent());
    }

    /**
     * Delete one record
     */
    public function testDelete()
    {
        if (empty($this->insertedId))
            $this->testInsert();

        $id = array_pop($this->insertedId);

        $lead = $this->zohoInstance->leads->delete($id);

        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $lead);

        $rawData = json_decode($lead->getRawData());
        $this->assertEquals('5000', $rawData->response->result->code);

        $deleted = $this->zohoInstance->leads->getDeletedIds()->fetch();

        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $deleted);
        $this->assertInternalType('array', $deleted->getContent());
    }

    /**
     * Update one record
     */
    public function testUpdate()
    {
        if (empty($this->insertedId))
            $this->testInsert();

        $id = $this->insertedId[0];

        $lead = $this->zohoInstance->leads->update($id, [
            'Company' => 'My company modified'
        ]);

        $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $lead);
        $this->assertEquals([$id], $lead->getContent());

        $rawData = json_decode($lead->getRawData());
        $this->assertEquals('Record(s) updated successfully', $rawData->response->result->message);
    }

    public function testLeadEntity()
    {
        if (empty($this->insertedId))
            $this->testInsert();

        $response = $this->zohoInstance->leads->getById($this->insertedId[0]);

        $lead = Zoho\CRM\Entities\Lead::createFromResponse($response);

        $this->assertInstanceOf(Zoho\CRM\Entities\Lead::class, $lead);
        $this->assertInternalType('string', $lead->id);

        $lead->id = 'NEW_ID';

        $this->assertEquals('NEW_ID', $lead->id);
    }

    public function testLeadEntityProperties()
    {
        $this->assertInternalType('array', Zoho\CRM\Entities\Lead::supportedProperties());
        $this->assertTrue(Zoho\CRM\Entities\Lead::supports('LEADID'));
        $this->assertFalse(Zoho\CRM\Entities\Lead::supports('INVALID'));
    }

    public function testInvalidLeadEntityAlias()
    {
        if (empty($this->insertedId))
            $this->testInsert();

        $response = $this->zohoInstance->leads->getById($this->insertedId[0]);

        $lead = Zoho\CRM\Entities\Lead::createFromResponse($response);

        $this->assertInstanceOf(Zoho\CRM\Entities\Lead::class, $lead);

        $this->expectException(Zoho\CRM\Exception\UnsupportedEntityPropertyException::class);
        $lead->invalid = 'DADASDAS';
    }

    /**
     * Delete all records inserted after each test
     */
    protected function tearDown()
    {
        if (!empty($this->insertedId)) {
            $leads = $this->zohoInstance->leads->deleteMany($this->insertedId);

            $this->assertInstanceOf(Zoho\CRM\Api\Response::class, $leads);

            $rawData = json_decode($leads->getRawData());
            $this->assertEquals('5000', $rawData->response->result->code);
            $this->insertedId[] = [];
        }
    }
}