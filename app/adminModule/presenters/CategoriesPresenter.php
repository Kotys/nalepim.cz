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
	 * @var Category
	 */
	private $defaultCategory;

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
				$this->flashMessage($this->translator->translate('messages.categoryManager.categoryNotFound'), FlashMessageType::$DANGER);
				$this->redirect('Categories:default');
			}
		} else {
			$this->flashMessage($this->translator->translate('messages.categoryManager.categoryNotFound'), FlashMessageType::$DANGER);
			$this->redirect('Categories:default');
		}
	}

	/**
	 * @param $id
	 */
	public function handleRemoveCategory($id)
	{
		try {
			$category = $this->categoryManager->getRepository()->find($id);
			if ($category instanceof Category) {
				$this->categoryManager->removeCategory($category);
				$this->flashMessage($this->translator->translate('messages.categoryManager.categoryRemoved'), FlashMessageType::$SUCCESS);
				$this->redirect('Categories:default');
			} else {
				$this->flashMessage($this->translator->translate('messages.categoryManager.categoryNotFound'), FlashMessageType::$DANGER);
				$this->redirect('Categories:default');
			}
		} catch (CategoryManagerException $e) {
			$this->flashMessage($e->getMessage(), FlashMessageType::$DANGER);
			$this->redirect('Categories:default');
		}
	}

	/**
	 * @param string|null $parentCategory
	 */
	public function actionNew(string $parentCategory = null)
	{
		if (!empty($parentCategory)) {
			$this->defaultCategory = $this->categoryManager->getRepository()->find($parentCategory);
			if (!$this->defaultCategory instanceof Category) {
//				$this->flashMessage();
			}
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

		$form->addSelect('parentCategory', null, $rootCategoriesAvailable)
			->setDefaultValue(($this->defaultCategory instanceof Category) ? $this->defaultCategory->getId() : null);

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

		if(!$this->categoryDetail->isRootCategory()) {
			$rootCategoriesAvailable = [null => "- ŽÁDNÁ -"];
			foreach ($this->categoryManager->getRootCategories() as $rootCategory) {
				$rootCategoriesAvailable[$rootCategory->getId()] = $rootCategory->getTitle();
			}

			$form->addSelect('parentCategory', null, $rootCategoriesAvailable)
				->setDefaultValue(($this->categoryDetail->getParentCategory() instanceof Category) ? $this->categoryDetail->getParentCategory()->getId() : null);
		}

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