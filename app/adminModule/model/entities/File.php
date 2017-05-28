<?php
/**
 * @author Jan Kotrba <kotrba@kotyslab.cz>
 */

namespace App\AdminModule\Model\Entities;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class File
{
	use Identifier;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $title;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	protected $fileSize;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	protected $created;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $path;


	/**
	 * File constructor.
	 */
	function __construct()
	{
		$this->created = new \DateTime('now');
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @return int
	 */
	public function getFileSize(): int
	{
		return $this->fileSize;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated(): \DateTime
	{
		return $this->created;
	}

	/**
	 * @return string
	 */
	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * @return string
	 */
	public function getDirectory(): string
	{
		return str_replace($this->getTitle(), "", $this->path);
	}

	/**
	 * @param string $title
	 */
	public function setTitle(string $title)
	{
		$this->title = $title;
	}

	/**
	 * @param int $fileSize
	 */
	public function setFileSize(int $fileSize)
	{
		$this->fileSize = $fileSize;
	}

	/**
	 * @param string $path
	 */
	public function setPath(string $path)
	{
		$this->path = $path;
	}

	/**
	 * @return string
	 */
	public function getFileUrl()
	{
		if ($this->path[0] === ".") {
			return '//' . $_SERVER['HTTP_HOST'] . substr($this->path, 1);
		} else {
			return '//' . $_SERVER['HTTP_HOST'] . $this->path;
		}
	}
}