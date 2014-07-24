<?php
/**
 * @link https://github.com/webtoucher/yii2-amqp
 * @copyright Copyright (c) 2014 webtoucher
 * @license https://github.com/webtoucher/yii2-amqp/blob/master/LICENSE.md
 */

namespace webtoucher\amqp\components;

use yii\base\Component;
use yii\base\Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use yii\helpers\Inflector;


/**
 * AMQP wrapper.
 *
 * @property AMQPConnection $connection AMQP connection.
 * @property AMQPChannel $channel AMQP channel.
 * @author Alexey Kuznetsov <mirakuru@webtoucher.ru>
 * @since 2.0
 */
class Amqp extends Component
{
    /**
     * @var AMQPConnection
     */
    protected static $ampqConnection;

    /**
     * @var string
     */
    public $host = '127.0.0.1';

    /**
     * @var integer
     */
    public $port = 5672;

    /**
     * @var string
     */
    public $user;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $vhost = '/';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (empty($this->user)) {
            throw new Exception("Parameter 'user' was not set for AMQP connection.");
        }
        if (empty(self::$ampqConnection)) {
            self::$ampqConnection = new AMQPConnection(
                $this->host,
                $this->port,
                $this->user,
                $this->password,
                $this->vhost
            );
        }
    }

    /**
     * Returns AMQP connection.
     *
     * @return AMQPConnection
     */
    public function getConnection()
    {
        return self::$ampqConnection;
    }

    /**
     * Returns AMQP connection.
     *
     * @param string $channel_id
     * @return AMQPChannel
     */
    public function getChannel($channel_id = null)
    {
        return $this->connection->channel($channel_id);
    }
}
