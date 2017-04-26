yii2-amqp
=========

AMQP extension wrapper to communicate with RabbitMQ server. Based on [php-amqplib/php-amqplib](https://github.com/php-amqplib/php-amqplib).

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
    'controllerMap' => [
        ...
        'rabbit' => [
            'class' => 'webtoucher\amqp\controllers\AmqpListenerController',
            'interpreters' => [
                'my-exchange' => 'app\components\RabbitInterpreter', // interpreters for each exchange
            ],
            'exchange' => 'my-exchange', // default exchange
        ],
        ...
    ],
    ...
];
```

Add messages interpreter class `@app/components/RabbitInterpreter` with your handlers for different routing keys:

```php
<?php

namespace app\components;

use webtoucher\amqp\components\AmqpInterpreter;


class RabbitInterpreter extends AmqpInterpreter
{
    /**
     * Interprets AMQP message with routing key 'hello_world'.
     *
     * @param array $message
     */
    public function readHelloWorld($message)
    {
        // todo: write message handler
        $this->log(print_r($message, true));
    }
}
```

## Usage

Just run command

```bash
$ php yii rabbit
```

to listen topics with any routing keys on default exchange or

```bash
$ php yii rabbit my_routing_key
```

to listen topics with one routing key.

Run command

```bash
$ php yii rabbit my_routing_key direct --exchange=my_exchange
```

to listen direct messages on selected exchange.

Also you can create controllers for your needs. Just use for your web controllers class
`webtoucher\amqp\controllers\AmqpConsoleController` instead of `yii\web\Controller` and for your console controllers
class `webtoucher\amqp\controllers\AmqpConsoleController` instead of `yii\console\Controller`. AMQP connection will be
available with property `connection`. AMQP channel will be available with property `channel`.


## Example usage round robin

```php
<?php
namespace console\controllers;

use webtoucher\amqp\components\Amqp;
use Yii;
use yii\console\Controller;

class ExampleController extends Controller
{
	public function actionIndex($message = 'Wasap Man')
	{
		Yii::$app->amqp->send('my-exchange', 'HelloWorld', $message, Amqp::TYPE_DIRECT, true);
	}
}
```

run same listen consumers

```bash
1st$ php yii rabbit HelloWorld direct true
```

```bash
2nd$ php yii rabbit HelloWorld direct true
```

send message from console controller

```bash
$ php yii example 
```

or

```bash
$ php yii example "same text"
```
