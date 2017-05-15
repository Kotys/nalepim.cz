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
	 * @var bool
	 * @persistent
	 */
	public $awaitingPayment = true;

	/**
	 * @var bool
	 * @persistent
	 */
	public $awaitingExpedition = true;

	/**
	 * @var bool
	 * @persistent
	 */
	public $resolved = false;

	/**
	 * @var bool
	 * @persistent
	 */
	public $canceledTest = false;

	/**
	 * @var string
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
	 * @param $filterKey string
	 */
	public function handleChangeFilterValue(string $filterKey): void
	{
		if(isset($this->$filterKey)) {
			$this->$filterKey = !$this->$filterKey;
		}

		$this->redirect('this');
	}

	/**
	 * @return Form
	 */
	public function createComponentSearchForm(): Form {
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