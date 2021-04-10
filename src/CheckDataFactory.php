<?php
/**
 * This file is part of OnlineReceipt package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brandshopru\OnlineReceiptApiClient;

use Brandshopru\OnlineReceiptApiClient\Contracts\OnlineReceiptCashierInterface;
use Brandshopru\OnlineReceiptApiClient\Contracts\OnlineReceiptOrderInterface;
use Brandshopru\OnlineReceiptApiClient\Exceptions\ItemsNotFound;
use Brandshopru\OnlineReceiptApiClient\Exceptions\RequiredParameterNotFound;

/**
 * Class CheckDataFactory.
 *
 * Фабрика преобразует объект интерфейса OnlineReceiptOrderInterface в массив
 * который можно использовать для отправки данных в API
 */
class CheckDataFactory
{
    /**
     * @param OnlineReceiptOrderInterface $order
     * @param null $responseUrl URL для подтверждения успешной фискализации на стороне Интернет-магазина
     * @param bool $printReceipt Печатать ли бумажный чек на кассе при фискализации
     * @param OnlineReceiptCashierInterface $cashier Информация о кассире
     *
     * @return array
     */
    public static function convertToArray(
        OnlineReceiptOrderInterface $order,
        $responseUrl = null,
        $printReceipt = false,
        $cashier = null
    )
    {
        self::validate($order);

        $checkData = [
            'id' => $order->getDocumentUuid(),
            'checkoutDateTime' => $order->getCheckoutDateTime(),
            'docNum' => $order->getOrderId(),
            'docType' => $order->getTypeOperation(),
            'printReceipt' => $printReceipt,
            'responseURL' => $responseUrl,
            'email' => $order->getCustomerContact(),
        ];

        if ($cashier) {
            $checkData['cashierName'] = $cashier->getName();
            $checkData['cashierInn'] = $cashier->getInn();
            $checkData['cashierPosition'] = $cashier->getPosition();
        }

        foreach ($order->getItems() as $item) {
            /** @var \Brandshopru\OnlineReceiptApiClient\Contracts\OnlineReceiptOrderItemInterface $item */
            $itemData = [
                'name' => $item->getName(),
                'price' => $item->getPrice(),
                'quantity' => $item->getQuantity(),
                'vatTag' => $item->getVatTag(),
                'paymentObject' => $item->getPaymentObject(),
                'paymentMethod' => $item->getPaymentMethod(),
            ];
            if ($item->getDiscSum() !== false) {
                $itemData['discSum'] = $item->getDiscSum();
            }
            if ($item->getNomenclatureCode() !== false) {
                $itemData['nomenclatureCode'] = $item->getNomenclatureCode();
            }
            $checkData['inventPositions'][] = $itemData;
        }

        foreach ($order->getPaymentItems() as $paymentItem) {
            /** @var \Brandshopru\OnlineReceiptApiClient\Contracts\OnlineReceiptPaymentItemInterface $paymentItem */
            $paymentItemData = [
                'paymentType' => $paymentItem->getType(),
                'sum' => $paymentItem->getSum(),
            ];

            $checkData['moneyPositions'][] = $paymentItemData;
        }

        return $checkData;
    }

    /**
     * @param \Brandshopru\OnlineReceiptApiClient\Contracts\OnlineReceiptOrderInterface $order
     *
     * @throws \Brandshopru\OnlineReceiptApiClient\Exceptions\ItemsNotFound
     * @throws \Brandshopru\OnlineReceiptApiClient\Exceptions\RequiredParameterNotFound
     */
    private static function validate(OnlineReceiptOrderInterface $order)
    {
        if (!$order->getDocumentUuid()) {
            throw new RequiredParameterNotFound('documentUuid is required');
        }

        if (!$order->getCheckoutDateTime()) {
            throw new RequiredParameterNotFound('checkoutDateTime is required');
        }

        if (!$order->getOrderId()) {
            throw new RequiredParameterNotFound('orderId is required');
        }

        if (!$order->getTypeOperation()) {
            throw new RequiredParameterNotFound('typeOperation is required');
        }

        if (!$order->getCustomerContact()) {
            throw new RequiredParameterNotFound('customerContact is required');
        }

        $items = $order->getItems();

        if (empty($items)) {
            throw new ItemsNotFound('orderItems is required');
        }

        foreach ($items as $item) {
            /** @var \Brandshopru\OnlineReceiptApiClient\Contracts\OnlineReceiptOrderItemInterface $item */
            if (!$item->getPrice()) {
                throw new RequiredParameterNotFound('price in orderItem is required');
            }
            if (!$item->getVatTag()) {
                throw new RequiredParameterNotFound('vatTag in orderItem is required');
            }
            if (!$item->getQuantity()) {
                throw new RequiredParameterNotFound('quantity in orderItem is required');
            }
            if (!$item->getName()) {
                throw new RequiredParameterNotFound('name in orderItem is required');
            }
        }

        $paymentItems = $order->getPaymentItems();

        if (empty($paymentItems)) {
            throw new ItemsNotFound('paymentItems is required');
        }

        foreach ($paymentItems as $paymentItem) {
            /** @var \Brandshopru\OnlineReceiptApiClient\Contracts\OnlineReceiptPaymentItemInterface $paymentItem */
            if (!$paymentItem->getType()) {
                throw new RequiredParameterNotFound('type in paymentItem is required');
            }
            if (!$paymentItem->getSum()) {
                throw new RequiredParameterNotFound('sum in paymentItem is required');
            }
        }
    }
}