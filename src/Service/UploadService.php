<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadService
{
    const IMG_DIR = '/public/images/user_photo/';
    const ARTICLE_DIR = '/public/images/article_images/';
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file, $user_id)
    {
        $fileName = $user_id . '-photo.'.$file->guessExtension();
        $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    public function loadArticlePhoto(UploadedFile $file)
    {
        $fileName = $this->getRandomArtName() . '-article.'.$file->guessExtension();
        $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    public function getRandomArtName()
    {
        $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $strlength = strlen($characters);
        $random = '';

        for ($i = 0; $i < 5; $i++) {
            $random .= $characters[rand(0, $strlength - 1)];
        }

        return $random;
    }


}