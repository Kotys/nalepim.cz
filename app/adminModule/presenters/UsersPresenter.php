<?php
/**
 * @author Jan Kotrba <kotrba@kotyslab.cz>
 */

namespace App\AdminModule\Presenters;

use App\AdminModule\SecuredAdminPresenter;
use App\Shared\FlashMessage\FlashMessageType;

class UsersPresenter extends SecuredAdminPresenter
{
	function beforeRender()
	{
		$this->flashMessage('SUCCESS', FlashMessageType::$SUCCESS);
		$this->flashMessage('INFO', FlashMessageType::$INFO);
		$this->flashMessage('WARNING', FlashMessageType::$WARNING);
		$this->flashMessage('DANGER', FlashMessageType::$DANGER);
	}
}