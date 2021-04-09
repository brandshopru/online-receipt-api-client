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

namespace Brandshopru\OnlineReceiptApiClient\Entity;

use Brandshopru\OnlineReceiptApiClient\Contracts\OnlineReceiptOrderInterface;
use Brandshopru\OnlineReceiptApiClient\Exceptions\TypeOperationsNotAllowed;

/**
 * Class Order.
 */
class Order extends AbstractEntity implements OnlineReceiptOrderInterface
{
    protected $allowedTypeOperations = [
        'SALE',
        'RETURN',
    ];

    /**
     * @var string
     */
    protected $documentUuid;

    /**
     * @var string
     */
    protected $orderId;

    /**
     * @var string
     */
    protected $customerContact;

    /**
     * @var string
     */
    protected $typeOperation;

    /**
     * @var iterable OnlineReceiptOrderItemInterface
     */
    protected $items;

    /**
     * @var iterable OnlineReceiptPaymentItemInterface
     */
    protected $paymentItems;

    /**
     * @var string
     */
    protected $checkoutDateTime;

    /**
     * @return string
     */
    public function getDocumentUuid()
    {
        return $this->documentUuid;
    }

    /**
     * @param string $documentUuid
     */
    public function setDocumentUuid($documentUuid)
    {
        $this->documentUuid = $documentUuid;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getCustomerContact()
    {
        return $this->customerContact;
    }

    /**
     * @param string $customerContact
     */
    public function setCustomerContact($customerContact)
    {
        $this->customerContact = $customerContact;
    }

    /**
     * @return string
     */
    public function getTypeOperation()
    {
        return $this->typeOperation;
    }

    /**
     * @param string $typeOperation
     *
     * @throws \Brandshopru\OnlineReceiptApiClient\Exceptions\TypeOperationsNotAllowed
     */
    public function setTypeOperation($typeOperation)
    {
        if (!in_array($typeOperation, $this->allowedTypeOperations)) {
            throw new TypeOperationsNotAllowed("$typeOperation is not allowed");
        }

        $this->typeOperation = $typeOperation;
    }

    /**
     * @return iterable
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param OrderItem $item
     */
    public function addItem(OrderItem $item)
    {
        $this->items[] = $item;
    }

    /**
     * @return iterable
     */
    public function getPaymentItems()
    {
        return $this->paymentItems;
    }

    /**
     * @param PaymentItem $paymentItem
     */
    public function addPaymentItem($paymentItem)
    {
        $this->paymentItems[] = $paymentItem;
    }

    /**
     * @return string
     */
    public function getCheckoutDateTime()
    {
        return $this->checkoutDateTime;
    }

    /**
     * @param string $checkoutDateTime
     */
    public function setCheckoutDateTime($checkoutDateTime)
    {
        $this->checkoutDateTime = $checkoutDateTime;
    }
}