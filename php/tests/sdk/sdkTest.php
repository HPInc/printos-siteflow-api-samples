<?php

use PHPUnit\Framework\TestCase;

final class OneflowSDKTest extends TestCase
{
    public function testCanCreateSdkInstance()
	{
        $client = new OneflowSDK(
            'http://localhost:3000/api',
            'token',
            'secret'
        );

        $this->assertNotEmpty($client);
    }

    public function testFailToCreateSdkInstanceWithoutParameters()
	{
        $this->expectException(Exception::class);
        $client = new OneflowSDK('', '', '');
    }

    public function testCanCreateSdkInstanceWithCustomRetry()
	{       
        $client = new OneflowSDK(
            'http://localhost:3000/api',
            'token',
            'secret',
            (object)['retries' => 1, 'retryDelay' => function(){}, 'retryCondition' => function(){}]
        );

        $this->assertNotEmpty($client);
    }

    public function testFailToRequestWithInvalidUrl()
	{
        $this->expectException(Exception::class);
        $client = new OneflowSDK('http://localhost:3000/api', 'token', 'secret');
        $client->request('GET', '/wrongPath');
    }

    public function testFailToRequestWithNonUrl()
	{
        $client = new OneflowSDK(
            'apiPath',
            'token',
            'secret'
        );

        $response = $client->request('GET', '/wrongPath');
        $this->assertEquals(false, $response);
    }

    public function testDefaultRetryFunctionsAreCalledOnError()
	{
        $client = new OneflowSDK(
            'https://pro-api.oneflowcloud.com/api',
            'token',
            'secret'
        );

        $response = $client->request('GET', '/wrongPath');
        $this->assertContains('{"message":"Token Not Found"}', $response);
    }

    public function testCustomRetryFunctionsAreCalledOnError()
	{       
        $retryDelay = $this->getMockBuilder(\stdclass::class)->setMethods(['retryDelay'])->getMock();
        $retryDelay->expects($this->once())->method('retryDelay');
        $retryCondition = $this->getMockBuilder(\stdclass::class)->setMethods(['retryCondition'])->getMock();
        $retryCondition->expects($this->once())->method('retryCondition')->will($this->returnValue(true));
        
        $client = new OneflowSDK(
            'https://pro-api.oneflowcloud.com/api',
            'token',
            'secret',
            (object)['retries' => 1, 'retryDelay' => array($retryDelay, 'retryDelay'), 'retryCondition' =>  array($retryCondition, 'retryCondition')]
        );

        $response = $client->request('GET', '/wrongPath');
    }
}