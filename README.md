yii2-amqp
=========

AMQP extension wrapper to communicate with RabbitMQ server. Based on [videlalvaro/php-amqplib](https://github.com/videlalvaro/php-amqplib).

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require webtoucher/yii2-amqp "*"
```

or add

```
"webtoucher/yii2-amqp": "*"
```

to the ```require``` section of your `composer.json` file.

Add the following in your console config:

```php
return [
    ...
    'components' => [
        ...
        'amqp' => [
            'class' => 'webtoucher\amqp\components\Amqp',
            'host' => '127.0.0.1',
            'port' => 5672,
            'user' => 'your_login',
            'password' => 'your_password',
            'vhost' => '/',
        ],
        ...
    ],
    ...
];
```

## Usage

Just use for your web controllers class `webtoucher\amqp\controllers\AmqpConsoleController` instead of
`yii\web\Controller` and for your console controllers class `webtoucher\amqp\controllers\AmqpConsoleController`
instead of `yii\console\Controller`. AMQP connection will be available with property `connection`. AMQP channel
will be available with property `channel`.

Example:

```php
<?php

namespace app\commands;

use PhpAmqpLib\Message\AMQPMessage;
use webtoucher\amqp\controllers\AmqpConsoleController;


class RabbitController extends AmqpConsoleController
{
    public function actionRun() {
        $this->amqp->listen('my_exchange', '#', function (AMQPMessage $msg) {
            $this->process($msg);
        });
    }

    protected function process(AMQPMessage $msg) {
        $message = json_decode($msg->body, true);
        // todo: write message handler
        print_r($message);
    }
}
```

You can start listening by follow command:

```bash
$ php yii rabbit
```
