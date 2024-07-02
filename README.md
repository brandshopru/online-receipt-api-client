# PHP клиент для API автоматической фискализации чеков интернет-магазина
[![](https://img.shields.io/packagist/l/Brandshopru/online-receipt-php-api-client.svg?style=flat-square)](https://github.com/Brandshopru/online-receipt-php-api-client/blob/master/LICENSE) 
[![](https://img.shields.io/packagist/dt/Brandshopru/online-receipt-php-api-client.svg?style=flat-square)](https://packagist.org/packages/Brandshopru/online-receipt-php-api-client)
[![](https://img.shields.io/packagist/v/Brandshopru/online-receipt-php-api-client.svg?style=flat-square)](https://packagist.org/packages/Brandshopru/online-receipt-php-api-client)
[![](https://img.shields.io/travis/Brandshopru/online-receipt-php-api-client.svg?style=flat-square)](https://travis-ci.org/Brandshopru/online-receipt-php-api-client)
[![](https://img.shields.io/codecov/c/github/Brandshopru/online-receipt-php-api-client.svg?style=flat-square)](https://codecov.io/gh/Brandshopru/online-receipt-php-api-client)

Пакет предоставляет удобный интерфейс для общения с API Онлайн.Чека для отправки данных чеков в сервис фискализации. 
Пакет упрощает разработку модулей интеграции интернет-магазина с сервисом фискализации Онлайн.Чека.


## Требования
* php 7.1, 8.0 и выше
* guzzlehttp/guzzle (или любой клиент следующий интерфейсу `\GuzzleHttp\ClientInterface`)
* ext-json
* curl

## Использование

### Отправка данных чека на сервер фискализации (создание документа)
Для начала необходимо сформировать данные самого чека. Для этого достаточно для ваших моделей инплементировать интерфейсы OnlineReceiptOrderInterface для заказа, OnlineReceiptOrderItemInterface для товара в заказе, OnlineReceiptPaymentItemInterface для способа оплаты. Также вы можете использовать entity из пакета, или отнаследовать от них собственные классы переопределив методы на собственные.
```php
use Brandshopru\OnlineReceiptApiClient\Entity\Order;
use Brandshopru\OnlineReceiptApiClient\Entity\Cashier;
use Brandshopru\OnlineReceiptApiClient\Entity\OrderItem;
use Brandshopru\OnlineReceiptApiClient\Entity\PaymentItem;

$dateTime =  new \DateTime('NOW');
// Создаем заказ
$order = Order::create([
    'documentUuid'     => uniqid(),
    'checkoutDateTime' => $dateTime->format(DATE_RFC3339),
    'orderId'          => rand(100000, 999999),
    'typeOperation'    => 'SALE',
    'customerContact'  => 'test@example.com',
]);

// Созадем товары
$orderItem1 = OrderItem::create([
    'price' => 100,
    'quantity' => 1,
    'vatTag' => OrderItem::VAT_NO,
    'name' => 'Test Product1'
]);

$orderItem2 = OrderItem::create([
    'price' => 200,
    'quantity' => 1,
    'vatTag' => OrderItem::VAT_NO,
    'name' => 'Test Product2'
]);

//Создаем способ оплаты
$paymentItem = PaymentItem::create([
    'type' => 'CARD',
    'sum' => 300
]);

// Добавляем товары и способ оплаты к заказу
$order->addItem($orderItem1);
$order->addItem($orderItem2);
$order->addPaymentItem($paymentItem);

//Создаем кассира
$cashier = Cashier::create([
    'name' => 'Test Cashier',
    'inn' => '123456789012',
    'position' => 'salesman',
]);
```

Далее объект заказа необходимо передать клиенту, также вы можете передать `responseURL` и печатать ли чек на кассе:
```php
$login = 'test@test.ru'; // Логин полученный на первом шаге
$password = 'password'; // Пароль полученный на первом шаге
$testMode = true; // Тестовый режим
$client = new \Brandshopru\OnlineReceiptApiClient\Client($login, $password, $testMode);
$responseUrl =  'https://internet.shop.ru/order/982340931/checkout?completed=1';
$printReceipt = true; // Печатать ли чек на кассе
$result = $client->sendCheck($order, $responseUrl, $printReceipt, $cashier);
```
Все параметры кроме $order - опциональные. Если не передан объект `OnlineReceiptCashierInterface` 
то будут использованы данные из настроек торговой точки.

В ответ придет массив со статусом обработки документа и фискального накопителя.

### Проверка статуса документа
Если при передаче данных чека был передан `responseURL`, то на него придет результат фискализации, если параметр задан не был, то вы можете самостоятельно проверить статус документа:
```php
$login = 'test@test.ru'; // Логин полученный на первом шаге
$password = 'password'; // Пароль полученный на первом шаге
$testMode = true; // Тестовый режим
$documentId = 'efbafcdd-113a-45db-8fb9-718b1fdc3524'; // id документа
$client = new \Brandshopru\OnlineReceiptApiClient\Client($login, $password, $testMode);
$result = $client->getStatusDocumentById($documentId);
```
В ответ придет массив со статусом `status`, который может принимать значения:
* QUEUED - документ принят в очередь на обработку;
* PENDING - документ получен кассой для печати;
* PRINTED - фискализирован успешно;
* COMPLETED - результат фискализации отправлен (если было заполнено поле responseURL) в сервис источник;
* FAILED - ошибка при фискализации.


Также в массив придет `fnState` - статус фискального накопителя, может принимать значения:

* ready - соединение с фискальным накопителем установлено, состояние позволяет фискализировать чеки
* associated - клиент успешно связан с розничной точкой, но касса еще ни разу не вышла на связь и не сообщила свое состояние
* failed - Проблемы получения статуса фискального накопителя. Этот статус не препятствует добавлению документов для фискализации. Все документы будут добавлены в очередь на сервере и дождутся момента когда касса будет в состоянии их фискализировать

Кроме того вы можете вызвать отдельно метод проверки статуса фискального накопителя (сервиса фискализации):
```php
$client = new \Brandshopru\OnlineReceiptApiClient\Client($login, $password, $testMode);
$result = $client->getStatusFiscalService();
```
## Лицензия
[MIT](https://raw.githubusercontent.com/Brandshopru/online-receipt-php-api-client/master/LICENSE)
