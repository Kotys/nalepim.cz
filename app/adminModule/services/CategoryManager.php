<?php
/**
 * @author Jan Kotrba <kotrba@kotyslab.cz>
 */

namespace App\AdminModule\Services;

use App\AdminModule\BaseManager;
use App\AdminModule\Model\Entities\Category;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Translation\Translator;

/**
 * Class CategoryManager
 * @package App\AdminModule\Services
 */
class CategoryManager extends BaseManager
{
	/**
	 * CategoryManager constructor.
	 * @param EntityManager $entityManager
	 * @param Translator $translator
	 */
	public function __construct(EntityManager $entityManager, Translator $translator)
	{
		parent::__construct($entityManager, $translator);
	}

	/**
	 * @return array
	 */
	public function getRootCategories()
	{
		return $this->getRepository()->findBy([
			'parentCategory' => null
		]);
	}

	/**
	 * @param $title
	 * @param Category|null $parent
	 * @return Category
	 * @throws CategoryManagerException
	 */
	public function createCategory($title, Category $parent = null)
	{
		$existingCategory = $this->getRepository()->findOneBy([
			'title' => $title
		]);

		if ($existingCategory instanceof Category) {
			throw new CategoryManagerException($this->translator->translate('messages.categoryManager.categoryAlreadyExists'));
		}

		$category = new Category();
		$category->setTitle($title);
		if ($parent instanceof Category) {
			$category->setParentCategory($parent);
		}

		try {
			$this->entityManager->persist($category);
			$this->entityManager->flush();
			return $category;
		} catch (\Exception $e) {
			throw new CategoryManagerException($this->translator->translate('messages.categoryManager.createCategoryFailed'));
		}
	}

	/**
	 * @return \Kdyby\Doctrine\EntityRepository
	 */
	public function getRepository()
	{
		return $this->entityManager->getRepository(Category::class);
	}
}

/**
 * Class CategoryManagerException
 * @package App\AdminModule\Services
 */
class CategoryManagerException extends \Exception
{
}