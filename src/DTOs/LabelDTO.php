<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\DTOs;

use Carbon\CarbonImmutable;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Label;

final class LabelDTO
{
    public function __construct(
        public int $labelId,
        public string $title,
        public string $color,
        public ?int $projectId,
        public CarbonImmutable $createdAt,
        public CarbonImmutable $updatedAt,
        public ?string $description,
        public string $type,
        public ?int $groupId,
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
