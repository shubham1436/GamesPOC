<?php include_once('config.php'); ?> <html> <script 
src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> 
<style type="text/css">
	*{
		margin:0;
		padding: 0;
	}
	body{
		background: #000;
		width: 100%;
		/*height: 100vh;*/
		color:white;
	}
	.login{
		/*display: flex;*/
		justify-content: center;
		align-items: center;
		height: 100vh;
		text-align: center;
	}
	.flexi{
		margin-top: 100px;
	}
	.login input[type="text"] {
		padding: 5px 10px;
		font-size: 1.2em;
		letter-spacing: 1px;
		width: 180px;
		border-radius: 5px;
		outline: none;
		margin:10px 0;
		border: none;
		/*text-align: center;*/
	}
	.login input[type="button"] {
		padding: 5px 15px;
		width: 140px;
		font-size: 1em;
		border-radius: 5px;
		outline: none;
		margin:5px 0;
		background-color: rgb(82, 124, 186);
		color:white;
		border: none;
	}
	.bllue {
		color:rgb(82, 124, 186);
		display:inline;
	}
	.header{
		margin-top: 20px;
		width: 200px;
		height: 100px;
		font-size:84px;
		display:inline;
	}
</style> <body> <div class="login">
		<div class="header"><span class="bllue">G</span>amer 
<span class="bllue">H</span>ub</div>
	<div class="flexi">
		<div>
			<input type="text" name='username' 
id="username"/>
		</div>
		<div>
			<input type="button" name="Login" id="login" 
value="Sign Up/Login" ></input><br/>
		</div>
	</div> </div>
	<!--div>
		<input type="button" name="new_user" id="new" value="New 
User" ></input>
	</div--> </body> <script type="text/javascript"> 
$(document).ready(function(){
	//url="http://10.175.0.218:8000/user";
	$("#login").click(function(){
    	 val = $("#username").val()
    	 webUrl = "/ajaxhandler.php?name="+val
    	 console.log(webUrl);
    	 $.ajax({
 			 url: webUrl,
 			 method: "POST",
 			 //data: { name : val },
 			 dataType: "json",
     	 	 success: function(data) {
       			console.log(data);
     	 	 	window.location.href="index.php?id="+data.id;
     		 },
     	 	 error: function(error) {
        		console.log(error)
     	 	}
     	 });
	});
	$("#new").click(function(){
    
	});
});
</script>
</html>
