<?php

namespace Olz\Entity\Common;

interface DataStorageInterface {
    public static function getEntityNameForStorage(): string;

    public function getEntityIdForStorage(): string;

    // Use DataStorageTrait to implement this!
    public function getStoredImageUploadIds(): array;

    // Use DataStorageTrait to implement this!
    public function getStoredFileUploadIds(): array;

    // Use DataStorageTrait to implement this!
    public function getImagesPathForStorage(): string;

    // Use DataStorageTrait to implement this!
    public function getFilesPathForStorage(): string;
}
