# Image generator
Image provider for [fzaninotto/Faker](https://github.com/fzaninotto/Faker) package with support multiple image sources and basic image manipulations

Local (fast & no need internet connection):
- **SolidColor** - generate image filled single color 
- **Gallery** - use local directory with images as source
- **Gradient** - generate gradient image

Remote:
- **LoremPixel** - [lorempixel.com](https://lorempixel.com) used in the original faker (very unstable now)  
- **PicsumPhotos** - [picsum.photos](https://picsum.photos)
- **Unsplash** - [unsplash.com](https://source.unsplash.com)
- **LoremFlickr** - [loremflickr.com](https://loremflickr.com)
- **PlaceKitten** - [placekitten.com](http://placekitten.com)
- **PlaceImg** - [placeimg.com](https://placeimg.com) Warning: selector param use image resizing

## Description
- Support multiple local & remote sources (also you can write own)
- Support basic image manipulations
- Support several result formats (data-url, file, content)
- Support multiple extensions

## Requirements
- PHP >=7.1
- GD Library (>=2.0)
- Imagick PHP extension (>=6.5.7)

## Installation
```
composer require --dev salopot/image-generator
```

## Configuration

```php
$generator = \Faker\Factory::create();
$imageProvider = new \Salopot\ImageGenerator\ImageProvider($generator);
// Configure some or all image sources
$imageProvider->addImageSource(new \Salopot\ImageGenerator\ImageSources\Local\SolidColorSource($imageProvider));
$imageProvider->addImageSource(new \Salopot\ImageGenerator\ImageSources\Local\GallerySource($imageProvider, '/dir/with/images'));
$imageProvider->addImageSource(new \Salopot\ImageGenerator\ImageSources\Local\SolidColorSource($imageProvider));
$imageProvider->addImageSource(new \Salopot\ImageGenerator\ImageSources\Remote\LoremPixelSource($imageProvider));
$imageProvider->addImageSource(new \Salopot\ImageGenerator\ImageSources\Remote\PicsumPhotosSource($imageProvider));
$imageProvider->addImageSource(new \Salopot\ImageGenerator\ImageSources\Remote\UnsplashSource($imageProvider));
$imageProvider->addImageSource(new \Salopot\ImageGenerator\ImageSources\Remote\PlaceKittenSource($imageProvider));
$imageProvider->addImageSource(new \Salopot\ImageGenerator\ImageSources\Remote\PlaceImgSource($imageProvider));
$generator->addProvider($imageProvider);
```

## Usage

Basic example
```php
$url = $generator->imageGenerator(640, 480)->getDataUrl();
```
Group random images for get the same with different changes
```php
$item1MainImage = $generator->imageGenerator(640, 480, 'item-1')->getDataUrl();
$item1ThumbnailImage = $generator->imageGenerator(50, 50, 'item-1')->getDataUrl();

$item2MainImage = $generator->imageGenerator(640, 480, 'item-2')->getDataUrl();
$item2ThumbnailImage = $generator->imageGenerator(50, 50, 'item-2')->getDataUrl();
```

Choose one of the available sources.
```php
$oneSourceUrl = $generator->imageGenerator(640, 480, null, 'SolidColor')
    ->grayscale()->getDataUrl();
$anotherSourceUrl = $generator->imageGenerator(640, 480, null, 'Unsplash')
    ->negative()->getDataUrl();
```

Support same image manipulations from any source:
```php
$filePath = $generator->imageGenerator(640, 480)
    ->setExtension('png')
    ->grayscale()
    ->opacity(95)
    ->contrast(30)
    ->gamma(1.6)
    ->brightness(15)
    ->blur(80)
    ->negative()
    ->insertImage('/path/to/logo.png','left', 'bottom')
    ->text('Hello world', 35, 'left', 'top', 30, -90)
    ->insertImage('/path/to/logo.png', 'right', 'top')
->getFilePath('/path/to/dir');
```

Support several output formats:
```php
$dataUrl = $generator->imageGenerator(640, 480)->getDataUrl();
$filePath = $generator->imageGenerator(640, 480)->getFilePath('/path/to/dir');
$content = $generator->imageGenerator(640, 480)->getContent();
``` 