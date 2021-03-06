<?php
/**
 * This file is part of OnlineReceipt package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brandshopru\OnlineReceiptApiClient;

/**
 * Class Config.
 */
class Config
{
    /**
     * Базовый URL сервиса.
     */
    const BASE_URI = 'https://api-or.brandshop.tech/api/v1';

    /**
     * Базовый URL сервиса для тестирования.
     */
    const BASE_TEST_URI = 'https://test.api-or.brandshop.tech/api/v1';

    /**
     * Возвращает базовый URL.
     *
     * @param bool $testMode
     *
     * @return string
     */
    public static function getBaseUrl($testMode = false)
    {
        if ($testMode) {
            return self::BASE_TEST_URI;
        }

        return self::BASE_URI;
    }
}