<?php

namespace RenjiNSK\DiscordHandlerBundle\Services;

use DiscordHandler\DiscordHandler;
use Symfony\Component\HttpKernel\Exception\{MethodNotAllowedHttpException, NotFoundHttpException};
use Symfony\Component\Routing\Exception\{MethodNotAllowedException, RouteNotFoundException};

/**
 * Class DiscordMonologHandlerService
 *
 * @package RenjiNSK\DiscordHandlerBundle\Services
 * @author  Kostas Rentzikas <krentzikas@ferryhopper.com>
 */
class DiscordMonologHandlerService extends DiscordHandler
{
    public const IGNORED_EXCEPTION_CLASSES = [
        MethodNotAllowedHttpException::class,
        MethodNotAllowedException::class,
        RouteNotFoundException::class,
        NotFoundHttpException::class,
    ];

    /** @var string */
    protected $name;
    /** @var string */
    protected $environment;

    /**
     * DiscordMonologHandlerService constructor.
     *
     * @param string $webhook
     * @param string $name
     * @param string $subName
     * @param int    $level
     * @param bool   $bubble
     */
    public function __construct(
        string $webhook,
        string $name,
        string $subName,
        int $level,
        bool $bubble
    ) {
        parent::__construct($webhook, $name, $subName, $level, $bubble);
        $this->name = $name;
        $this->config->setEmbedMode(true);
    }

    /**
     * @param string $environment
     *
     * @return DiscordMonologHandlerService
     * @noinspection PhpUnused
     */
    public function setEnvironment(string $environment): DiscordMonologHandlerService
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function handle(array $record): bool
    {
        if ($this->level > $record['level']) {
            return false;
        }

        /** @var \Throwable $exceptionInstance */
        $exceptionInstance = $record['context']['exception'] ?? new \Exception('[DiscordMonologHandler] No exception found');
        if (\in_array(\get_class($exceptionInstance), self::IGNORED_EXCEPTION_CLASSES, true)
        ) {
            return false;
        }

        return parent::handle($record);
    }

    /**
     * @inheritDoc
     */
    protected function write(array $record): void
    {
        $message = $record['message'];
        $fields  = [];
        if (\array_key_exists('context', $record)) {
            foreach ($record['context'] as $index => $item) {
                if (\is_object($item) || \is_array($item)) {
                    $item = \json_encode($item);
                }
                $message  = \str_replace('{'.$index.'}', $item, $message);
                $fields[] = [
                    'name'  => "{$index}:",
                    'value' => empty($item) ? "-{$item}-" : $item,
                ];
            }
        }
        $parts = [
            [
                'embeds' => [
                    [
                        'title'       => "{$record['level_name']} [{$this->name}][{$this->environment}]",
                        'description' => $message,
                        'timestamp'   => $record['datetime']->format($this->config->getDatetimeFormat()),
                        'color'       => $this->levelColors[$record['level']],
                        'fields'      => $fields,
                    ],
                ],
            ],
        ];

        foreach ($this->config->getWebHooks() as $webHook) {
            foreach ($parts as $part) {
                $this->send($webHook, $part);
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function send($webHook, $json)
    {
        try {
            parent::send($webHook, $json);
            \usleep(250000); // Sleep for 1/2 sec to avoid rate limit
        } catch (\Throwable $exception) {
            // There's a case where we get an error from discord.
            // So catch it, and do nothing
            // Causing not to report the error to Discord
        }
    }

}
