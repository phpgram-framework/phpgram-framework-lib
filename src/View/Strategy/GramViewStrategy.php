<?php
/**
 * phpgram project
 *
 * This File is part of the phpgram Mvc Framework Lib
 *
 * Web: https://gitlab.com/grammm/php-gram/phpgram-framework-lib/tree/master
 *
 * @license https://gitlab.com/grammm/php-gram/phpgram-framework-lib/blob/master/LICENSE
 *
 * @author Jörn Heinemann <joernheinemann@gmx.de>
 */

namespace Gram\Project\Lib\View\Strategy;

use Gram\Project\Lib\View\ViewInterface;
use Gram\Resolver\ResolverInterface;
use Gram\Strategy\StrategyInterface;

class GramViewStrategy implements StrategyInterface
{
	/**
	 * @inheritdoc
	 */
	public function getHeader()
	{
		return ["name"=>'Content-Type',"value"=>'text/html'];
	}

	/**
	 * @inheritdoc
	 *
	 * Führt die View Klasse aus und gibt das ausgefüllte Template zurück
	 */
	public function invoke(ResolverInterface $resolver, array $param)
	{
		$result = $resolver->resolve($param);

		if(!$result instanceof ViewInterface){
			return $result;
		}

		return $result->render();
	}
}