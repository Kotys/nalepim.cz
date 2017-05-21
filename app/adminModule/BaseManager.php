<?php
/**
 * @author Jan Kotrba <kotrba@kotyslab.cz>
 */

namespace App\AdminModule;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Translation\Translator;

abstract class BaseManager
{
	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * @var Translator
	 */
	protected $translator;

	function __construct(EntityManager $entityManager, Translator $translator)
	{
		$this->entityManager = $entityManager;
		$this->translator = $translator;
	}
}