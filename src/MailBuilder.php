<?php

namespace MailCraft;

/**
 * Fluent email builder — chainable API for constructing and sending emails.
 *
 * Usage:
 *   MailCraft::create('welcome')
 *     ->to('john@example.com')
 *     ->data(['name' => 'John', 'plan' => 'Pro'])
 *     ->prompt('Mention dedicated support')
 *     ->action('Get Started', 'https://app.example.com')
 *     ->send();
 */
class MailBuilder
{
    private MailCraftClient $client;
    private string $type;
    private string $to = '';
    private ?array $data = null;
    private ?string $prompt = null;
    private ?array $actions = null;

    public function __construct(MailCraftClient $client, string $type)
    {
        $this->client = $client;
        $this->type = $type;
    }

    public function to(string $email): static
    {
        $this->to = $email;
        return $this;
    }

    public function data(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function prompt(string $prompt): static
    {
        $this->prompt = $prompt;
        return $this;
    }

    public function action(string $label, string $url, ?string $style = null): static
    {
        if ($this->actions === null) {
            $this->actions = [];
        }
        $action = ['label' => $label, 'url' => $url];
        if ($style !== null) {
            $action['style'] = $style;
        }
        $this->actions[] = $action;
        return $this;
    }

    public function actions(array $actions): static
    {
        $this->actions = $actions;
        return $this;
    }

    public function send(): array
    {
        return $this->client->send(array_filter([
            'type' => $this->type,
            'to' => $this->to,
            'data' => $this->data,
            'prompt' => $this->prompt,
            'actions' => $this->actions,
        ], fn($v) => $v !== null && $v !== ''));
    }
}
