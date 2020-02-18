<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Salopot\ImageGenerator\ImageSources\Remote;

use Salopot\ImageGenerator\ImageProvider;
use Salopot\ImageGenerator\ImageSources\ImageSourceInterface;
use Salopot\ImageGenerator\ImageSources\SourceSelectorTrait;
use Intervention\Image\Image;

class LoremPixelSource implements ImageSourceInterface
{
    use SourceSelectorTrait;

    public const NAME = 'LoremPixel';

    protected $allowedCategories = [
        'abstract', 'animals', 'business', 'cats', 'city', 'food', 'nightlife',
        'fashion', 'people', 'nature', 'sports', 'technics', 'transport'
    ];

    /**
     * LoremPixelSource constructor.
     * @param ImageProvider $imageProvider
     */
    public function __construct(ImageProvider $imageProvider)
    {
        $this->imageProvider = $imageProvider;
    }

    public function getName(): string
    {
        return static::NAME;
    }

    protected function getRandomSelector()
    {
        return [
            'category' => $this->imageProvider->getGenerator()->randomElement($this->allowedCategories),
            'id' => mt_rand(1, 10),
        ];
    }

    protected function getImageBySelector(int $width, int $height, $selector): Image
    {
        $url = "https://lorempixel.com/{$width}/{$height}/{$selector['category']}/{$selector['id']}/";
        return $this->imageProvider->getImageManager()->make($url);
    }

    protected function getRandomImage(int $width, int $height): Image
    {
        $url = "https://lorempixel.com/{$width}/{$height}/";
        return $this->imageProvider->getImageManager()->make($url);
    }
}
