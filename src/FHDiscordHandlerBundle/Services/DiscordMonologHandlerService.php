<?php

namespace Renji\FHDiscordHandlerBundle\Services;

use DiscordHandler\DiscordHandler;

/**
 * Class DiscordMonologHandlerService
 *
 * @package Renji\FHDiscordHandlerBundle\Services
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