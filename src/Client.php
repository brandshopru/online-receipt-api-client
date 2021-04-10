<?php
/**
 * This file is part of OnlineReceipt package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brandshopru\OnlineReceiptApiClient;

use Brandshopru\OnlineReceiptApiClient\Contracts\ClientInterface;
use Brandshopru\OnlineReceiptApiClient\Contracts\OnlineReceiptCashierInterface;
use Brandshopru\OnlineReceiptApiClient\Contracts\OnlineReceiptOrderInterface;

/**
 * Class Client.
 */
class Client implements ClientInterface
{
    const LOGIN_URI = '/login';
    const STATUS_URI = '/status';
    const SEND_CHECK_DATA_URI = '/doc';
    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $token;

    /**
     * @var bool
     */
    private $testMode;

    /**
     * @var \GuzzleHttp\Client|\GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * Client constructor.
     *
     * @param string $login
     * @param string $password
     * @param bool $testMode
     * @param \GuzzleHttp\ClientInterface $client
     */
    public function __construct(string $login, string $password, bool $testMode = false, \GuzzleHttp\ClientInterface $client = null)
    {
        $this->login = $login;
        $this->password = $password;
        $this->testMode = $testMode;
        $this->client = $client ?? new \GuzzleHttp\Client();
    }

    /**
     * Опрос готовности сервиса фискализации.
     *
     * @return array ['status', 'statusDateTime']
     */
    public function getStatusFiscalService()
    {
        $url = Config::getBaseUrl($this->testMode) . self::STATUS_URI;

        return $this->send('GET', $url);
    }

    /**
     * Отправка данных чека на сервер фискализации (создание документа).
     *
     * @param OnlineReceiptOrderInterface $order
     * @param null $responseUrl
     * @param bool $printReceipt
     * @param OnlineReceiptCashierInterface|null $cashier
     *
     * @return array|bool|float|int|string
     */
    public function sendCheck(OnlineReceiptOrderInterface $order, $responseUrl = null, $printReceipt = false, OnlineReceiptCashierInterface $cashier = null)
    {
        $url = Config::getBaseUrl($this->testMode) . self::SEND_CHECK_DATA_URI;
        $checkData = CheckDataFactory::convertToArray($order, $responseUrl, $printReceipt, $cashier);

        return $this->send('PUT', $url, $checkData);
    }

    /**
     * Проверка статуса документа.
     *
     * @param $documentId
     *
     * @return array
     */
    public function getStatusDocumentById($documentId)
    {
        $url = Config::getBaseUrl($this->testMode) . self::SEND_CHECK_DATA_URI . '/' . $documentId . '/status';

        return $this->send('GET', $url);
    }

    /**
     * Получение JWT
     */
    public function authToken()
    {
        $url = Config::getBaseUrl($this->testMode) . self::LOGIN_URI;

        $response = $this->send('POST', $url, [
            'username' => $this->login,
            'password' => $this->password
        ], true);

        $this->token = $response['access_token'];
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $data
     * @param bool $login
     *
     * @return array
     */
    private function send(string $method, string $url, array $data = [], bool $login = false): array
    {
        $authParams = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => $data
        ];

        if (!$login) {
            $authParams['headers']['Authorization'] = "Bearer " . $this->token;
        }

        $response = $this->client->request($method, $url, $authParams);

        return json_decode($response->getBody()->getContents(), true);
    }
}