<?php
/**
 * Created by PhpStorm.
 * User: krishansharma01
 * Date: 9/20/2017
 * Time: 10:41 AM
 */

namespace AppBundle\Controller;


use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class UploadPictureConsumer implements ConsumerInterface
{

        // Getting here means the picture has successfully been uploaded.
    private $logger; // Monolog-logger.

    // Init:
    public function __construct(  )
    {
        $this->logger = new Logger('name');
        echo "testclass is listening...";
    }

    public function execute(AMQPMessage $msg)
    {
        $message = unserialize($msg->body);
        $userid = $message['userid'];
        echo $userid;
        // Do something with the data. Save to db, write a log, whatever.
    }

}