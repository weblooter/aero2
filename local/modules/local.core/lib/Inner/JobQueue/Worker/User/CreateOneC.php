<?
namespace Local\Core\Inner\JobQueue\Worker\User;

use \Local\Core\Inner;
use \Bitrix\Main;

class CreateOneC extends Inner\JobQueue\Abstracts\Worker implements Inner\Interfaces\UseInDb
{
    /**
     * {@inheritdoc}
     */
    public function doJob(): Main\Result
    {
        $result = new Main\Result();
        $arInputData = $this->getInputData();

        /**
         * @var \Bitrix\Main\Result $obRes
         */
        if (empty($arInputData['USER_ID'])) {
            throw new \Local\Core\Inner\JobQueue\Exception\FailException('Не передан ID пользователя');
        }

        \Local\Core\Exchange\Onec\User\User::create($arInputData['USER_ID']);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getNextExecuteAt(int $addSecond = 120): Main\Type\DateTime
    {
        //Some Another logic
        return parent::getNextExecuteAt($addSecond);
    }

}