<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\DTOs;

use Carbon\CarbonImmutable;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Label;

final class LabelDTO
{
    public function __construct(
        public readonly int $labelId,
        public readonly string $title,
        public readonly string $color,
        public readonly ?int $projectId,
        public readonly CarbonImmutable $createdAt,
        public readonly CarbonImmutable $updatedAt,
        public readonly ?string $description,
        public readonly string $type,
        public readonly ?int $groupId,
    ) {
    }

    public static function fromLabel(Label $label): LabelDTO
    {
        return new self(
            $label->getLabelId(),
            $label->getTitle(),
            $label->getColor(),
            $label->getProjectId(),
            $label->getCreatedAt(),
            $label->getUpdatedAt(),
            $label->getDescription(),
            $label->getType(),
            $label->getGroupId(),
        );
    }
}
