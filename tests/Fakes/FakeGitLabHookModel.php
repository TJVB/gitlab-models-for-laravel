<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes;

use Carbon\CarbonImmutable;
use TJVB\GitLabWebhooks\Contracts\Models\GitLabHookModel;

class FakeGitLabHookModel implements GitLabHookModel
{
    public bool $systemHook = false;
    public array $body = [];
    public string $eventType = 'dummy';
    public string $eventName = 'dummy';
    public string $objectKind = 'dummy';
    public ?CarbonImmutable $createdAt = null;
    public array $stored = [];
    public bool $isRemoved = false;
    public function isSystemHook(): bool
    {
        return $this->systemHook;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getObjectKind(): string
    {
        return $this->objectKind;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        if ($this->createdAt === null) {
            $this->createdAt = CarbonImmutable::now();
        }
        return $this->createdAt;
    }

    public function store(
        array $body,
        string $eventName,
        string $eventType,
        string $objectKind,
        bool $systemHook
    ): GitLabHookModel {
        $this->stored[] = [
            'body' => $body,
            'eventName' => $eventName,
            'eventType' => $eventType,
            'objectKind' => $objectKind,
            'systemHook' => $systemHook,
        ];
        return $this;
    }

    public function remove(): void
    {
        $this->isRemoved = true;
    }
}
