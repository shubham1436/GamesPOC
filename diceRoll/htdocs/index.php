<?php
include_once('config.php');
$id=$_GET['id'];

session_start();
    $session_id=session_id();
    $_SESSION['sessionid'] = $session_id;

   
?>
<html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<head>
    <title>Dice Simulator 2015</title>
    <link rel="stylesheet" href="main.css">
</head>  
<body>
 
<div class='dice'>
  <div class='dice-face'></div>
  <div class='dice-face'></div>
  <div class='dice-face'></div>
  <div class='dice-face'></div>
  <div class='dice-face'></div>
  <div class='dice-face'></div>
</div>
<input type="hidden" value="" id="dice_val"/>
  <p id="placeholder">
  </p>
  <div class="score-container">
    <div>SCORE</div>
    <span id="score"></span>
  </div>
  
  <button id="play" disabled>Roll Dice</button>
  <button id="replay" style="display:none"> Replay </button>
  <button id="buy" disabled>Buy Credits</button>
  <p id="message"></p>
 
<input type="hidden" id="maxRoll" value=""></input>
  <script src="main.js"></script>
</body>
<script type="text/javascript">
userUrl = "<?php echo USER_API_URL; ?>"
//userUrl="http://10.175.0.218:8000/user";
gameUrl= "<?php echo GAME_API_URL; ?>"
//"http://10.175.0.218:8002/game";
$(document).ready(function(){
	/** Check if **/
		// webUrl = userUrl+"/"+<?php echo $id; ?>;

    var webUrl = "/ajaxhandler.php?serviceName=userMgmt"+ '&id=' +<?php echo $id; ?>;
		$.ajax({
 			 url: webUrl,
 			 method: "GET",
 			 dataType: "json",
     	 	 success: function(data) {
     	 	 	if(data.credit > 0){
                    //alert(data.credit);
                    console.log(data);
     	 	 		$("#play").prop('disabled', false);
                    $("#buy").prop('disabled', true);
     	 	 	}
     	 	 	else{
                    $("#play").prop('disabled', true);
     	 	 		$("#buy").prop('disabled', false);
     	 	 	}
     		},
     	 	 error: function(error) {
        	 console.log(error)
     	 	}
     	});
        /**
            Store the maxRolls allowed for a game
        **/
        maxcount = gameUrl+"/maxcount";
        webUrl="/ajaxhandler.php?serviceName=gameApi"+'&functionName=maxcount';
        console.log(maxcount);
             $.ajax({
            
             url: webUrl,
             method: "GET",
             dataType: "json",
             success: function(data) {
                if(data > 0){
                  $("#maxRoll").val(data);
                }
                else{
                  $("#maxRoll").val("-1");
                }
            },
             error: function(error) {
             console.log(error)
            }
        });
        // $.ajax({
        //      url: webUrl,
        //      method: "GET",
        //      dataType: "json",
        //      success: function(data) {
        //         if(data > 0){
        //           $("#maxRoll").val(data);
        //         }
        //         else{
        //           $("#maxRoll").val("-1");
        //         }
        //     },
        //      error: function(error) {
        //      console.log(error)
        //     }
        // });

          $("#buy").click(function(){
         //webUrl = gameUrl+'/credit/'+<?php echo $id; ?>;
          webUrl="/ajaxhandler.php?serviceName=gameApi"+'&functionName=credit&id='+<?php echo $id; ?>;
         $.ajax({
             url: webUrl,
             method: "POST",
             //data: { },
             dataType: "json",
             success: function(data) {
                console.log(data);
               // $("dice_val").val() = data;
                $("#message").text("Congratulations, you just recharged your account. Begin to play another game. Click Reset");
                $('#message').fadeIn().delay(5000).fadeOut();
                $("#play").css('display','none'); //prop('disabled', false);
                $("#replay").css("display","inline-block");
                $("#replay").prop('disabled', false);

                $("#buy").prop('disabled', true);
             },
             error: function(error) {
                console.log(error)
            }
             });
        });

        $("#replay").click(function(){     
            //webUrl = gameUrl+'/replay/'+"<?php echo $_SESSION['sessionid']; ?>";
             webUrl="/ajaxhandler.php?serviceName=gameApi&functionName=replay&sessionid="+"<?php echo $_SESSION['sessionid']; ?>";
            $.ajax({
                url: webUrl,
                method: "POST",
                //data: { },
                 dataType: "json",
                success: function(data) {
                 $("#play").css('display','inline-block');
                 $("#replay").css("display","none");
                $("#play").prop('disabled','false');
                $("#buy").prop('disabled','true');
                 //$("#score").text("");
                $("#play").click();    
             },
             error: function(error) {
                console.log(error)
            }
             });
                   
        });
	$("#play").click(function(){
    //  webUrl = gameUrl+'/randomnumber/?userid='+<?php echo $id; ?>+'&sessionid='+"<?php echo $_SESSION['sessionid']; ?>";
      webUrl="/ajaxhandler.php?serviceName=gameApi"+'&functionName=randomnumber&userid='+<?php echo $id; ?>+'&sessionid='+"<?php echo $_SESSION['sessionid']; ?>";
    	 $.ajax({
 			 url: webUrl,
 			 method: "GET",
 			 //data: { },
 			 dataType: "json",
     	 	 success: function(data) {
                maxRoll = $("#maxRoll").val();
                if(data.count == maxRoll){
                    rollthedice(data.number);
                    $("#message").text("This was your last roll of the game. Click replay to start it all over again");
                    $('#message').fadeIn().delay(5000).fadeOut();
                    
                    if(data.credit < 1){
                        $("#buy").prop('disabled', false);
                        $("#replay").css("display","inline-block");
                        $("#replay").prop('disabled',true);  
                         $("#play").css('disabled',"none"); 
                        $("#message").text("OOPS! You just run out of credit. But you were doing good. Click on 'Buy Credits' to add credit to your wallet");
                       // $('#message').fadeIn().delay(5000).fadeOut();
                     }
                     else{
                         $("#replay").css("display","inline-block");
                          $("#replay").prop('disabled',false); 
                         $("#buy").prop('disabled',true); 
                        $("#play").css('display','none');
                        //$("#score").text(data.score).css('color',"white");
                       //  $("#replay").style("display","block");
                     }
                     $("#score").text(data.score);

                }
                else if(data.credit < 1){
                    rollthedice(data.number);
                    $("#play").css('display','none');
                    $("#replay").css("display","inline-block");
                    $("#buy").prop('disabled', false);
                    $("#replay").prop('disabled', true);
                    $("#score").text(data.score);
                    $("#message").text("OOPS! You just run out of credit. Click on 'Buy Credits' to add credit to your wallet");
                    //$('#message').fadeIn().delay(5000).fadeOut();


                }
                else{
                rollthedice(data.number);

                $("#score").text(data.score);
                 $("#play").css('display','inline-block');
                    $("#replay").css("display","none");
                    $("#buy").prop('disabled', true);
                    $("#play").prop('disabled', false);

                console.log(data);
              }  
     		 },
     	 	 error: function(error) {
        		console.log(error)
     	 	}
     	 });


	});


    

   
function rollthedice(val){
            var faces = document.getElementsByClassName("dice-face");

  for (var fIt = 0; fIt < faces.length; fIt++) {
    faces[fIt].style.backgroundColor = "#af9400";
  }

/** This parameter needs to be replaced **/
  var randFace = val-1; //Math.round(Math.random() * 5);

  /*** This is dynamic***/

  dice.style.left = "50%"; //Math.random() * innerWidth + "px";
  dice.style.top = "100px"; //Math.random() * innerHeight + "px";

  dice.style.transform =
    "rotate3d(1, 0, 0, " +
    Math.random() * 360 +
    "deg) rotate3d(0, 1, 0, " +
    Math.random() * 360 +
    "deg) rotate3d(0, 0, 1, " +
    Math.random() * 360 +
    "deg)";

  setTimeout(function() {
    dice.style.transform = faceRot[randFace];
    document.getElementsByClassName("dice-face")[
      randFace
    ];
  }, 900);
        }

	$("#new").click(function(){
    
	});
});	
</script>
</html>
