<?php
/**
 * @author Jan Kotrba <kotrba@kotyslab.cz>
 */

namespace App\AdminModule\Presenters;

use App\AdminModule\SecuredAdminPresenter;
use Nette\Application\UI\Form;

/**
 * Class OrdersPresenter
 * @package App\AdminModule\Presenters
 */
class OrdersPresenter extends SecuredAdminPresenter
{
	/**
	 * @persistent
	 */
	public $awaitingPayment = true;

	/**
	 * @persistent
	 */
	public $awaitingExpedition = true;

	/**
	 * @persistent
	 */
	public $resolved = false;

	/**
	 * @persistent
	 */
	public $canceledTest = false;

	/**
	 * @persistent
	 */
	public $searchTerm;

	/**
	 *
	 */
	public function actionDefault()
	{
		$this->getTemplate()->awaitingPayment = $this->awaitingPayment;
		$this->getTemplate()->awaitingExpedition = $this->awaitingExpedition;
		$this->getTemplate()->resolved = $this->resolved;
		$this->getTemplate()->canceledTest = $this->canceledTest;
	}

	/**
	 * @param $filterKey
	 */
	public function handleChangeFilterValue($filterKey)
	{
		if(isset($this->$filterKey)) {
			$this->$filterKey = !$this->$filterKey;
		}

		$this->redirect('this');
	}

	/**
	 * @return Form
	 */
	public function createComponentSearchForm() {
		$form = new Form();
		$form->addText('searchTerm')->setDefaultValue($this->searchTerm);
		$form->addSubmit('find');

		$form->onSuccess[] = function(Form $form, $formValues) {
			$this->redirect('this', [
				'searchTerm' => $formValues->searchTerm
			]);
		};

		return $form;
	}
}