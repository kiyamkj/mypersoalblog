<?php
$mode = null;
$errors = array();
$success = null;

if(isset($_POST['login'])){
	$mode = "login";

	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
	$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

	$query = "SELECT ID, name, email, rights 
			  FROM tbl_user
			  WHERE email = ?
			  	AND password = SHA1(?)";

	$prepared_statement = $db->prepare($query);
	$prepared_statement->bind_param('ss', $email, $password);

	$prepared_statement->execute();

	$result = $prepared_statement->get_result();

	if($result->num_rows == 0){
		$errors[] = "Username or password is not valid";
	} else {
		$user = $result->fetch_assoc();

		$_SESSION['logged_in'] = true;
		$_SESSION['user_id'] = $user['ID'];
		$_SESSION['user_name'] = $user['name'];
		$_SESSION['user_email'] = $user['email'];
		$_SESSION['user_rights'] = $user['rights'];

 		header("Location: index.php");
	}
} else if(isset($_POST['register'])){
	$mode = "register";

	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
	$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
	$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
	$password_confirm = filter_input(INPUT_POST, 'password_confirm', FILTER_SANITIZE_STRING);

	if(!$email){
		$errors[] = "E-Mail is invalid.";
	}

	if($password !== $password_confirm){
		$errors[] = "The given passwords do not match";
	}

	if(count($errors) == 0){
		$query_input = "INSERT INTO tbl_user (name, email, password, rights) VALUES (?, ?, SHA1(?), 3)";
		$prepare_insert = $db->prepare($query_input);
		$prepare_insert->bind_param("sss", $name, $email, $password);
		
		if(!$prepare_insert->execute()){
			$errors[] = "Could not register the account. Please inform the administrator.";
		} else {
			$success = "The account was successfully created. You can login now.";
			$mode = "login";
		}
	}
}
?>
<div class="well">
	<?php
	if(count($errors) > 0){
	?>
		<div class="bg-danger" style="padding: 10px 5px 2px 0px; margin-bottom: 10px;">
			<ul>
				<?php
				foreach($errors as $error){
					echo "<li>$error</li>";
				}
				?>
			</ul>
		</div>
	<?php	
	}
	?>

	<?php
		if($success != null){
			?>
			<div class="bg-success" style="padding: 10px; margin-bottom: 10px;">
				<?php echo $success; ?>
			</div>
			<?php
		}
	?>
	<ul class="nav nav-tabs">
		<li class="<?php echo ($mode == "login" || !$mode ? "active" : ""); ?>"><a href="#login" data-toggle="tab">Login</a></li>
		<li class="<?php echo ($mode == "register" ? "active" : ""); ?>"><a href="#create" data-toggle="tab">Create Account</a></li>
	</ul>
	<div id="myTabContent" class="tab-content">
		<div class="tab-pane <?php echo ($mode == "login" || !$mode ? "in active" : "fade"); ?>" id="login">
			<br>
			<form class="form-horizontal" action="index.php?action=login" method="POST">
				<fieldset>
					<div class="form-group">
						<!-- Username -->
						<label class="col-sm-2 control-label" for="email">E-Mail</label>
						<div class="col-sm-5">
							<input type="text" id="email" name="email" placeholder="" class="form-control">
						</div>
					</div>
					
					<div class="form-group">
						<!-- Password-->
						<label class="col-sm-2 control-label" for="password">Password</label>
						<div class="col-sm-5">
							<input type="password" id="password" name="password" placeholder="" class="form-control">
						</div>
					</div>
					
					
					<div class="form-group">
						<!-- Button -->
						<div class="col-sm-5 col-sm-offset-2">
							<button type="submit" name="login" class="btn btn-success">Login</button>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
		<div class="tab-pane <?php echo ($mode == "register" ? "in active" : "fade"); ?>" id="create">
			<br>
			<form id="tab" action="index.php?action=login" method="POST">
				<div class="form-group">
					<label for="register_name">Name</label>
					<input type="text" id="register_name" name="name" value="" class="form-control">
				</div>
				<div class="form-group">
					<label for="register_email">Email</label>
					<input type="text" id="register_email" name="email" value="" class="form-control">
				</div>
				<div class="form-group">
					<label for="register_password">Password</label>
					<input type="password" id="register_password" name="password" value="" class="form-control">
				</div>
				<div class="form-group">
					<label for="register_password_confirm">Confirm password</label>
					<input type="password" id="register_password_confirm" name="password_confirm" value="" class="form-control">
				</div>
				<div>
					<button type="submit" name="register" class="btn btn-primary">Create Account</button>
				</div>
			</form>
		</div>
	</div>
</div>