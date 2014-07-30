<?php
/**
 * @link https://github.com/webtoucher/yii2-amqp
 * @copyright Copyright (c) 2014 webtoucher
 * @license https://github.com/webtoucher/yii2-amqp/blob/master/LICENSE.md
 */

namespace webtoucher\amqp\controllers;

use PhpAmqpLib\Message\AMQPMessage;
use yii\console\Exception;
use webtoucher\amqp\components\AmqpInterpreter;
use webtoucher\amqp\components\AmpqInterpreterInterface;
use webtoucher\commands\Controller;
use yii\helpers\Inflector;


/**
 * AMQP listener controller.
 *
 * @author Alexey Kuznetsov <mirakuru@webtoucher.ru>
 * @since 2.0
 */
class AmqpListenerController extends AmqpConsoleController
{
    /**
     * Interpreter class for AMQP messages. Empty value means this class.
     *
     * @var string
     */
    public $interpreter;

    /**
     * Listened exchange.
     *
     * @var string
     */
    public $exchange = 'exchange';

    public function actionRun($routingKey = '#')
    {
        $this->amqp->listen($this->exchange, $routingKey, function (AMQPMessage $msg) {
            $routingKey = $msg->delivery_info['routing_key'];
            $method = 'read' . Inflector::camelize($routingKey);

            if (empty($this->interpreter)) {
                $interpreter = $this;
            } elseif (class_exists($this->interpreter)) {
                $interpreter = new $this->interpreter;
                if (!$interpreter instanceof AmqpInterpreter) {
                    throw new Exception(sprintf("Class '%s' is not correct interpreter class.", $this->interpreter));
                }
            } else {
                throw new Exception(sprintf("Interpreter class '%s' was not found.", $this->interpreter));
            }

            if (method_exists($interpreter, $method)) {
                $interpreter->$method(json_decode($msg->body, true));
            } else {
                if (empty($this->interpreter)) {
                    $interpreter = new AmqpInterpreter();
                }
                $interpreter->log(
                    sprintf("Unknown routing key '%s' for exchange '%s'.", $routingKey, $this->exchange),
                    self::MESSAGE_ERROR
                );
            }
        });
    }
}
