<?php
    $config = require('config.php');
    $connection = require('assets/php/db.php');
	
	if ($config['SteamAPIKey'] == '') {
		header("Location: error.php?error=nosteamkey");
        exit();
	}

    session_start();
	if(isset($_SESSION['logged_in']) && !empty($_SESSION['logged_in'])){
		$username = $_SESSION['userData']['name'];
		$avatar = $_SESSION['userData']['avatar'];
	}

    if (isset($_GET['player'])) {
        $steamid = $_GET['player'];
    } else {
        echo "No player provided.";
        exit();
    }

	if (isset($_GET['server'])) {
		$server = $_GET['server'];
	} else {
		$server = array_search(reset($config['servers']), $config['servers']);
	}

	if (isset($_GET['table'])) {
		$tablename = $_GET['table'];
	} else {
		$tablename = 'wipe';
	}

    $table = $config['servers'][$server][$tablename];
    $sql = "SELECT * FROM $table WHERE steamid=$steamid";
	$res_data = mysqli_query($connection, $sql);
	$resultCheck = mysqli_num_rows($res_data);

    if ($resultCheck <= 0) {
        header("Location: error.php?error=nodata");
        exit();
    }

    $player = mysqli_fetch_assoc($res_data);

	function getPlayTime($minutes) {
		$d = floor ($minutes / 1440);
		$h = floor (($minutes - $d * 1440) / 60);
		$m = $minutes - ($d * 1440) - ($h * 60);
		
		if ($d > 0) {
			return "{$d}d {$h}h {$m}m";
		} else if ($h > 0) {
			return "{$h}h {$m}m";
		} else {
			return "{$m}m";
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

		<main class='player-profile-wrapper'>
			<?php 
				echo "<img class='background-image' src=" . $config['backgroundImage'] . " />"; 
			?>
            <section class='player-view-container'>
				<div class='player-view-header'>
					<a target="#" href=<?php echo "https://steamcommunity.com/profiles/". $player['steamid'] ?>><?php echo $player['name'] ?></a>
					<div class='profile-table-buttons'>
						<a class=<?php echo ($tablename == 'wipe' ? "active" : "''") ?> href=<?php echo "?server=$server&player=". $player['steamid'] ."&table=wipe"?>>WIPE</a>
						<a class=<?php echo ($tablename == 'overall' ? "active" : "''") ?> href=<?php echo "?server=$server&player=". $player['steamid'] ."&table=overall"?>>OVERALL</a>
					</div>
				</div>
				<div class='player-view'>
					<h1 class='hover-text'><i class="fa-solid fa-circle-info"></i> HOVER FOR HIT COUNT</h1>
					<img class='player-model' src='./assets/images/player.png' />
					<div class='hits-container'>
						<div class='hitcount head'>
							<h1 class='hitcount-part'>Head</h1>
							<?php echo "<p class='hitcount-result'>". $player['head_hits'] ." Hits</p>" ?>
						</div>
						<div class='hitcount torso'>
							<h1 class='hitcount-part'>Torso</h1>
							<?php echo "<p class='hitcount-result'>". $player['torso_hits'] ." Hits</p>" ?>
						</div>
						<div class='hitcount leftarm'>
							<?php echo "<p class='hitcount-result'>". $player['leftleg_hits'] ." Hits</p>" ?>
							<h1 class='hitcount-part'>Left Arm</h1>
						</div>
						<div class='hitcount rightarm'>
							<h1 class='hitcount-part'>Right Arm</h1>
							<?php echo "<p class='hitcount-result'>". $player['leftleg_hits'] ." Hits</p>" ?>
						</div>
						<div class='hitcount leftleg'>
							<?php echo "<p class='hitcount-result'>". $player['leftleg_hits'] ." Hits</p>" ?>
							<h1 class='hitcount-part'>Left Leg</h1>
						</div>
						<div class='hitcount rightleg'>
							<h1 class='hitcount-part'>Right Leg</h1>
							<?php echo "<p class='hitcount-result'>". $player['rightleg_hits'] ." Hits</p>" ?>
						</div>
						<div class='hitcount leftfoot'>
							<?php echo "<p class='hitcount-result'>". $player['leftfoot_hits'] ." Hits</p>" ?>
							<h1 class='hitcount-part'>Left Foot</h1>
						</div>
						<div class='hitcount rightfoot'>
							<h1 class='hitcount-part'>Right Foot</h1>
							<?php echo "<p class='hitcount-result'>". $player['rightfoot_hits'] ." Hits</p>" ?>
						</div>
					</div>
				</div>
            </section>
            <section class='profile-row-1'>
				<h1 class='profile-data-header'>Player Stats</h1>
				<div class='profile-data-container'>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-globe profile-icon"></i>Connects</h1>
							<h2 class='profile-result'><?php echo $player['connections'] ?></h2>
						</div>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-clock profile-icon"></i>Playtime</h1>
							<h2 class='profile-result'><?php echo getPlayTime($player['playtime']) ?></h2>
						</div>
					</div>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-gun profile-icon"></i>Kills</h1>
							<h2 class='profile-result'><?php echo $player['kills'] ?></h2>
						</div>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-cross profile-icon"></i>Deaths</h1>
							<h2 class='profile-result'><?php echo $player['deaths'] ?></h2>
						</div>
					</div>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-user-doctor profile-icon"></i>Wounded</h1>
							<h2 class='profile-result'><?php echo $player['wounded'] ?></h2>
						</div>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-capsules profile-icon"></i>Suicides</h1>
							<h2 class='profile-result'><?php echo $player['suicides'] ?></h2>
						</div>
					</div>
					<?php
						$kdr = $player['deaths'] > 0 ? round($player['kills'] / $player['deaths'], 2) : $player['kills'];
						$hits = $player['head_hits'] + $player['torso_hits'] + $player['leftarm_hits'] + $player['rightarm_hits'] + $player['leftleg_hits'] + $player['rightleg_hits'] + $player['leftfoot_hits'] + $player['rightfoot_hits'];
						$accuracy = $player['bfired'] < 1 ? "100" : (round(($hits / $player['bfired']) * 100));
						if ($accuracy > 100) $accuracy = 100;
					?>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-crosshairs profile-icon"></i>K/D Ratio</h1>
							<h2 class='profile-result'><?php echo $kdr ?></h2>
						</div>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-bullseye profile-icon"></i>Accuracy</h1>
							<h2 class='profile-result'><?php echo $accuracy ?>%</h2>
						</div>
					</div>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-bomb profile-icon"></i>Satchels</h1>
							<h2 class='profile-result'><?php echo $player['satchelsthrown']; ?></h2>
						</div>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-explosion profile-icon"></i>C4</h1>
							<h2 class='profile-result'><?php echo $player['c4thrown']; ?></h2>
						</div>
					</div>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-rocket profile-icon"></i>Rockets</h1>
							<h2 class='profile-result'><?php echo $player['rocketsfired']; ?></h2>
						</div>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-building-shield profile-icon"></i>TCs Broke</h1>
							<h2 class='profile-result'><?php echo $player['tcsdestroyed']; ?></h2>
						</div>
					</div>
				</div>
            </section>
            <section class='profile-row-2'>
				<h1 class='profile-data-header'>NPC Kills</h1>
				<div class='profile-data-container'>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-egg profile-icon"></i>Chickens</h1>
							<h2 class='profile-result'><?php echo $player['chickens'] ?></h2>
						</div>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-hippo profile-icon"></i>Boars</h1>
							<h2 class='profile-result'><?php echo $player['boars'] ?></h2>
						</div>
					</div>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-leaf profile-icon"></i>Deers</h1>
							<h2 class='profile-result'><?php echo $player['deers'] ?></h2>
						</div>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-horse profile-icon"></i>Horses</h1>
							<h2 class='profile-result'><?php echo $player['horses'] ?></h2>
						</div>
					</div>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-bone profile-icon"></i>Wolves</h1>
							<h2 class='profile-result'><?php echo $player['wolves'] ?></h2>
						</div>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-paw profile-icon"></i>Bears</h1>
							<h2 class='profile-result'><?php echo $player['bears'] ?></h2>
						</div>
					</div>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-walkie-talkie profile-icon"></i>Scientists</h1>
							<h2 class='profile-result'><?php echo $player['scientists'] ?></h2>
						</div>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-helicopter profile-icon"></i>Helicopters</h1>
							<h2 class='profile-result'><?php echo $player['helicopters'] ?></h2>
						</div>
					</div>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<h1 class='profile-header'><i class="fa-solid fa-car-burst profile-icon"></i>Bradleys</h1>
							<h2 class='profile-result'><?php echo $player['bradleys'] ?></h2>
						</div>
					</div>
				</div>
            </section>
			<section class='profile-row-3'>
				<h1 class='profile-data-header'>Weapon Kills</h1>
				<div class='profile-data-container'>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='Assult Rifle'>
								<img class='weapon-image' src='./assets/images/weapons/assultrifle.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['ak47'] ?></h2>
						</div>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='LR-300'>
								<img class='weapon-image' src='./assets/images/weapons/lr300.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['lr300'] ?></h2>
						</div>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='M39'>
								<img class='weapon-image' src='./assets/images/weapons/m39.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['m39'] ?></h2>
						</div>
					</div>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='Semi Auto Rifle'>
								<img class='weapon-image' src='./assets/images/weapons/sar.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['sar'] ?></h2>
						</div>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='HM-LMG'>
								<img class='weapon-image' src='./assets/images/weapons/hmlmg.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['hmlmg'] ?></h2>
						</div>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='M249'>
								<img class='weapon-image' src='./assets/images/weapons/m249.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['m249'] ?></h2>
						</div>
					</div>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='Bolt Action Rifle'>
								<img class='weapon-image' src='./assets/images/weapons/bolt.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['bolt'] ?></h2>
						</div>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='L96'>
								<img class='weapon-image' src='./assets/images/weapons/l96.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['l96'] ?></h2>
						</div>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='MP5'>
								<img class='weapon-image' src='./assets/images/weapons/mp5.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['mp5'] ?></h2>
						</div>
					</div>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='Thompson'>
								<img class='weapon-image' src='./assets/images/weapons/thompson.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['thompson'] ?></h2>
						</div>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='Custom SMG'>
								<img class='weapon-image' src='./assets/images/weapons/custom.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['custom'] ?></h2>
						</div>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='Pump Shotgun'>
								<img class='weapon-image' src='./assets/images/weapons/pumpshotgun.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['pump'] ?></h2>
						</div>
					</div>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='Double Barrel'>
								<img class='weapon-image' src='./assets/images/weapons/double.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['doublebarrel'] ?></h2>
						</div>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='Spaz-12'>
								<img class='weapon-image' src='./assets/images/weapons/spaz12.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['spaz12'] ?></h2>
						</div>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='M92'>
								<img class='weapon-image' src='./assets/images/weapons/m92.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['m92'] ?></h2>
						</div>
					</div>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='Python'>
								<img class='weapon-image' src='./assets/images/weapons/python.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['python'] ?></h2>
						</div>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='Semi Auto Pistol'>
								<img class='weapon-image' src='./assets/images/weapons/p250.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['semipistol'] ?></h2>
						</div>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='Revolver'>
								<img class='weapon-image' src='./assets/images/weapons/revolver.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['revolver'] ?></h2>
						</div>
					</div>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='Waterpipe'>
								<img class='weapon-image' src='./assets/images/weapons/waterpipe.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['waterpipe'] ?></h2>
						</div>
						<div class='profile-data' >
							<div class='weapon-profile-header' weapon-name='Eoka'>
								<img class='weapon-image' src='./assets/images/weapons/eoka.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['eoka'] ?></h2>
						</div>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='Nailgun'>
								<img class='weapon-image' src='./assets/images/weapons/nailgun.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['nailgun'] ?></h2>
						</div>
					</div>
					<div class='profile-data-section'>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='Compound Bow'>
								<img class='weapon-image' src='./assets/images/weapons/compound.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['compound'] ?></h2>
						</div>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='Crossbow'>
								<img class='weapon-image' src='./assets/images/weapons/crossbow.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['crossbow'] ?></h2>
						</div>
						<div class='profile-data'>
							<div class='weapon-profile-header' weapon-name='Hunting Bow'>
								<img class='weapon-image' src='./assets/images/weapons/bow.png' />
							</div>
							<h2 class='profile-result'><?php echo $player['bow'] ?></h2>
						</div>
					</div>
				</div>
            </section>
		</main>
	</body>
</html>