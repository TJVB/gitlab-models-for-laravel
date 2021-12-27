<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Exceptions;

class DataNotFound extends GitLabModelsException
{
    public static function notFoundForModelAndId(string $model, int|string $id): DataNotFound
    {
        return new self('We did not found any result for ' . $model . ' with id ' . $id);
    }
}
