<?php

namespace Cocktales\Framework\Image;

use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageOptimizer
{
    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * ImageOptimizer constructor.
     * @param ImageManager $imageManager
     */
    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    /**
     * Creates an Image with dimensions 100 x 100
     *
     * @param UploadedFile $file
     * @return \Intervention\Image\Image
     */
    public function createThumbnail(UploadedFile $file): Image
    {
        return $this->imageManager->make($file)->resize(100, 125)->encode($file->getClientOriginalExtension(), 100);
    }

    /**
     * Creates an Image with dimensions 450 x 450
     *
     * @param UploadedFile $file
     * @return Image
     */
    public function createStandardSize(UploadedFile $file): Image
    {
        return $this->imageManager->make($file)->resize(375, 450)->encode($file->getClientOriginalExtension(), 10);
    }
}
