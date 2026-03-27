<?php

namespace MailCraft\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MailCraft\MailCraftClient;
use MailCraft\MailCraftException;
use PHPUnit\Framework\TestCase;

class MailCraftClientTest extends TestCase
{
    private function createClientWithMock(array $responses): MailCraftClient
    {
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handler]);

        $client = new MailCraftClient('mc_test_key');

        // Use reflection to inject mock HTTP client
        $ref = new \ReflectionClass($client);
        $prop = $ref->getProperty('http');
        $prop->setAccessible(true);
        $prop->setValue($client, $httpClient);

        return $client;
    }

    public function test_throws_if_api_key_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new MailCraftClient('');
    }

    public function test_sends_email_successfully(): void
    {
        $client = $this->createClientWithMock([
            new Response(201, [], json_encode(['id' => 'log_123', 'status' => 'sent'])),
        ]);

        $result = $client->send([
            'type' => 'welcome',
            'to' => 'john@example.com',
            'data' => ['name' => 'John'],
        ]);

        $this->assertEquals('log_123', $result['id']);
        $this->assertEquals('sent', $result['status']);
    }

    public function test_sends_with_prompt_and_actions(): void
    {
        $client = $this->createClientWithMock([
            new Response(201, [], json_encode(['id' => 'log_456', 'status' => 'sent'])),
        ]);

        $result = $client->send([
            'type' => 'welcome',
            'to' => 'john@example.com',
            'data' => ['name' => 'John'],
            'prompt' => 'Be formal',
            'actions' => [['label' => 'Click', 'url' => 'https://example.com']],
        ]);

        $this->assertEquals('sent', $result['status']);
    }

    public function test_throws_on_api_error(): void
    {
        $client = $this->createClientWithMock([
            new Response(401, [], json_encode(['message' => 'Invalid API key'])),
        ]);

        $this->expectException(MailCraftException::class);
        $client->send(['type' => 'test', 'to' => 'test@test.com']);
    }

    public function test_throws_if_type_is_missing(): void
    {
        $client = new MailCraftClient('mc_test');
        $this->expectException(\InvalidArgumentException::class);
        $client->send(['type' => '', 'to' => 'test@test.com']);
    }
}
