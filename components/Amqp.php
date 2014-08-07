<?php
/**
 * @link https://github.com/webtoucher/yii2-amqp
 * @copyright Copyright (c) 2014 webtoucher
 * @license https://github.com/webtoucher/yii2-amqp/blob/master/LICENSE.md
 */

namespace webtoucher\amqp\components;

use yii\base\Component;
use yii\base\Exception;
use yii\helpers\Inflector;
use yii\helpers\Json;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;


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
     * @var AMQPChannel[]
     */
    protected $channels = [];

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
        $index = $channel_id ?: 'default';
        if (!array_key_exists($index, $this->channels)) {
            $this->channels[$index] = $this->connection->channel($channel_id);
        }
        return $this->channels[$index];
    }

    /**
     * Sends message to the exchange.
     *
     * @param string $exchange
     * @param string $routing_key
     * @param string|array|AMQPMessage $message
     * @return void
     */
    public function send($exchange, $routing_key, $message) {
        if (empty($message)) {
            throw new Exception('AMQP message can not be empty');
        }
        if (is_array($message)) {
            $message = Json::encode($message);
        }
        if (is_string($message)) {
            $message = new AMQPMessage($message);
        }
        $this->channel->basic_publish($message, $exchange, $routing_key);

        $this->channel->close();
        $this->connection->close();
    }

    /**
     * Listens the exchange for messages.
     *
     * @param string $exchange
     * @param string $routing_key
     * @param callable $callback
     */
    public function listen($exchange, $routing_key, $callback) {
        list($queueName) = $this->channel->queue_declare();
        $this->channel->queue_bind($queueName, $exchange, $routing_key);
        $this->channel->basic_consume($queueName, '', false, true, false, false, $callback);
        while(count($this->channel->callbacks)) {
            $this->channel->wait();
        }

        $this->channel->close();
        $this->connection->close();
    }
}
