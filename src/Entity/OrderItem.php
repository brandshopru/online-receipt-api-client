<?php
/**
 * This file is part of OnlineReceipt package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brandshopru\OnlineReceiptApiClient\Entity;

use Brandshopru\OnlineReceiptApiClient\Contracts\OnlineReceiptOrderItemInterface;
use Brandshopru\OnlineReceiptApiClient\Exceptions\PaymentMethodNotAllowed;
use Brandshopru\OnlineReceiptApiClient\Exceptions\PaymentObjectNotAllowed;
use Brandshopru\OnlineReceiptApiClient\Exceptions\VatTagNotAllowed;

/**
 * Class OrderItem.
 */
class OrderItem extends AbstractEntity implements OnlineReceiptOrderItemInterface
{
    /**
     * @var float
     */
    protected $discSum;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var string
     */
    protected $vatTag;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $paymentObject = 'commodity';

    /**
     * @var string
     */
    protected $paymentMethod = 'full_payment';

    /**
     * @var array
     */
    protected $allowedVatTags = [
        self::VAT_NO,
        self::VAT_0,
        self::VAT_10,
        self::VAT_18,
        self::VAT_20,
        self::VAT_10_110,
        self::VAT_18_118,
        self::VAT_20_120,
    ];

    /**
     * @var array
     */
    protected $allowedPaymentObject = [
        'commodity',
        'excise',
        'job',
        'service',
        'gambling_bet',
        'gambling_prize',
        'lottery',
        'lottery_prize',
        'intellectual_activity',
        'payment',
        'agent_commission',
        'composite',
        'another',
    ];

    /**
     * @var array
     */
    protected $allowedPaymentMethod = [
        'full_prepayment',
        'prepayment',
        'advance',
        'full_payment',
        'partial_payment',
        'credit',
        'credit_payment',
    ];

    /**
     * @var string
     */
    protected $nomenclatureCode;

    /**
     * @return float
     */
    public function getDiscSum()
    {
        return $this->discSum;
    }

    /**
     * @param float $discSum
     */
    public function setDiscSum($discSum)
    {
        $this->discSum = $discSum;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getVatTag()
    {
        return $this->vatTag;
    }

    /**
     * @param int $vatTag
     *
     * @throws \Brandshopru\OnlineReceiptApiClient\Exceptions\VatTagNotAllowed
     */
    public function setVatTag($vatTag)
    {
        if (!in_array($vatTag, $this->allowedVatTags)) {
            throw new VatTagNotAllowed("$vatTag is not allowed");
        }

        $this->vatTag = $vatTag;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPaymentObject()
    {
        return $this->paymentObject;
    }

    /**
     * @param $paymentObject
     *
     * @throws PaymentObjectNotAllowed
     */
    public function setPaymentObject($paymentObject)
    {
        if (!in_array($paymentObject, $this->allowedPaymentObject)) {
            throw new PaymentObjectNotAllowed("$paymentObject is not allowed");
        }

        $this->paymentObject = $paymentObject;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param $paymentMethod
     *
     * @throws PaymentMethodNotAllowed
     */
    public function setPaymentMethod($paymentMethod)
    {
        if (!in_array($paymentMethod, $this->allowedPaymentMethod)) {
            throw new PaymentMethodNotAllowed("$paymentMethod is not allowed");
        }

        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return string
     */
    public function getNomenclatureCode()
    {
        return $this->nomenclatureCode;
    }

    /**
     * @param $nomenclatureCode
     */
    public function setNomenclatureCode($nomenclatureCode)
    {
        $this->nomenclatureCode = $nomenclatureCode;
    }
}