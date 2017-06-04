<?php
/**
 * @author Jan Kotrba <kotrba@kotyslab.cz>
 */

namespace App\AdminModule\Model\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\Strings;

/**
 * @ORM\Entity
 */
class Category
{
	use Identifier;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $title;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $urlTitle;

	/**
	 * @ORM\OneToMany(targetEntity="Category", mappedBy="parentCategory")
	 */
	protected $childrenCategories;

	/**
	 * @ORM\ManyToOne(targetEntity="Category", inversedBy="childrenCategories")
	 * @ORM\JoinColumn(name="parentCategory", referencedColumnName="id")
	 */
	protected $parentCategory;

	public function __construct()
	{
		$this->childrenCategories = new ArrayCollection();
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @return mixed
	 */
	public function getChildrenCategories()
	{
		return $this->childrenCategories;
	}

	/**
	 * @return mixed
	 */
	public function getParentCategory()
	{
		return $this->parentCategory;
	}

	/**
	 * @param string $title
	 */
	public function setTitle(string $title)
	{
		$this->title = $title;
		$this->urlTitle = Strings::webalize($title);
	}

	/**
	 * @param mixed $parentCategory
	 */
	public function setParentCategory(Category $parentCategory)
	{
		$this->parentCategory = $parentCategory;
	}

	public function isRootCategory() {
		return $this->parentCategory === null;
	}

	/**
	 * @return string
	 */
	public function getUrlTitle(): string
	{
		return $this->urlTitle;
	}
}