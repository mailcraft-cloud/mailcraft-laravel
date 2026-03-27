<?php

namespace MailCraft\Facades;

use Illuminate\Support\Facades\Facade;
use MailCraft\MailCraftClient;

/**
 * @method static array send(array $options)
 * @method static \MailCraft\MailBuilder create(string $type)
 *
 * @see MailCraftClient
 */
class MailCraft extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MailCraftClient::class;
    }
}
