<?php
/**
 * This file is part of OnlineReceipt package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brandshopru\OnlineReceiptApiClient\Entity;

use Brandshopru\OnlineReceiptApiClient\Exceptions\MethodNotFound;

/**
 * Class AbstractEntity.
 */
abstract class AbstractEntity
{
    /**
     * @param array $params
     *
     * @return static
     * @throws \Brandshopru\OnlineReceiptApiClient\Exceptions\MethodNotFound
     */
    public static function create(array $params)
    {
        $item = new static();
        foreach ($params as $key => $param) {
            $methodName = 'set' . $key;
            if (method_exists($item, $methodName)) {
                $item->$methodName($param);
            } else {
                throw new MethodNotFound("Method is $methodName not found");
            }
        }

        return $item;
    }
}