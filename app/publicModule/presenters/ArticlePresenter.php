<?php
/**
 * @author Jan Kotrba <kotrba@kotyslab.cz>
 */

namespace App\PublicModule\Presenters;

use App\PublicModule\BasePublicPresenter;
use Tracy\Debugger;

class ArticlePresenter extends BasePublicPresenter
{
	public function actionDefault($id) {
		Debugger::barDump($id);
	}
}