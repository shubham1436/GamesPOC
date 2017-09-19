<?php
/**
 * Created by PhpStorm.
 * User: krishansharma01
 * Date: 8/23/2017
 * Time: 1:55 PM
 */
namespace AppBundle\Controller\Game;
use AppBundle\Entity\GamePlay;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\DiceGamePlay;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Validator\Constraints\DateTime;

class GameController extends FOSRestController
{
    /**
     * @Rest\Post("/game/")
     */
    public function postAction(Request $request)
    {
        $data = new DiceGamePlay;
        $userid = $request->get('userid');


        if(empty($userid))
        {
            return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);
        }
        $data->setUserid($userid);
        $data->setCredit(20);
        $data->setSessionid("null");
        $data->setScore(0);
        $data->setCount(0);


        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();




        return new View($data,Response::HTTP_OK);

    }

    /**
     * @Rest\Get("/game/")
     */
    public function getAction()
    {
        $restresult = $this->getDoctrine()->getRepository('AppBundle:DiceGamePlay')->findAll();
        if ($restresult === null) {
            return new View("there are no users exist", Response::HTTP_NOT_FOUND);
        }
        return $restresult;

    }

    /**
     * @Rest\Get("/game/maxcount")
     */
    public function getCount()
    {
        return 4;

    }
    
    /** 
    * @Rest\Get("/game/randomnumber/")
    */
    public function idAction(Request $request)
    {
        $userid=$request->get('userid');
        $sessionid=$request->get('sessionid');
        $repository=$this->getDoctrine()->getRepository(('AppBundle:DiceGamePlay'));
        $service = $repository->findBy(array('userid' => $userid),array('id' => 'DESC'),1 ,0)[0];
        if ($service === null) {
            $service = $repository->findBy(array('userid' => $userid,'sessionid'=>$sessionid),array('userid' => 'ASC'),1 ,0)[0];
        }
        $number=rand(1,6);
        if($service->getCount()==4 && $service->getSessionid()==$sessionid){
            $data = [
                'message'=>"Success",
                'count'=>-1,//$service->getCount(),
                'number'=>'NULL',
                'credit' => $service->getCredit(),
                'score' => $service->getScore(),
            ];
            return new View($data,Response::HTTP_OK);

        }
        if($service->getCredit()==0 && $service->getSessionid()==$sessionid){
            $data = [
                'message'=>"Success",
                'count'=>$service->getCount(),
                'number'=>'NULL',
                'credit' => $service->getCredit(),
                'score' => $service->getScore(),
            ];
            return new View($data,Response::HTTP_OK);
        }

        /**
         * TBD: to put value in rabbit MQ
         */
        if ($service->getScore()+$number>250){
            $data = [
                'message'=>"Success",
                'count'=>$service->getCount(),
                'number'=>'NULL',
                'credit' => $service->getCredit(),
                'score' => $service->getScore(),
            ];

            return new View($data,Response::HTTP_OK);
        }
        /* END*/
        if($service->getSessionid()!= $sessionid ){
            //create check if sessionid and username exists in DB else create new entry in table with userid and session id
            $newEntry = new DiceGamePlay;
            $newEntry->setUserid($userid);
            $newEntry->setCount(1);
            $newEntry->setCredit(15);

            $newEntry->setSessionid($sessionid);
            $newEntry->setScore($number);
            $em = $this->getDoctrine()->getManager();
            $em->persist($newEntry);
            $em->flush();

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_PORT => "8000",
                CURLOPT_URL => "http://".$this->getParameter('IpAddress')."/userupdate/?userid=".$userid."&credit=".$newEntry->getCredit(),
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
            $data = [
                'message'=>"Success",
                'count'=>$newEntry->getCount(),
                'number'=>$number,
                'credit' => $newEntry->getCredit(),
                'score' => $newEntry->getScore(),
            ];





            return new View($data, Response::HTTP_OK);
        }
        else {


            $newCredit = $service->getCredit() - 5;

            $newScore = $service->getScore() + $number;
            $data = new DiceGamePlay;
            $service->setCredit($newCredit);
            $service->setScore($newScore);
            $service->setCount($service->getCount()+1);
            $service->setSessionid($sessionid);
            $em = $this->getDoctrine()->getManager();
            $em->persist($service);
            $em->flush();

            //to update user table

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_PORT => "8000",
                CURLOPT_URL => "http://".$this->getParameter('IpAddress')."/userupdate/?userid=".$userid."&credit=".$newCredit,
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
            $data = [
                'message'=>"Success",
                'count'=>$service->getCount(),
                'number'=>$number,
                'credit' => $service->getCredit(),
                'score' => $service->getScore(),
            ];

            return new View($data, Response::HTTP_OK);
        }

    }

    /**
     * @Rest\Get("/game/score/{userid}")
     */
    public  function getScore($userid){
        $repository=$this->getDoctrine()->getRepository(('AppBundle:DiceGamePlay'));
        $service = $repository->findBy(array('userid' => $userid),array('userid' => 'ASC'),1 ,0)[0];
        if ($service === null) {
            return new View("user not found", Response::HTTP_NOT_FOUND);
        }
        $data = [
            'message'=>"Success",

            'score' => $service->getScore(),
        ];
        return new View($data,Response::HTTP_OK );
    }

    /**
     * @Rest\Post("/game/replay/{sessionid}")
     */
    public  function gameReplay($sessionid){
        $repository=$this->getDoctrine()->getRepository(('AppBundle:DiceGamePlay'));
        $service = $repository->findBy(array('sessionid' => $sessionid),array('id' => 'DESC'),1 ,0)[0];
        if ($service === NULL) {
            return new View("Session not found", Response::HTTP_NOT_FOUND);
        }
        $service->setScore(0);    
        $em = $this->getDoctrine()->getManager();
        $em->persist($service);
        $em->flush();
        $data = [
            'message'=>"Score Reset",
            'credit' => $service->getCredit(),
        ];
        return new View($data,Response::HTTP_OK );
    }

    /**
     * @Rest\Post("/game/credit/{userid}")
     */
    public  function addCredit($userid){
        $repository=$this->getDoctrine()->getRepository(('AppBundle:DiceGamePlay'));
        $service = $repository->findBy(array('userid' => $userid),array('id' => 'DESC'),1 ,0)[0];
        if ($service === NULL) {
            return new View("user not found", Response::HTTP_NOT_FOUND);
        }
        $service->setCredit($service->getCredit()+20);
        $service->setCount(0);
        $em = $this->getDoctrine()->getManager();
        $em->persist($service);
        $em->flush();
        $data = [
            'message'=>"Credit Added",
            // 'Çount'=>$service->getCount(),
            'credit' => $service->getCredit(),
            'number'=>'NULL'
            //   'Score' => $service->GetScore(),
        ];
          //to update user table
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_PORT => "8000",
                CURLOPT_URL => "http://".$this->getParameter('IpAddress')."/userupdate/?userid=".$userid."&credit=".$data['credit'],
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
        return new View($data,Response::HTTP_OK );

    }
}
