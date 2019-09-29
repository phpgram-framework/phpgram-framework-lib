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
 * @author Jörn Heinemann <j.heinemann1@web.de>
 */

namespace Gram\Project\Lib\Cache\Listener;

/**
 * Interface ListenerInterface
 * @package Gram\Project\Lib\Listener
 *
 * Ein allgemeines Interface für alle Listener um Caches zu löschen
 */
interface ListenerInterface
{
	public function trigger(array $ids);
}