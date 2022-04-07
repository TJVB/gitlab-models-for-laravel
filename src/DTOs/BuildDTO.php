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
        public int $buildId,
        public int $pipelineId,
        public int $projectId,
        public string $name,
        public string $stage,
        public string $status,
        public bool $allowFailure,
        public CarbonImmutable $createdAt,
        public ?CarbonImmutable $startedAt = null,
        public ?CarbonImmutable $finishedAt = null,
        public ?float $duration = null,
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

    public static function fromPipelineEventData(array $data): BuildDTO
    {
        return new BuildDTO(
            (int)Arr::get($data, 'id'),
            (int)Arr::get($data, 'pipeline_id'),
            (int)Arr::get($data, 'project_id'),
            Arr::get($data, 'name'),
            Arr::get($data, 'stage'),
            Arr::get($data, 'status'),
            (bool) Arr::get($data, 'allow_failure'),
            CarbonImmutable::make(Arr::get($data, 'created_at')),
            CarbonImmutable::make(Arr::get($data, 'started_at')),
            CarbonImmutable::make(Arr::get($data, 'finished_at')),
        );
    }
}
