<?php 
  session_start();

  if (!isset($_SESSION['username'])) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Chat App - Login</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
	
	<link rel="icon" href="img/logo.png">

	<style>

		:root {
			--poppins: 'Poppins', sans-serif;
			--lato: 'Lato', sans-serif;

			--light: #F9F9F9;
			--blue: #3C91E6;
			--light-blue: #CFE8FF;
			--grey: #eee;
			--dark-grey: #AAAAAA;
			--dark: #342E37;
			--red: #DB504A;
			--yellow: #FFCE26;
			--light-yellow: #FFF2C6;
			--orange: #FD7238;
			--light-orange: #FFE0D3;
		}

		.body {
			background: #F9F9F9;
		}
		
		.vh-100 {
			min-height: 100vh;
		}
		.w-400 {
			width: 400px;
		}
		.fs-xs {
			font-size: 1rem;
		}
		.w-10 {
			width: 10%;
		}
		a {
			text-decoration: none;
		}
		.fs-big {
			font-size: 5rem !important;
		}
		.online {
			width: 10px;
			height: 10px;
			background: green;
			border-radius: 50%;
		}
		.w-15 {
			width: 15%;
		}
		.fs-sm {
			font-size: 1.4rem;
		}
		small {
			color: #bbb;
			font-size: 0.7rem;
			text-align: right;
		}
		.chat-box {
			overflow-y: auto;
			overflow-x: hidden;
			max-height: 50vh;
		}
		.rtext {
			width: 65%;
			background: #f8f9fa;
			color: #444;
		}

		.ltext {
			width: 65%;
			background: #3289c8;
			color: #fff;
		}
		/* width */
		*::-webkit-scrollbar {
		  width: 3px;
		}

		/* Track */
		*::-webkit-scrollbar-track {
		  background: #f1f1f1;
		}

		/* Handle */
		*::-webkit-scrollbar-thumb {
		  background: #aaa;
		}

		/* Handle on hover */
		*::-webkit-scrollbar-thumb:hover {
		  background: #3289c8;
		}

		textarea {
			resize: none;
		}

		/*message_status*/
	</style>
</head>
<body class="d-flex justify-content-center align-items-center" style="background: #F9F9F9; height: 100%; padding: 0;">
 	<div class="w-400 p-3">
	 	<form method="post" action="app/http/auth.php">
			<div class="d-flex justify-content-center align-items-center flex-column">

		 		<img src="img/logo.png" class="w-25">
		 		<h3 class="display-4 fs-1 p-3 text-center">CHAT ROOM</h3>   

	 		</div>
	 		<?php if (isset($_GET['error'])) { ?>
	 		<div class="alert alert-warning" role="alert">
			  <?php echo htmlspecialchars($_GET['error']);?>
			</div>
			<?php } ?>
			
	 		<?php if (isset($_GET['success'])) { ?>
	 		<div class="alert alert-success" role="alert">
			  <?php echo htmlspecialchars($_GET['success']);?>
			</div>
			<?php } ?>
		  <div class="mb-3">
		    <label class="form-label"> User name</label>
		    <input type="text" class="form-control"name="username">
		  </div>

		  <div class="mb-3">
		    <label class="form-label">Password</label>
		    <input type="password" class="form-control" name="password">
		  </div>
			  
		  <button type="submit" class="btn btn-primary">LOGIN</button>
		  <a href="signup.php">Sign Up</a>
		</form>
 	</div>
</body>
</html>
<?php
  }else{
  	header("Location: home.php");
   	exit;
  }
 ?>