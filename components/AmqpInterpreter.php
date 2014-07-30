<?php
/**
 * @link https://github.com/webtoucher/yii2-amqp
 * @copyright Copyright (c) 2014 webtoucher
 * @license https://github.com/webtoucher/yii2-amqp/blob/master/LICENSE.md
 */

namespace webtoucher\amqp\components;

use yii\helpers\Console;


/**
 * AMQP interpreter class.
 *
 * @author Alexey Kuznetsov <mirakuru@webtoucher.ru>
 * @since 2.0
 */
class AmqpInterpreter
{
    const MESSAGE_INFO = 0;
    const MESSAGE_ERROR = 1;

    /**
     * Logs info and error messages.
     *
     * @param $message
     * @param $type
     */
    public function log($message, $type = self::MESSAGE_INFO) {
        $format = [$type == self::MESSAGE_ERROR ? Console::FG_RED : Console::FG_BLUE];
        Console::stdout(Console::ansiFormat($message . PHP_EOL, $format));
    }
}