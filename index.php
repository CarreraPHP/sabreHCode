<?php
session_start();
if (isset($_SESSION["userDetails"])) {
	header("location:app.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="content-language" content="de" />
		<meta name="reply-to" content="mailto:DEFAULT_EMAIL" />
		<meta name="robots" content="index, nofollow" />
		<meta name="date" content="2012-05-31" />
		<title>Sabre LMS - Mock Tool</title>
		<link rel="stylesheet" type="text/css" href="resources/css/external.css"/>
		<link rel="stylesheet" type="text/css" href="resources/css/style.css"/>
		<script src="/library/jquery.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$("#form").submit(function() 
				{
					var username = $('#mail').val();
					var password = $('#pass').val();
					if(username != 'E-Mail-Address' && password != 'Password') {
						$.ajax({
							type : "POST",
							url : "data/session/User.php",
							data : {
								type : "login",
								username : username,
								password : password

							},
							dataType : "json",
							success : function(data) {
								if(data.success){
									$(location).attr('href', "app.php");
								}else{									
									$('.errorHolder').html(data.message).removeClass("invisible").addClass("visible");
								}
								console.log(data);

							}
						});
					}
					else 
					{
						alert("Please enter username and password");
					}
					
					return false;
				});
			});
		</script>
	</head>
	<body>
		<div class="errorHolder invisible" ></div>
		<div class="page_margins">
			<div id="header" role="banner">
				<div id="topnav" role="contentinfo">
					<h1>Sabre LMS - Mock Tool</h1>
				</div>
			</div>
			<div id="main">
				<div class="subcolumns">
					<form class="loginForm" id="form" >
						<legend>
							Login
						</legend>
						<div class="input text">
							<input type="hidden" name="action" value="login" />
							<input type="text" name="mail" id="mail" value="" 
										 onfocus="if(this.value=='email/username'){this.value='';}"
										 onblur="if(this.value==''){this.value='email/username';}"
										 />

						</div>
						<div class="input text">
							<input type="password" name="pass" id="pass" value="" 
										 onfocus="if(this.value=='password'){this.value='';}"
										 onblur="if(this.value==''){this.value='password';}"/>
						</div>
						<div class="input text">
							<div class="submit">
								<input class="submitBtn" type="submit" name="login" value="Anmelden" />
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>
