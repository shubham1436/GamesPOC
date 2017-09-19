<?php
namespace AppBundle\Controller\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\User;
use Symfony\Component\Validator\Constraints\DateTime;

class UserController extends FOSRestController
{
    /**
     * @Rest\Post("/user/")
     */
    public function postAction(Request $request)
    {
        $data = new User;
        $name = $request->get('name');

        if(empty($name))
        {
            return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);
        }
        $data->setName($name);
       $data->setCredit(20);

       $data->setCreated(new \Datetime());

       $data->setLastlogin(new \Datetime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_PORT => "8080",
            CURLOPT_URL => "http://".$this->getParameter('IpAddress')."/game/?userid=".$data->getId(),
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


        return new View($data,Response::HTTP_OK);

 }

    /**
     * @Rest\Get("/user/")
     */
    public function getAction()
    {
        $restresult = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        if ($restresult === null) {
            return new View("there are no users exist", Response::HTTP_NOT_FOUND);
        }
        return $restresult;
    }

    /**
     * @Rest\Get("/user/{id}")
     */
    public function idAction($id)
    {
        $singleresult = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if ($singleresult === null) {
            return new View("user not found", Response::HTTP_NOT_FOUND);
        }
        return $singleresult;
    }

    /**
     * @Rest\Post("/userupdate/")
     */
    public function useridAction(Request $request)
    {

        $userid = $request->get('userid');
        $credit=$request->get('credit');
        $singleresult = $this->getDoctrine()->getRepository('AppBundle:User')->find($userid);
        if ($singleresult === null) {
            return new View("user not found", Response::HTTP_NOT_FOUND);
        }
        else{
            $singleresult->setCredit($credit);
            $em = $this->getDoctrine()->getManager();
            $em->persist($singleresult);
            $em->flush();

        }

        return new View($singleresult,Response::HTTP_OK);
    }
}
