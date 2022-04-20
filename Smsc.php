<?php

use Bitrix\Main\Error;
use Bitrix\MessageService\Sender\Base;
use Bitrix\MessageService\Sender\Result\SendMessage;

class Smsc extends Base
{
    private $login;

    private $password;

    private $client;

    public function __construct()
    {
        $this->login = 'my-login';
        $this->password = 'my-password';

        $this->client = new SmscClient($this->login, $this->password);
    }

    public function sendMessage(array $messageFields)
    {
        if (!$this->canUse()) {
            $result = new SendMessage();
            $result->addError(new Error('Ошибка отправки. СМС-сервис отключен'));
            return $result;
        }

        $parameters = [
            'phones' => $messageFields['MESSAGE_TO'],
            'mes' => $messageFields['MESSAGE_BODY'],
        ];

        if ($messageFields['MESSAGE_FROM']) {
            $parameters['sender'] = $messageFields['MESSAGE_FROM'];
        }

        $result = new SendMessage();
        $response = $this->client->sendSms([$parameters['phones']], $parameters['mes'], $parameters['sender']);

        if (!$response) {
            $result->addError(new Error("Отправка не удалась", 0, 100));
            return $result;
        }

        return $result;
    }

    public function getShortName()
    {
        return 'smsc.ru';
    }

    public function getId()
    {
        return 'smscru';
    }

    public function getName()
    {
        return 'SMS-центр';
    }

    public function canUse()
    {
        return true;
    }

    public function getFromList()
    {
        return $this->client->getFromList();
    }
}
