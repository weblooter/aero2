<?php
namespace Local\Core\Inner\BxModified;


class HttpResponse extends \Bitrix\Main\HttpResponse
{

    /**
     * Создает ответ в формате JSON
     *
     * @param     $data
     * @param int $status
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\ArgumentTypeException
     */
    public function setContentJson($data, $status = 200)
    {
        $json = \Bitrix\Main\Web\Json::encode($data, JSON_UNESCAPED_UNICODE);

        $this->setContent($json);

        if ($json === false) {
            throw new \RuntimeException(json_last_error_msg(), json_last_error());
        }

        $this->setHeader('Content-Type: application/json;charset=utf-8');

        $this->setStatus($status);

    }

}