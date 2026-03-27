<?php

namespace MailCraft;

class MailCraftException extends \RuntimeException
{
    private ?array $body;

    public function __construct(string $message, int $statusCode = 0, ?\Throwable $previous = null, ?array $body = null)
    {
        parent::__construct($message, $statusCode, $previous);
        $this->body = $body;
    }

    public function getStatusCode(): int
    {
        return $this->getCode();
    }

    public function getBody(): ?array
    {
        return $this->body;
    }
}
