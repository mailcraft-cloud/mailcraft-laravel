# mailcraft-laravel

Official Laravel SDK for [MailCraft](https://github.com/mailcraft-cloud/mailcraft) — open source transactional email with AI-generated content.

## Installation

```bash
composer require mailcraft/mailcraft-laravel
```

Laravel auto-discovers the service provider and facade.

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=mailcraft-config
```

Add your API key to `.env`:

```env
MAILCRAFT_API_KEY=mc_your_api_key_here
```

If self-hosting:

```env
MAILCRAFT_BASE_URL=https://your-mailcraft-instance.com
```

## Usage

### Fluent API (recommended)

```php
use MailCraft\Facades\MailCraft;

MailCraft::create('welcome')
    ->to('john@example.com')
    ->data(['name' => 'John', 'plan' => 'Pro'])
    ->prompt('Mention dedicated support')
    ->action('Get Started', 'https://app.example.com')
    ->action('View Docs', 'https://docs.example.com', 'secondary')
    ->send();
```

### Classic API

```php
$result = MailCraft::send([
    'type'    => 'welcome',
    'to'      => 'john@example.com',
    'data'    => ['name' => 'John', 'plan' => 'Pro'],
    'prompt'  => 'Mention dedicated support',
    'actions' => [
        ['label' => 'Get Started', 'url' => 'https://app.example.com'],
    ],
]);
```

### With Dependency Injection

```php
use MailCraft\MailCraftClient;

class WelcomeController extends Controller
{
    public function __construct(private MailCraftClient $mail) {}

    public function send(): void
    {
        $this->mail->create('welcome')
            ->to('john@example.com')
            ->data(['name' => 'John'])
            ->send();
    }
}
```

### Without Laravel (standalone)

```php
use MailCraft\MailCraftClient;

$client = new MailCraftClient('mc_your_api_key');

$client->create('welcome')
    ->to('john@example.com')
    ->data(['name' => 'John'])
    ->send();
```

## Fluent Builder Methods

| Method | Description |
|--------|-------------|
| `->to($email)` | Set recipient email |
| `->data($array)` | Set dynamic template data |
| `->prompt($string)` | Steer AI content generation |
| `->action($label, $url, $style?)` | Add a CTA button (call multiple times) |
| `->actions($array)` | Set all actions at once |
| `->send()` | Send the email, returns `['id' => ..., 'status' => ...]` |

## Options (Classic API)

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `type` | `string` | Yes | Email type: `'welcome'`, `'invoice'`, `'password-reset'`, etc. |
| `to` | `string` | Yes | Recipient email address |
| `data` | `array` | No | Template variables |
| `prompt` | `string` | No | AI content instructions |
| `actions` | `array` | No | CTA buttons: `[['label' => ..., 'url' => ..., 'style' => ...]]` |

## Response

```php
['id' => 'log_abc123', 'status' => 'sent']
// status: "sent" | "failed" | "fallback"
```

## Error Handling

```php
use MailCraft\Facades\MailCraft;
use MailCraft\MailCraftException;

try {
    MailCraft::create('welcome')
        ->to('john@example.com')
        ->send();
} catch (MailCraftException $e) {
    $e->getStatusCode(); // 401, 400, 502, etc.
    $e->getMessage();    // "Invalid API key"
    $e->getBody();       // Parsed response body
}
```

## Self-Hosted

```env
MAILCRAFT_BASE_URL=https://mail.internal.example.com
```

Or pass directly:

```php
$client = new MailCraftClient('mc_your_key', 'https://mail.internal.example.com');
```

## License

MIT
