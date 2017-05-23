<?php
/**
 * @author Jan Kotrba <kotrba@kotyslab.cz>
 */

namespace App\AdminModule\Presenters;

use App\AdminModule\Model\Entities\File;
use App\AdminModule\SecuredAdminPresenter;
use App\AdminModule\Services\FileManager;
use App\AdminModule\Services\FileManagerException;
use App\Shared\FlashMessage\FlashMessageType;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Form;

/**
 * Class FilesPresenter
 * @package App\AdminModule\Presenters
 */
class FilesPresenter extends SecuredAdminPresenter
{
	/**
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * FilesPresenter constructor.
	 * @param Translator $translator
	 * @param FileManager $fileManager
	 */
	public function __construct(Translator $translator, FileManager $fileManager)
	{
		parent::__construct($translator);
		$this->fileManager = $fileManager;
	}

	/**
	 *
	 */
	public function actionDefault()
	{
		$this->getTemplate()->files = $this->fileManager->getAllFiles();
	}

	/**
	 * @param $fileId
	 */
	public function handleDeleteFile($fileId)
	{
		$file = $this->fileManager->getFile($fileId);
		if ($file instanceof File) {
			try {
				$this->fileManager->deleteFile($file);
				$this->flashMessage($this->translator->translate('messages.fileManager.fileRemoved'), FlashMessageType::$SUCCESS);
			} catch (FileManagerException $e) {
				$this->flashMessage($e->getMessage(), FlashMessageType::$DANGER);
			}
		} else {
			$this->flashMessage($this->translator->translate('messages.fileManager.fileNotFound'), FlashMessageType::$WARNING);
		}

		$this->redirect('this');
	}

	/**
	 * @return Form
	 */
	protected function createComponentUploadFile()
	{
		$form = new Form();
		$form->addUpload('file')->setRequired();
		$form->addSubmit('upload');

		$form->onSuccess[] = function (Form $form, $formValues) {
			try {
				$this->fileManager->uploadFile($formValues->file);
				$this->flashMessage($this->translator->translate('messages.fileManager.fileUploaded'), FlashMessageType::$SUCCESS);
				$this->redirect('this');
			} catch (FileManagerException $e) {
				$this->flashMessage($e->getMessage(), FlashMessageType::$DANGER);
			}
		};

		return $form;
	}
}