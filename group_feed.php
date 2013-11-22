<?php
	require_once('include/functions.php');
	include_once('include/db.php');
	
	//subscribe to group (group name / key or user email)
	//display the users subscribed groups and polls within those groups
	try{
		if(!userLoggedIn()){
			header("location:index.php");
		}
		$db = db_getpdo();
		$groups = groupsJoinedByUser($db);
	}catch(PDOException $e){
		//echo "Caught PDOException ('{$e->getMessage()}')\n{$e}\n";
		$_SESSION['error'] = $e->getMessage();
		header("location:error.php");
	}
?>
<!doctype html>
<html>
	<head>
		<title>WebClicker - Group Subscriptions</title>
		<?php outputHeader(); ?>
	</head>
	<body>
		<div id="homepage" data-role="page" data-theme='a'>
			<header data-role="header"  data-tap-toggle="false">
				<h1>Group Subscriptions</h1>
				<a href="index.php"  data-role="button" class="ui-btn-left" data-inline="true" data-icon="home" data-ajax="false">Home</a>
			</header><!-- /header -->
			
			<div data-role="collapsible">
				<h1>Subscribe to a Group</h1>
				<ul data-role="listview" data-filter="true" data-inset="true">
				<?php displayPossibleSubscriptions($db); ?>
				</ul>
			</div>
			<div data-role="content">
				<h2>Group Subscriptions</h2>
				<ul data-role="listview" data-inset="true">
			<?php
				foreach($groups as $group){
					echo '<li><div data-role="collapsible">';
					echo "<h1>$group->Name</h1>";
					echo 	'<ul data-role="listview" data-filter="true">';
					if($group->Name != 'Public'){
						echo '<li><form action="control/group_unsubscribe.php" method="POST" data-ajax="false">
								<input type="hidden" name="groupcreator" value="'.$group->Creator.'">
								<input type="hidden" name="groupname" value="'.$group->Name.'">
								<input type="submit" name="submit" value="Unsubscribe">
							</form></li>';
					}
					
					$sql = $db->prepare("SELECT * FROM polls WHERE poll_group_name=:group");
					$sql->bindValue(':group', $group->Name);
					$sql->execute();
					displayPollsList($sql->fetchAll());
					echo 	'</ul><!-- /list -->';
					echo '</div></li><!-- /collapsible -->';
				}
				$db = null;
			?>
				</ul>
			</div>
		</div><!-- /page -->
	</div>
</body>
</html>