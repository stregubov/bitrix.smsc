<?php

class SmscClient
{
    private $login;
    private $password;

    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function sendSms(array $phones, string $message, string $sender = null): bool
    {
        $context = stream_context_create([
            "http" => [
                "method" => "POST",
                "timeout" => 3
            ]
        ]);

        $phones = array_map(function ($item) {
            return Helper::trimPhone($item);
        }, $phones);

        if (empty($phones)) {
            return false;
        }

        $queryParams = [
            "login" => $this->login,
            "psw" => $this->password,
            "phones" => implode(";", $phones),
            "mes" => $message,
            "charset" => "utf-8"
        ];

        if (!empty($sender)) {
            $queryParams['sender'] = $sender;
        }

        $queryString = http_build_query($queryParams);

        $result = file_get_contents("https://smsc.ru/sys/send.php?$queryString", false, $context);

        return strpos($result, "ERROR") === false;
    }

    public function getFromList(): array
    {
        $senders = [];
        $queryParams = [
            "login" => $this->login,
            "psw" => $this->password,
            "get" => "1"
        ];
        $queryString = http_build_query($queryParams);

        $result = file_get_contents("https://smsc.ru/sys/senders.php?$queryString");

        return json_decode($result, true);
    }
}
