<?php
/**
 * This file is part of OnlineReceipt package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brandshopru\OnlineReceiptApiClient\Contracts;

/**
 * Interface OnlineReceiptOrderItemInterface.
 */
interface OnlineReceiptCashierInterface
{
    /**
     * Имя кассира.
     *
     * @return string|null
     */
    public function getName();

    /**
     * ИНН кассира (используется валидатор ИНН).
     *
     * @return float
     */
    public function getInn();

    /**
     * Должность кассира (до 100 символов).
     *
     * @return int
     */
    public function getPosition();
}