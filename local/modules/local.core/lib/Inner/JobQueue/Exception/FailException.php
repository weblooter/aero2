<?php

namespace Local\Core\Inner\JobQueue\Exception;

/**
 * Если будет выброшено при выполнении работой воркером,<br>
 * выполняемая работа будет помечена статусом F.<br>
 * И больше отрабатывать не будет.
 * Class Fail
 * @package Local\Core\Inner\JobQueue\Exception
 */
final class FailException extends \Exception
{

}
