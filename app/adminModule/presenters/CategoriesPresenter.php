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
	 * @return Form
	 */
	public function createComponentNewCategoryForm()
	{
		$form = new Form();

		$form->addText('title')->setRequired();

		$rootCategoriesAvailable = [null => "- ŽÁDNÁ -"];

		/**
		 * @var $rootCategory Category
		 */
		foreach ($this->categoryManager->getRootCategories() as $rootCategory) {
			$rootCategoriesAvailable[$rootCategory->getId()] = $rootCategory->getTitle();
		}

		$form->addSelect('parentCategory', null, $rootCategoriesAvailable)->setDefaultValue(null);

		$form->addSubmit('create');

		$form->onSuccess[] = function (Form $form, $values) {
			if (isset($values->parentCategory)) {
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
}