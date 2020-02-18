<?php
declare(strict_types=1);

namespace Salopot\ImageGenerator\ImageSources;

use Salopot\ImageGenerator\ImageProvider;
use Intervention\Image\Image;

/**
 * Source depended selector
 * use source functionality
 * for getting same image with different dimensions
 */
trait SourceSelectorTrait
{
    /** @var array */
    protected $selectorMap;

    /** @var ImageProvider */
    protected $imageProvider;

    /**
     * @param ImageProvider $imageProvider
     */
    public function __construct(ImageProvider $imageProvider)
    {
        $this->imageProvider = $imageProvider;
    }

    /**
     * Generate random source selector
     * @return mixed
     */
    abstract protected function getRandomSelector();

    /**
     * Return image by storing selector
     * @param int $width
     * @param int $height
     * @param mixed $selector
     * @return Image
     */
    abstract protected function getImageBySelector(int $width, int $height, $selector): Image;

    /**
     * Return random image from source
     * @param int $width
     * @param int $height
     * @return Image
     */
    abstract protected function getRandomImage(int $width, int $height): Image;

    /**
     * Get source selector by name
     * @param string $selectorName
     * @return mixed
     */
    protected function getSelectorByName(string $selectorName)
    {
        if (!isset($this->selectorMap[$selectorName])) {
            $this->selectorMap[$selectorName] = $this->getRandomSelector();
        }
        return $this->selectorMap[$selectorName];
    }

    /**
     * @inheritDoc
     */
    public function getImage(int $width, int $height, ?string $selectorName = null): Image
    {
        return $selectorName
            ? $this->getImageBySelector($width, $height, $this->getSelectorByName($selectorName))
            : $this->getRandomImage($width, $height);
    }
}
