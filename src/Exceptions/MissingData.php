<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Exceptions;

final class MissingData extends GitLabModelsException
{
    public static function missingDataForAction(string $field, string $action): MissingData
    {
        return new self('We miss a value for ' . $field . ' for ' . $action);
    }
}
