<?php
declare(strict_types=1);

namespace Salopot\ImageGenerator\ImageSources;

use Intervention\Image\Image;

interface ImageSourceInterface
{
    /**
     * Return image from resource
     * @param int $width
     * @param int $height
     * @param string|null $selectorName
     * @return Image
     */
    public function getImage(int $width, int $height, ?string $selectorName = null): Image;

    /**
     * Return background generator name
     * @return string
     */
    public function getName(): string;
}
