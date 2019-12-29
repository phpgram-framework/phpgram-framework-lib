<?php
/**
 * phpgram project
 *
 * This File is part of the phpgram Framework Lib
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
use Gram\Strategy\StdAppStrategy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GramViewStrategy extends StdAppStrategy
{

	/**
	 * @inheritdoc
	 *
	 * Führt die View Klasse aus und gibt das ausgefüllte Template zurück
	 */
	public function invoke(
		ResolverInterface $resolver,
		array $param,
		ServerRequestInterface $request,
		ResponseInterface $response
	):ResponseInterface
	{
		$this->prepareResolver($request,$response,$resolver);

		$content = $resolver->resolve($param);

		if($content instanceof ViewInterface){
			$content = $content->render();
		}

		return $this->createBody($resolver,$content);
	}
}