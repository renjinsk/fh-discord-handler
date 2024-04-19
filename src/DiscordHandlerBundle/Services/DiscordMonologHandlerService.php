<?php

namespace RenjiNSK\DiscordHandlerBundle\Services;

use DiscordHandler\DiscordHandler;
use Symfony\Component\HttpKernel\Exception\{MethodNotAllowedHttpException, NotFoundHttpException};
use Symfony\Component\Routing\Exception\{MethodNotAllowedException, RouteNotFoundException};
use Monolog\{Level, LogRecord};

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

    protected string $name;
    protected string $environment;

    public function __construct(
        string|array $webhook,
        string $name,
        string $subName,
        int|Level $level,
        bool $bubble
    ) {
        if ( ! $level instanceof Level) {
            $level = Level::fromValue($level);
        }
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
    public function handle(LogRecord $record): bool
    {
        if ($this->level > $record->level) {
            return false;
        }

        /** @var \Throwable $exceptionInstance */
        $exceptionInstance = $record->context['exception'] ??
            new \Exception('[DiscordMonologHandler] No exception found');
        if (\in_array($exceptionInstance::class, self::IGNORED_EXCEPTION_CLASSES, true)
        ) {
            return false;
        }

        return parent::handle($record);
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        $message = $record->message;
        $fields  = [];
        foreach ($record->context ?? [] as $index => $item) {
            if (\is_object($item) || \is_array($item)) {
                $item = \json_encode($item);
            }
            $message  = \str_replace('{'.$index.'}', $item, $message);
            $fields[] = [
                'name'  => "{$index}:",
                'value' => empty($item) ? "-{$item}-" : $item,
            ];
        }
        $parts = [
            [
                'embeds' => [
                    [
                        'title'       => "{$record['level_name']} [{$this->name}][{$this->environment}]",
                        'description' => $message,
                        'timestamp'   => $record['datetime']->format($this->config->getDatetimeFormat()),
                        'color'       => $this->getColorForLevel($record->level),
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
    protected function send(string $webHook, array $json): void
    {
        try {
            parent::send($webHook, $json);
            \usleep(500000); // Sleep for 1/2 sec to avoid rate limit
        } catch (\Throwable) {
            // There's a case where we get an error from discord.
            // So catch it, and do nothing
            // Causing not to report the error to Discord
        }
    }

}
