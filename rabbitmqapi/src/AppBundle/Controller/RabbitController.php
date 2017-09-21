<?php
/**
 * Created by PhpStorm.
 * User: krishansharma01
 * Date: 9/21/2017
 * Time: 1:11 PM
 */

namespace AppBundle\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;


class RabbitController extends FOSRestController
{
    /**
     * @Rest\Post("/rabbit/")
     */
    public function useridAction(Request $request)
    {

        $msg = $request->get('msg');


        return new View($msg,Response::HTTP_OK);
    }
}