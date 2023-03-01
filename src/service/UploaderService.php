<?php

namespace App\service;

use symfony\component\HttpFoundation\File\Exception\FileException;
use symfony\component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploaderService
{
    public function __construct( private SluggerInterface $slugger){
       
    }
    public function uploadFile(
         UploadedFile $file,
         string $directoryFolder
    ){
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        // Move the file to the directory where brochures are stored
        try {
            $file->move(
                $directoryFolder,
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        // updates the 'brochureFilename' property to store the PDF file name
        // instead of its contents

        return $newFilename;
     }
}
