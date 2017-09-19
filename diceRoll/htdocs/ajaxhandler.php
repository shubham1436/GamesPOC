<?php

$serviceName = $_REQUEST['serviceName'];
$reqType = $_SERVER['REQUEST_METHOD'] === 'POST'? 'POST': 'GET';//$_REQUEST['reqType'];

function callAPI($url,$request) {
    $context  = stream_context_create($request);
    $resp = file_get_contents($url, false, $context);
    if ($resp === FALSE) { 
        return 'Error in getting response';
    }
    return $resp;
}

function loginServiceHandler($data) {
    $url = 'http://127.0.0.1:8000/user/';

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );

    echo callAPI($url,$options);
}

function sendUserGetRequest($url) {
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'GET'
        )
    );

    echo callAPI($url,$options);
}

function getServiceHandler($url) {
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'GET'
        )
    );
    
    echo callAPI($url, $options);
}

function postServiceHandler($url, $data) {
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    
    echo callAPI($url, $options);
}

switch($serviceName) {
    case 'userMgmt':
        $url = "<?php echo USER_API_URL; ?>";
        if($reqType === 'POST') {
            $data = $_POST;
           
            postServiceHandler($url, $data);
        } else if($reqType === 'GET') {

          
            $url = $url.$_GET['id'];
           
            getServiceHandler($url);
        }
        break;
      case 'gameApi':
          $url="<?php echo GAME_API_URL; ?>";
          if($reqType=='POST'){
               if($_GET['functionName']==='replay'){
                    $url = $url.$_REQUEST['functionName'];
                    $url=$url."/";
                    $url=$url.$_REQUEST['sessionid'];
                    $data=[];
                    postServiceHandler($url,$data);
                    
                }else if($_GET['functionName']==='credit'){
                     $url = $url.$_REQUEST['functionName'];
                    $url=$url."/";
                    $url=$url.$_REQUEST['id'];
                     $data=[];
                     postServiceHandler($url,$data);
                }
             
          }  else if($reqType==='GET'){
              
                if($_GET['functionName']==='maxcount'){
                    $url = $url.$_GET['functionName'];
                     getServiceHandler($url);
                }else if($_GET['functionName']==='randomnumber'){
                    $url=$url.$_GET['functionName'];
                    $url=$url."/?userid=";
                    $url=$url.$_GET['userid'];
                    $url=$url."&sessionid=";
                    $url=$url.$_GET['sessionid'];
                   
                    getServiceHandler($url);
                }
                
          }
}
?>
