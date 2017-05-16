<?php
/**
 * @author Jan Kotrba <kotrba@kotyslab.cz>
 */

namespace App\AdminModule\Presenters;

use App\AdminModule\SecuredAdminPresenter;
use Nette\Application\UI\Form;
use Tracy\Debugger;

/**
 * Class ProductsPresenter
 * @package App\AdminModule\Presenters
 */
class ProductsPresenter extends SecuredAdminPresenter
{
	/**
	 * @var string
	 * @persistent
	 */
	public $searchTerm;

	/**
	 * @var integer
	 * @persistent
	 */
	public $rootCatId;

	/**
	 * @var integer
	 * @persistent
	 */
	public $childCatId;


	/**
	 * @return Form
	 */
	public function createComponentSearchForm(): Form
	{
		$form = new Form();
		$form->addText('searchTerm')->setDefaultValue($this->searchTerm);

		$form->addSelect('rootCatId', null, [
			0 => 'Vyberte',
			1 => 'Kategorie 1',
			2 => 'Kategorie 2',
			3 => 'Kategorie 3',
			4 => 'Kategorie 4'
		])->setDefaultValue($this->rootCatId);

		$form->addSelect('childCatId', null, [
			0 => 'Vyberte',
			1 => 'Kategorie 1',
			2 => 'Kategorie 2',
			3 => 'Kategorie 3',
			4 => 'Kategorie 4'
		])->setDefaultValue(($this->rootCatId > 0) ? $this->childCatId : null)->setDisabled(!$this->rootCatId);

		$form->addSubmit('find');

		$form->onSuccess[] = function (Form $form, $formValues) {
			Debugger::barDump($formValues);
			$this->redirect('this', [
				'searchTerm' => $formValues->searchTerm,
				'rootCatId' => $formValues->rootCatId,
				'childCatId' => isset($formValues->childCatId) ? $formValues->childCatId : null
			]);
		};

		return $form;
	}
}