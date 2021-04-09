<?php
/**
 * This file is part of OnlineReceipt package.
 *
 *
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brandshopru\OnlineReceiptApiClient\Contracts;

/**
 * Interface ClientInterface.
 */
interface ClientInterface
{
    /**
     * Опрос готовности сервиса фискализации.
     *
     * @return array ['status', 'statusDateTime']
     */
    public function getStatusFiscalService();

    /**
     * Отправка данных чека на сервер фискализации (создание документа).
     *
     * @param OnlineReceiptOrderInterface $order
     *
     * @return array
     */
    public function sendCheck(OnlineReceiptOrderInterface $order);

    /**
     * Проверка статуса документа.
     *
     * @param $documentId
     *
     * @return array
     */
    public function getStatusDocumentById($documentId);
}