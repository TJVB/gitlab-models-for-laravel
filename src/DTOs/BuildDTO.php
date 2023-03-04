<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\DTOs;

use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;

final class BuildDTO
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        public readonly int $buildId,
        public readonly int $pipelineId,
        public readonly int $projectId,
        public readonly string $name,
        public readonly string $stage,
        public readonly string $status,
        public readonly bool $allowFailure,
        public readonly CarbonImmutable $createdAt,
        public readonly ?CarbonImmutable $startedAt = null,
        public readonly ?CarbonImmutable $finishedAt = null,
        public readonly ?float $duration = null,
    ) {
    }

    public static function fromBuildEventData(array $data): BuildDTO
    {
        return new BuildDTO(
            (int)Arr::get($data, 'build_id'),
            (int)Arr::get($data, 'pipeline_id'),
            (int)Arr::get($data, 'project_id'),
            (string) Arr::get($data, 'build_name'),
            (string) Arr::get($data, 'build_stage'),
            (string) Arr::get($data, 'build_status'),
            (bool) Arr::get($data, 'build_allow_failure'),
            CarbonImmutable::make(Arr::get($data, 'build_created_at')),
            CarbonImmutable::make(Arr::get($data, 'build_started_at')),
            CarbonImmutable::make(Arr::get($data, 'build_finished_at')),
            Arr::get($data, 'build_duration'),
        );
    }

    public static function fromPipelineEventData(array $data, int $pipelineId, int $projectId): BuildDTO
    {
        return new BuildDTO(
            (int)Arr::get($data, 'id'),
            $pipelineId,
            $projectId,
            (string) Arr::get($data, 'name'),
            (string) Arr::get($data, 'stage'),
            (string) Arr::get($data, 'status'),
            (bool) Arr::get($data, 'allow_failure'),
            CarbonImmutable::make(Arr::get($data, 'created_at')),
            CarbonImmutable::make(Arr::get($data, 'started_at')),
            CarbonImmutable::make(Arr::get($data, 'finished_at')),
        );
    }
}
