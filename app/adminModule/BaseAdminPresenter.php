<?php
/**
 * @author Jan Kotrba <kotrba@kotyslab.cz>
 */

namespace App\AdminModule;

use Kdyby\Translation\Translator;
use Nette\Application\UI\Presenter;

abstract class BaseAdminPresenter extends Presenter
{
	/**
	 * @var Translator
	 */
	protected $translator;

	/**
	 * BaseAdminPresenter constructor.
	 * @param Translator $translator
	 */
	public function __construct(Translator $translator)
	{
		$this->translator = $translator;
	}
}