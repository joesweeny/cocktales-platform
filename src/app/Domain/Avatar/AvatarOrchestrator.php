<?php

namespace Cocktales\Domain\Avatar;

use Cocktales\Domain\Avatar\Entity\Avatar;
use Cocktales\Domain\Avatar\Persistence\Repository;
use Cocktales\Framework\Image\ImageOptimizer;
use Cocktales\Framework\Uuid\Uuid;
use Intervention\Image\Image;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AvatarOrchestrator
{
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var ImageOptimizer
     */
    private $optimizer;
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * AvatarOrchestrator constructor.
     * @param Repository $repository
     * @param ImageOptimizer $optimizer
     * @param Filesystem $filesystem
     */
    public function __construct(Repository $repository, ImageOptimizer $optimizer, Filesystem $filesystem)
    {
        $this->repository = $repository;
        $this->optimizer = $optimizer;
        $this->filesystem = $filesystem;
    }

    /**
     * @param Avatar $avatar
     * @return Avatar
     * @throws \Cocktales\Domain\Avatar\Exception\AvatarRepositoryException
     */
    public function createAvatar(Avatar $avatar): Avatar
    {
        return $this->repository->createAvatar($avatar);
    }

    /**
     * @param Uuid $userId
     * @return Avatar
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function getAvatarByUserId(Uuid $userId): Avatar
    {
        return $this->repository->getAvatarByUserId($userId);
    }

    /**
     * @param Uuid $userId
     * @param callable $updater
     * @return Avatar
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function updateAvatar(Uuid $userId, callable $updater): Avatar
    {
        return $this->repository->updateAvatar($userId, $updater);
    }

    /**
     * @param UploadedFile $file
     * @param string $path
     * @return void
     */
    public function saveThumbnailToStorage(UploadedFile $file, string $path): void
    {
        $this->filesystem->put($path, $this->createThumbnail($file)->stream()->__toString());
    }

    /**
     * @param UploadedFile $file
     * @param string $path
     * @return void
     */
    public function saveStandardSizeToStorage(UploadedFile $file, string $path): void
    {
        $this->filesystem->put($path, $this->createStandardSize($file)->stream()->__toString());
    }

    /**
     * @param UploadedFile $file
     * @return \Intervention\Image\Image
     */
    private function createThumbnail(UploadedFile $file): Image
    {
        return $this->optimizer->createThumbnail($file);
    }

    /**
     * @param UploadedFile $file
     * @return Image
     */
    private function createStandardSize(UploadedFile $file): Image
    {
        return $this->optimizer->createStandardSize($file);
    }
}
