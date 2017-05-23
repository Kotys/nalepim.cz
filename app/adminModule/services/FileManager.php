<?php
/**
 * @author Jan Kotrba <kotrba@kotyslab.cz>
 */

namespace App\AdminModule\Services;

use App\AdminModule\BaseManager;
use App\AdminModule\Model\Entities\File;
use Nette\Http\FileUpload;
use Nette\Utils\DateTime;
use Nette\Utils\Finder;
use Nette\Utils\Random;

/**
 * Class FileManager
 * @package App\AdminModule\Services
 */
class FileManager extends BaseManager
{
	/**
	 * @param FileUpload $fileUpload
	 * @return File
	 * @throws FileManagerException
	 */
	public function uploadFile(FileUpload $fileUpload): File
	{
		if ($fileUpload->isOk()) {
			try {
				$directory = $this->getFileDir();
				$uploadedFile = $fileUpload->move(DATA_UPLOAD_DIR . $directory . "/" . $fileUpload->getSanitizedName());

				$file = new File();
				$file->setTitle($uploadedFile->getSanitizedName());
				$file->setPath($uploadedFile->getTemporaryFile());
				$file->setFileSize($uploadedFile->getSize());

				$this->entityManager->persist($file);
				$this->entityManager->flush();

				return $file;
			} catch (\Exception $e) {
				throw new FileManagerException($this->translator->translate('messages.fileManager.fileUploadFailed'));
			}
		} else {
			throw new FileManagerException($this->translator->translate('messages.fileManager.fileIsNotValid'));
		}
	}

	/**
	 * @return string
	 */
	private function getFileDir(): string
	{
		do {
			$directory = (new DateTime())->format('Y-m-d') . '/' . Random::generate(2) . '/' . Random::generate(2);
		} while (file_exists(DATA_UPLOAD_DIR . $directory) && is_dir(DATA_UPLOAD_DIR . $directory));

		return $directory;
	}

	/**
	 * @return array
	 */
	public function getAllFiles()
	{
		return $this->entityManager->getRepository(File::class)->findBy([], [
			'created' => 'DESC'
		]);
	}

	/**
	 * @param $id
	 * @return null|object
	 */
	public function getFile($id)
	{
		return $this->entityManager->getRepository(File::class)->find($id);
	}

	/**
	 * @param File $file
	 * @throws FileManagerException
	 */
	public function deleteFile(File $file)
	{
		if (file_exists($file->getPath())) {
			if (unlink($file->getPath())) {
				try {
					$this->cleanDirectory($file->getDirectory());
					$this->entityManager->remove($file);
					$this->entityManager->flush();
				} catch (\Exception $e) {
					throw new FileManagerException($this->translator->translate('messages.fileManager.deleteFileFailed'));
				}
			} else {
				throw new FileManagerException($this->translator->translate('messages.fileManager.deleteFileFailed'));
			}
		} else {
			try {
				$this->cleanDirectory($file->getDirectory());
				$this->entityManager->remove($file);
				$this->entityManager->flush();
			} catch (\Exception $e) {
				throw new FileManagerException($this->translator->translate('messages.fileManager.deleteFileFailed'));
			}
			throw new FileManagerException($this->translator->translate('messages.fileManager.fileNotFound'));
		}
	}

	/**
	 * Try to remove empty directories
	 * @param string $destination
	 */
	private function cleanDirectory(string $destination)
	{
		$pathParts = array_filter(explode('/', str_replace(DATA_UPLOAD_DIR, "", $destination)));

		for ($i = count($pathParts); $i > 0; $i--) {
			$path = DATA_UPLOAD_DIR . implode('/', array_slice($pathParts, 0, $i));

			if (file_exists($path)) {
				if (Finder::findFiles('*')->from($path)->count() === 0) {
					@rmdir($path);
				} else {
					return;
				}
			}
		}
	}
}

/**
 * Class FileManagerException
 * @package App\AdminModule\Services
 */
class FileManagerException extends \Exception
{
}