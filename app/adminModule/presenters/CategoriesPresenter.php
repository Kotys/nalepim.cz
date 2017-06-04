<?php
/**
 * @author Jan Kotrba <kotrba@kotyslab.cz>
 */

namespace App\AdminModule\Presenters;

use App\AdminModule\Model\Entities\Category;
use App\AdminModule\SecuredAdminPresenter;
use App\AdminModule\Services\CategoryManager;
use App\AdminModule\Services\CategoryManagerException;
use App\Shared\FlashMessage\FlashMessageType;
use Kdyby\Translation\Translator;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Tracy\Debugger;

/**
 * Class CategoriesPresenter
 * @package App\AdminModule\Presenters
 */
class CategoriesPresenter extends SecuredAdminPresenter
{
	/**
	 * @var CategoryManager
	 */
	private $categoryManager;

	/**
	 * @var Category
	 */
	private $categoryDetail;

	/**
	 * CategoriesPresenter constructor.
	 * @param Translator $translator
	 * @param CategoryManager $categoryManager
	 */
	public function __construct(Translator $translator, CategoryManager $categoryManager)
	{
		parent::__construct($translator);
		$this->categoryManager = $categoryManager;
	}

	/**
	 *
	 */
	public function actionDefault()
	{
		$this->getTemplate()->categories = $this->categoryManager->getRootCategories();
	}

	/**
	 * @param $id
	 */
	public function actionDetail($id)
	{
		if (!empty($id)) {
			$this->categoryDetail = $this->categoryManager->getRepository()->find($id);
			if ($this->categoryDetail instanceof Category) {
				$this->getTemplate()->category = $this->categoryDetail;
			} else {
				$this->flashMessage('TODO NOT FOUND MESSAGE', FlashMessageType::$DANGER);
				$this->redirect('Categories:default');
			}
		} else {
			$this->flashMessage('TODO NOT FOUND MESSAGE', FlashMessageType::$DANGER);
			$this->redirect('Categories:default');
		}
	}

	/**
	 * @return Form
	 */
	public function createComponentNewCategoryForm()
	{
		$form = new Form();

		$form->addText('title')->setRequired();

		$rootCategoriesAvailable = [null => "- ŽÁDNÁ -"];
		foreach ($this->categoryManager->getRootCategories() as $rootCategory) {
			$rootCategoriesAvailable[$rootCategory->getId()] = $rootCategory->getTitle();
		}

		$form->addSelect('parentCategory', null, $rootCategoriesAvailable)->setDefaultValue(null);

		$form->addSubmit('create');

		$form->onSuccess[] = function (Form $form, $values) {
			if (isset($values->parentCategory) && !empty($values->parentCategory)) {
				$parentCategory = $this->categoryManager->getRepository()->find($values->parentCategory);
				if (!$parentCategory instanceof Category) {
					$this->flashMessage($this->translator->translate('messages.categoryManager.parentCategoryNotFound'), FlashMessageType::$WARNING);
					return;
				}
			}

			try {
				$this->categoryManager->createCategory($values->title, (isset($parentCategory) AND $parentCategory instanceof Category) ? $parentCategory : null);
				$this->flashMessage($this->translator->translate('messages.categoryManager.newCategoryCreated'), FlashMessageType::$SUCCESS);
				$this->redirect('Categories:default');
			} catch (CategoryManagerException $e) {
				$this->flashMessage($e->getMessage(), FlashMessageType::$DANGER);
			}
		};

		return $form;
	}

	/**
	 * @return Form
	 * @throws BadRequestException
	 */
	public function createComponentEditCategoryForm()
	{
		if (!$this->categoryDetail instanceof Category) {
			throw new BadRequestException('Category not found');
		}

		$form = new Form();

		$form->addText('title')
			->setDefaultValue($this->categoryDetail->getTitle())
			->setRequired();

		$rootCategoriesAvailable = [null => "- ŽÁDNÁ -"];
		foreach ($this->categoryManager->getRootCategories() as $rootCategory) {
			$rootCategoriesAvailable[$rootCategory->getId()] = $rootCategory->getTitle();
		}

		$form->addSelect('parentCategory', null, $rootCategoriesAvailable)
			->setDefaultValue(($this->categoryDetail->getParentCategory() instanceof Category) ? $this->categoryDetail->getParentCategory()->getId() : null);

		$form->addSubmit('save');

		$form->onSuccess[] = function (Form $form, $values) {
			$updatedCategory = $this->categoryDetail;
			$updatedCategory->setTitle($values->title);

			if ($values->parentCategory !== null) {
				$parentCategory = $this->categoryManager->getRepository()->find($values->parentCategory);
				if ($parentCategory instanceof Category) {
					$updatedCategory->setParentCategory($parentCategory);
				} else {
					$this->flashMessage($this->translator->translate('messages.categoryManager.parentCategoryNotFound'), FlashMessageType::$WARNING);
					return;
				}
			} else {
				$updatedCategory->setParentCategory(null);
			}

			try {
				$this->categoryManager->updateCategory($updatedCategory);
				$this->flashMessage($this->translator->translate('messages.categoryManager.categoryUpdated'), FlashMessageType::$SUCCESS);
				$this->redirect('this');
			} catch (CategoryManagerException $e) {
				$this->flashMessage($e->getMessage(), FlashMessageType::$DANGER);
			}
		};

		return $form;
	}
}