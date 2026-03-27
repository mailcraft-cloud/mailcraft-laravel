<?php

namespace MailCraft\Facades;

use Illuminate\Support\Facades\Facade;
use MailCraft\MailCraftClient;

/**
 * @method static array send(array $options)
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
