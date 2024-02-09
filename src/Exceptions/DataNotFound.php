<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Exceptions;

final class DataNotFound extends GitLabModelsException
{
    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public static function notFoundForModelAndId(string $model, int|string $id): DataNotFound
    {
        return new self('We did not found any result for ' . $model . ' with id ' . $id);
    }
}
