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
       // echo "testclass is listening...";
    }

    public function execute(AMQPMessage $msg)
    {
        $message = unserialize($msg->body);
        $userid = $message['userid'];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_PORT => "8003",
            CURLOPT_URL => "http://rabbitmqapi:8000/rabbit/?msg=".$userid,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",

            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        // Do something with the data. Save to db, write a log, whatever.
    }

}