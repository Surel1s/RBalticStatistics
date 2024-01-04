<?php
    $config = require('config.php');

	session_start();
	if(isset($_SESSION['logged_in']) && !empty($_SESSION['logged_in'])){
		$username = $_SESSION['userData']['name'];
		$avatar = $_SESSION['userData']['avatar'];
	}

	if (isset($_GET['error'])) {
		$error = $_GET['error'];
	} else {
		$error = 'nodata';
	}
	
	switch ($error) {
		case "nosteamkey": {
			$errortitle = "No Steam API Key!";
			$errordescription = "It looks like the owner of this site has not put in a steam api key, please reach out to them.";
			break;
		}

		case "noconnection": {
			$errortitle = "No Connection!";
			$errordescription = "Looks like we had a issue connecting to the database provided, please reach out to owner of site.";
			break;
		}

		case "nodata": {
			$errortitle = "No Profile Found!";
			$errordescription = "There was a error retrieving data for this user. Please try again later.";
			break;
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<link rel="icon" href="favicon.ico">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta
			name="description"
			content=<?php echo $config['SiteDescription'] ?>
		>
		<title><?php echo ($config['ServerName'] . " - Official Stats") ?></title>
		<link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
		<link rel="stylesheet" href="styles.css">
	</head>
	<body>
		<nav class='main-navbar-container'>
			<div class='navbar-textlogo'><a href='index.php'><?php echo $config['ServerName'] ?></a></div>
			<div class='navbar-links'>
				<?php 
					if (isset($_SESSION['logged_in']) && !empty($_SESSION['logged_in'])) {
						echo "<div class='user-profile'>";
							echo "<img class='user-profile-avatar' src=". $avatar ."></img>";
							echo "<h1 class='user-profile-name'>". $username ."</h1>";
							echo '';
						echo "</div>";
						echo "<div class='user-profile-dropdown'>";
							echo "<button class='user-profile-button' onclick='window.location = \"profile.php?server=$server&player=" . $_SESSION['userData']['steam_id'] ."\"'><i class=\"fa-solid fa-user\"></i>PROFILE</button>";
							echo "<button class='user-logout-button' onclick='window.location = \"assets/php/steam/logout.php\"'><i class=\"fa-solid fa-circle-xmark\"></i>LOGOUT</button>";
						echo "</div>";
					} else {
						echo "<a class='navbar-login-button' href='assets/php/steam/init-steam.php'><i class='fa-solid fa-arrow-right-to-bracket'></i>LOGIN</a>";
					}
				?>
			</div>
		</nav>

		<main class='error-container'>
			<?php 
				echo "<img class='background-image' src=" . $config['backgroundImage'] . " />"; 
			?>
			
			<h1><i class="fa-solid fa-circle-exclamation"></i><?php echo $errortitle; ?></h1>
			<p><?php echo $errordescription; ?></p>
			<a href='index.php'>BACK TO HOME</a>
		</main>
    </body>
</html>