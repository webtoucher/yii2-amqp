<?php
/**
 * @link https://github.com/webtoucher/yii2-amqp
 * @copyright Copyright (c) 2014 webtoucher
 * @license https://github.com/webtoucher/yii2-amqp/blob/master/LICENSE.md
 */

namespace webtoucher\amqp\controllers;

use webtoucher\amqp\components\AmqpTrait;
use webtoucher\commands\Controller;


/**
 * AMQP console controller.
 *
 * @author Alexey Kuznetsov <mirakuru@webtoucher.ru>
 * @since 2.0
 */
abstract class AmqpConsoleController extends Controller
{
    use AmqpTrait;

    /**
     * @inheritdoc
     */
    public function options($actionId)
    {
        return array_merge(
            parent::options($actionId),
            ['exchange']
        );
    }
}
