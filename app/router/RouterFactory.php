<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$adminRouter = new RouteList('Admin');
		$adminRouter[] = new Route('/admin/sign-in', 'Sign:in');
		$adminRouter[] = new Route('/admin/<presenter>/<action>[/<id>]', 'Dashboard:default');

		$publicRouter = new RouteList('Public');
		$publicRouter[] = new Route('product/<id>', 'Product:default');
		$publicRouter[] = new Route('article/<id>', 'Article:default');
		$publicRouter[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');

		$router = new RouteList();
		$router[] = $adminRouter;
		$router[] = $publicRouter;

		return $router;
	}

}
