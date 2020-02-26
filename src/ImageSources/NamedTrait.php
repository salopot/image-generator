<?php
declare(strict_types=1);

namespace Salopot\ImageGenerator\ImageSources;

trait NamedTrait
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return static::NAME;
    }
}
