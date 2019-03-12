<?

namespace Local\Core\Inner\JobQueue\Worker;

class ProviderXmlExport extends \Local\Core\Inner\JobQueue\Abstracts\Worker implements \Local\Core\Inner\Interfaces\UseInDb
{

    static $saveDirectory = '/upload/provider_exchange';
    static $C_OPTION_CODE = 'PROVIDER_XML_EXPORT_LAST_ORDER_ID';
    static $nextExecuteTime = 60 * 5;

    /**
     * {@inheritdoc}
     */
    public function doJob(): \Bitrix\Main\Result
    {
        $result = new \Bitrix\Main\Result();

        if ( !\Bitrix\Main\Loader::includeModule( 'sale' ) )
        {
            throw new \Exception( 'Module sale is not loaded!' );
        }

        if ( !\Bitrix\Main\Loader::includeModule( 'iblock' ) )
        {
            throw new \Exception( 'Module iblock is not loaded!' );
        }

        $arWorkerParams = $this->getInputData();

        if ( empty( $arWorkerParams[ 'ORDER_ID' ] ) )
        {
            throw new \Local\Core\Inner\JobQueue\Exception\FailException( 'Worker param "ORDER_ID" is empty' );
        }


        try
        {
            $res = new \Local\Core\Exchange\Provider\OrderExport( $arWorkerParams[ 'ORDER_ID' ] );
            if ( !$res->getResult()->isSuccess() )
            {
                throw new \Exception( implode( '; ', $res->getResult()->getErrorMessages() ) );
            }
        }
        catch ( \Local\Core\Exchange\Provider\Exception\ExportTypeException $e )
        {
            // Обработка ошибку выбора типа экспорта провайдера
            static::$nextExecuteTime = 60 * 60 * 24;
            throw new \Exception( 'Экпортируемого типа не существует. '.$e->getMessage() );
        }
        catch ( \Local\Core\Exchange\Provider\Exception\ExportIncompleteDataException $e )
        {
            // Обработка ошибки не полных данных
            static::$nextExecuteTime = 60 * 60 * 24;
            throw new \Exception( 'При инициализации экспорта не были переданые все необходимые параметры - '.$e->getMessage() );
        }
        catch ( \Local\Core\Exchange\Provider\Exception\ExportDataIsEmptyException $e )
        {
            // Обработка пустых данных при создание экспорта
            static::$nextExecuteTime = 60 * 60 * 24;
            throw new \Exception( 'Класс продайдера '.$e->getMessage().' получил все данные и был запущен, но по какой то причине, перебрав корзину, мы не получили никаких данных для экспорта' );
        }
        catch ( \Local\Core\Exchange\Provider\Exception\ExportHandlerTypeNotDeclaredException $e )
        {
            static::$nextExecuteTime = 60 * 60 * 24;
            throw new \Exception( 'У провайдера был запущен экспорт в формате, который у него не описан. '.$e->getMessage() );
        }
        catch ( \Exception $e )
        {
            // Заказ не найден или корзина у заказа пустая
            throw new \Local\Core\Inner\JobQueue\Exception\FailException( 'Order with ID "'.$arWorkerParams[ 'ORDER_ID' ].'" not found or order\'s basket is empty' );
        }

        // TODO LOG_WRITER

        //Some php code

        //You can
        //throw New \Exception('Some');
        //Or
        //$result->addError(new \Bitrix\Main\Error('Some Error'));

        //If need final, without success
        //throw new \Local\Core\Inner\JobQueue\Exception\FailException('Финалочка');

        //All other throw \Throwable will be logged as critical
        //All \Bitrix\Main\Error in Result will NOT be logged. It's your issue
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getNextExecuteAt( int $addSecond = 0 ): \Bitrix\Main\Type\DateTime
    {

        $addSecond = static::$nextExecuteTime;

        //Some Another logic
        return parent::getNextExecuteAt( $addSecond );
    }

}