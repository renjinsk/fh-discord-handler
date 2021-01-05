<?php

namespace RenjiNSK\DiscordHandlerBundle\Services;

use DiscordHandler\DiscordHandler;

/**
 * Class DiscordMonologHandlerService
 *
 * @package RenjiNSK\DiscordHandlerBundle\Services
 * @author  Kostas Rentzikas <krentzikas@ferryhopper.com>
 */
class DiscordMonologHandlerService extends DiscordHandler
{
    public function __construct(
        string $webhook,
        string $name
    ){

    }
}