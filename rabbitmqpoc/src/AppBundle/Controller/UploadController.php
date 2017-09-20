<?php
/**
 * Created by PhpStorm.
 * User: krishansharma01
 * Date: 9/20/2017
 * Time: 10:39 AM
 */

namespace AppBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UploadController extends Controller
{
    public function uploadAction(Request $request)
    {
            // Generate a path
            // Store the image
//        $message = ['user_id' => 1235];
//        $this->get('old_sound_rabbit_mq.upload_picture_producer')->publish(json_encode($message));
        for ($x = 0; $x <= 10; $x++) {
            $msg = array('userid' => $x);
            $this->get('old_sound_rabbit_mq.upload_picture_producer')->publish(serialize($msg));
        }
        $msg = array('userid' => 12345);
        $this->get('old_sound_rabbit_mq.upload_picture_producer')->publish(serialize($msg));
    }

}