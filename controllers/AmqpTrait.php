<?php
/**
 * @link https://github.com/webtoucher/yii2-amqp
 * @copyright Copyright (c) 2014 webtoucher
 * @license https://github.com/webtoucher/yii2-amqp/blob/master/LICENSE.md
 */

namespace webtoucher\amqp\controllers;

use Yii;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnection;
use webtoucher\amqp\components\Amqp;
use webtoucher\commands\Controller;


/**
 * AMQP trait for controllers.
 *
 * @property AMQPConnection $connection AMQP connection.
 * @property AMQPChannel $channel AMQP channel.
 * @author Alexey Kuznetsov <mirakuru@webtoucher.ru>
 * @since 2.0
 */
trait AmqpTrait
{
    /**
     * @var Amqp
     */
    public $amqp;

    /**
     * Returns AMQP connection.
     *
     * @return AMQPConnection
     */
    public function getConnection()
    {
        if (empty($this->amqp)) {
            $this->amqp = Yii::$app->amqp;
        }
        return $this->amqp->getConnection();
    }

    /**
     * Returns AMQP channel.
     *
     * @param string $channel_id
     * @return AMQPChannel
     */
    public function getChannel($channel_id = null)
    {
        if (empty($this->amqp)) {
            $this->amqp = Yii::$app->amqp;
        }
        return $this->amqp->getChannel($channel_id);
    }
}
