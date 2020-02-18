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

class UnsplashSource implements ImageSourceInterface
{
    use SourceSelectorTrait;

    public const NAME = 'Unsplash';

    /**
     * @param ImageProvider $imageProvider
     */
    public function __construct(ImageProvider $imageProvider)
    {
        $this->imageProvider = $imageProvider;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    protected function getRandomImage(int $width, int $height): Image
    {
        $random = random_int(11111, 99999);
        $url = "https://source.unsplash.com/random/{$width}x{$height}?sig={$random}";
        return $this->imageProvider->getImageManager()->make($url);
    }

    protected function getRandomSelector()
    {
        //TODO: change to get by {PHOTO ID}
        $url = 'https://source.unsplash.com/random/?sig=' . random_int(1111, 9999);
        foreach (get_headers($url) as $header) {
            if (strpos($header, 'Location: ') === 0) {
                $redirect = trim(substr($header, 10));
                break;
            }
        }
        $parts = parse_url($redirect);
        parse_str($parts['query'], $query);
        unset($query['w'], $query['h']);
        $queryString = http_build_query($query);
        return "{$parts['scheme']}://{$parts['host']}{$parts['path']}?{$queryString}";
    }

    protected function getImageBySelector(int $width, int $height, $selector): Image
    {
        $url = "{$selector}&w={$width}&h={$height}";
        return $this->imageProvider->getImageManager()->make($url);
    }
}
