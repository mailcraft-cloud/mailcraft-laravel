# mailcraft-laravel

Official Laravel SDK for [MailCraft](https://github.com/mailcraft-cloud/mailcraft-laravel) — transactional email with AI-generated content.

## Installation

```bash
composer require mailcraft/mailcraft-laravel
```

Laravel auto-discovers the service provider and facade via package discovery.

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=mailcraft-config
```

Add your API key to `.env`:

```env
MAILCRAFT_API_KEY=mc_your_api_key_here
```

If you are self-hosting MailCraft, also set:

```env
MAILCRAFT_BASE_URL=https://your-mailcraft-instance.com
```

## Usage

### With the Facade

```php
use MailCraft\Facades\MailCraft;

$result = MailCraft::send([
    'type'    => 'welcome',
    'to'      => 'john@example.com',
    'data'    => ['name' => 'John', 'plan' => 'Pro'],
    'prompt'  => 'Mention dedicated support and onboarding.',
    'actions' => [
        ['label' => 'Get Started', 'url' => 'https://app.example.com/onboard'],
    ],
]);

// $result['id']     — e.g. "log_abc123"
// $result['status'] — "sent" | "failed" | "fallback"
```

### With Dependency Injection

```php
use MailCraft\MailCraftClient;

class WelcomeController extends Controller
{
    public function __construct(private MailCraftClient $mailcraft) {}

    public function send(): void
    {
        $this->mailcraft->send([
            'type' => 'welcome',
            'to'   => 'john@example.com',
            'data' => ['name' => 'John'],
        ]);
    }
}
```

### Without Laravel (standalone)

```php
use MailCraft\MailCraftClient;

$client = new MailCraftClient('mc_your_api_key_here');

$result = $client->send([
    'type' => 'welcome',
    'to'   => 'john@example.com',
    'data' => ['name' => 'John', 'plan' => 'Pro'],
]);
```

## Options

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `type` | `string` | Yes | Email template type (e.g. `welcome`, `password-reset`, `invoice`) |
| `to` | `string` | Yes | Recipient email address |
| `data` | `array` | No | Template variables merged into the email content |
| `prompt` | `string` | No | Natural-language instruction for the AI (e.g. "mention the free trial") |
| `actions` | `array` | No | Call-to-action buttons (see below) |

### `actions` format

Each action is an associative array:

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `label` | `string` | Yes | Button text |
| `url` | `string` | Yes | Button destination URL |
| `style` | `string` | No | Visual style hint (e.g. `primary`, `secondary`) |

## Response

```php
[
    'id'     => 'log_abc123',   // Unique send log ID
    'status' => 'sent',         // "sent" | "failed" | "fallback"
]
```

## Error Handling

```php
use MailCraft\Facades\MailCraft;
use MailCraft\MailCraftException;

try {
    $result = MailCraft::send([
        'type' => 'welcome',
        'to'   => 'john@example.com',
    ]);
} catch (MailCraftException $e) {
    // HTTP status code (e.g. 401, 422, 500)
    $statusCode = $e->getStatusCode();

    // Parsed response body from the API (or null)
    $body = $e->getBody();

    // Human-readable message
    $message = $e->getMessage();

    logger()->error('MailCraft send failed', [
        'status'  => $statusCode,
        'message' => $message,
        'body'    => $body,
    ]);
} catch (\InvalidArgumentException $e) {
    // Missing required fields (type or to)
    logger()->error($e->getMessage());
}
```

## Self-Hosted Configuration

If you run MailCraft on your own infrastructure, override the base URL:

```env
MAILCRAFT_BASE_URL=https://mail.internal.example.com
```

Or pass it directly when instantiating the client:

```php
$client = new MailCraftClient('mc_your_key', 'https://mail.internal.example.com');
```

## Testing

```bash
composer install
vendor/bin/phpunit
```

## License

MIT — see [LICENSE](LICENSE).
