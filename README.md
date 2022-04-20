# bitrix.smsc
Рассыльщик смс для Битрикса. Реализуется в рамках стандартного функционала, поэтому может использоваться в стандартных процессах битрикса, например, регистрация и подтверждение номера телефона.

Для использования подключаем в php_interface/init.php

```php

$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandler('messageservice', 'onGetSmsSenders', function ()
    {
        return [
            new Smsc(),
        ];
    });

```
