<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
	public function __construct(
		private $targetDirectory,
		private SluggerInterface $slugger,
	) {
	}

	public function upload(UploadedFile $file): string
	{
		$originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); // permet de récupérer le nom du fichier
		$safeFilename = hash('md5', $this->slugger->slug($originalFilename)); // permet de sécuriser le nom du fichier en le hashant avec md5 et en le slugifiant avec le slugger de symfony (pour éviter les caractères spéciaux)
		$fileName = '/uploads/' . $safeFilename.'-'.uniqid().'.'.$file->guessExtension(); // permet de créer un nom de fichier unique

		/*
		 * Déplace le fichier dans le dossier uploads avec le nom de fichier unique
		 */
		try {
			$file->move(
				$this->getTargetDirectory(),
				$fileName
			);
		} catch (FileException $e) {
			// ... handle exception if something happens during file upload
		}

		return $fileName;
	}

	public function getTargetDirectory(): string
	{
		return $this->targetDirectory;
	}
}