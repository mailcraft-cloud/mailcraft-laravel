<?php

namespace MailCraft;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class MailCraftClient
{
    private Client $http;
    private string $apiKey;
    private string $baseUrl;

    public function __construct(string $apiKey, string $baseUrl = 'https://api.mailcraft.cloud')
    {
        if (empty($apiKey)) {
            throw new \InvalidArgumentException('MailCraft: apiKey is required');
        }

        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->http = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
        ]);
    }

    /**
     * Send a transactional email.
     *
     * @param array{
     *   type: string,
     *   to: string,
     *   data?: array<string, mixed>,
     *   prompt?: string,
     *   actions?: array<array{label: string, url: string, style?: string}>
     * } $options
     * @return array{id: string, status: string}
     * @throws MailCraftException
     */
    public function send(array $options): array
    {
        if (empty($options['type'])) {
            throw new \InvalidArgumentException('MailCraft: type is required');
        }
        if (empty($options['to'])) {
            throw new \InvalidArgumentException('MailCraft: to is required');
        }

        try {
            $response = $this->http->post('/v1/send', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ],
                'json' => array_filter([
                    'type' => $options['type'],
                    'to' => $options['to'],
                    'data' => $options['data'] ?? null,
                    'prompt' => $options['prompt'] ?? null,
                    'actions' => $options['actions'] ?? null,
                ], fn($v) => $v !== null),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $body = null;
            $statusCode = 0;
            $message = $e->getMessage();

            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $body = json_decode($e->getResponse()->getBody()->getContents(), true);
                $message = $body['message'] ?? $message;
            }

            throw new MailCraftException($message, $statusCode, $e, $body);
        }
    }
}
