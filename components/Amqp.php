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
 * AMQP wrapper
 *
 * @method mixed close($reply_code = 0, $reply_text = "", $method_sig = array(0, 0))
 * @method mixed flow($active)
 * @method mixed access_request($realm, $exclusive = false, $passive = false, $active = false, $write = false, $read = false)
 * @method mixed exchange_declare($exchange, $type, $passive = false, $durable = false, $auto_delete = true, $internal = false, $nowait = false, $arguments = null, $ticket = null)
 * @method mixed exchange_delete($exchange, $if_unused = false, $nowait = false, $ticket = null)
 * @method mixed exchange_bind($destination, $source, $routing_key = "", $nowait = false, $arguments = null, $ticket = null)
 * @method mixed exchange_unbind($destination, $source, $routing_key = "", $arguments = null, $ticket = null)
 * @method mixed queue_bind($queue, $exchange, $routing_key = "", $nowait = false, $arguments = null, $ticket = null)
 * @method mixed queue_unbind($queue, $exchange, $routing_key = "", $arguments = null, $ticket = null)
 * @method mixed queue_declare($queue = "", $passive = false, $durable = false, $exclusive = false, $auto_delete = true, $nowait = false, $arguments = null, $ticket = null)
 * @method mixed queue_delete($queue = "", $if_unused = false, $if_empty = false, $nowait = false, $ticket = null)
 * @method mixed queue_purge($queue = "", $nowait = false, $ticket = null)
 * @method void basic_ack($delivery_tag, $multiple = false)
 * @method void basic_nack($delivery_tag, $multiple = false, $requeue = false)
 * @method mixed basic_cancel($consumer_tag, $nowait = false)
 * @method mixed basic_consume($queue = "", $consumer_tag = "", $no_local = false, $no_ack = false, $exclusive = false, $nowait = false, $callback = null, $ticket = null, $arguments = array())
 * @method null|AMQPMessage basic_get($queue = "", $no_ack = false, $ticket = null)
 * @method void basic_publish($msg, $exchange = "", $routing_key = "", $mandatory = false, $immediate = false, $ticket = null)
 * @method void batch_basic_publish($msg, $exchange = "", $routing_key = "", $mandatory = false, $immediate = false, $ticket = null)
 * @method void publish_batch()
 * @method mixed basic_qos($prefetch_size, $prefetch_count, $a_global)
 * @method mixed basic_recover($requeue = false)
 * @method void basic_reject($delivery_tag, $requeue)
 * @method mixed tx_commit()
 * @method mixed tx_rollback()
 * @method mixed confirm_select($nowait = false)
 * @method void confirm_select_ok()
 * @method mixed wait_for_pending_acks($timeout = 0)
 * @method mixed tx_select()
 * @method mixed set_return_listener($callback)
 * @method void set_nack_handler($callback)
 * @method void set_ack_handler($callback)
 * @author Alexey Kuznetsov <mirakuru@webtoucher.ru>
 * @since 2.0
 */
class Amqp extends Component
{
    /**
     * @var AMQPConnection
     */
    protected static $connection;

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
            throw new Exception("Parameter 'login' was not set for AMQP connection.");
        }
        if (empty(self::$connection)) {
            self::$connection = new AMQPConnection(
                $this->host,
                $this->port,
                $this->user,
                $this->password,
                $this->vhost
            );
        }
    }

    /**
     * @param string $channel_id
     * @return AMQPChannel
     */
    public function channel($channel_id = null)
    {
        return self::$connection->channel($channel_id);
    }

    /**
     * @inheritdoc
     */
    public function __call($name, $params)
    {
        $channel = $this->channel();
        if (method_exists($channel, $name)) {
            return call_user_func_array([$channel, $name], $params);
        } else {
            return parent::__call($name, $params);
        }
    }
}
