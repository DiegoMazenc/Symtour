<?php

namespace App\Service;

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\S3\Exception\S3Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AddPhotosService
{
    public function __construct(
        private EntityManagerInterface $em,
        private SluggerInterface $slugger
    ) {
    }
    public function addNewPicture($img, $entity)
    {

        $originalName = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
        $nameSlug = $this->slugger->slug($originalName);
        $fileName = $nameSlug . '-' . uniqid() . '.' . $img->guessExtension();
        try {
            $sha256 = hash_file('sha256', $img->getRealPath());
            $credentials = new Credentials('AKIAZQ3DNS574T63MPNV', '2yTo3/9lJ7YFOCb5gAbQ7yGE2m08iy5XyyKy6Ur4');

            $s3 = new S3Client([
                'version' => 'latest',
                'region' => 'eu-west-3',
                'credentials' => $credentials
            ]);

            $s3->putObject([
                'Bucket' => 'symtour',
                'Key' => $fileName,
                'Body' => $img,
                'SourceFile' => $img->getRealPath(),
                'ContentType' => $img->getMimeType(),
                'ContentSHA256' => $sha256,
                'ACL' => 'public-read',
            ]);
            // dd($s3->getObjectUrl('symtour', $fileName));
        } catch (S3Exception $e) {
            dd($e->getMessage());

        }

        if ($entity instanceof \App\Entity\Profil) {
            $entity->setPicture($s3->getObjectUrl('symtour', $fileName));
        } else {
            $entity->setLogo($s3->getObjectUrl('symtour', $fileName));
        }

    }
}