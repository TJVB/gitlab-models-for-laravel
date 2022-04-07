<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests;

trait TrueFalseProvider
{
    public function trueFalseProvider(): array
    {
        return [
            'enabled' => [true],
            'false' => [false],
        ];
    }
}
