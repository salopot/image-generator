<?php
declare(strict_types=1);

namespace Salopot\ImageGenerator\ImageSources\Local;

use Salopot\ImageGenerator\ImageProvider;
use Salopot\ImageGenerator\ImageSources\ImageSourceInterface;
use Salopot\ImageGenerator\ImageSources\NamedTrait;
use Salopot\ImageGenerator\ImageSources\SourceSelectorTrait;
use Intervention\Image\Image;

class SolidColorSource implements ImageSourceInterface
{
    use SourceSelectorTrait,
        NamedTrait;

    public const NAME = 'SolidColor';

    /**
     * SolidFillSource constructor.
     * @param ImageProvider $imageProvider
     */
    public function __construct(ImageProvider $imageProvider)
    {
        $this->imageProvider = $imageProvider;
    }

    protected function getRandomSelector()
    {
        return $this->imageProvider->getGenerator()->hexColor;
    }

    protected function getImageBySelector(int $width, int $height, $selector): Image
    {
        return $this->imageProvider->getImageManager()->canvas($width, $height, $selector);
    }

    public function getRandomImage(int $width, int $height): Image
    {
        return $this->getImageBySelector($width, $height, $this->getRandomSelector());
    }
}
