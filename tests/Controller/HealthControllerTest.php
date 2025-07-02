<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HealthControllerTest extends WebTestCase
{
    public function testHealthEndpoint(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('checks', $content);
        $this->assertArrayHasKey('timestamp', $content);
    }

    public function testHealthSimpleEndpoint(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health/simple');

        $this->assertResponseIsSuccessful();
        
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('healthy', $content['status']);
    }
} 