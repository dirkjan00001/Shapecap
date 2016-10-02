<?php
session_start();
$number_of_questions = 2;			// total number of questions. Note that currently only 2 is possible. TODO extend to arbitrary number of questions
$shape = [	'triangle' 	=> 3,
						'square' 		=> 4,
						'pentagon' 	=> 5,
						'circle' 		=> 15 ];  // This array is used for the generation of the questions. Two shapes are selected for the questions

if (isset($_POST["submit"])) {
	$name = $_POST['name'];
	$email = $_POST['email'];
	$message = $_POST['message'];
	$human[0] = intval($_POST['human0']);
	$human[1] = intval($_POST['human1']);
	$from = 'Demo Contact Form';
	$to = 'example@domain.com';
	$subject = 'Message from Contact Demo ';

	$body ="From: $name\n E-Mail: $email\n Message:\n $message";

	// Check if name has been entered
	if (!$_POST['name']) {
		$errName = 'Please enter your name';
	}

	// Check if email has been entered and is valid
	if (!$_POST['email'] || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$errEmail = 'Please enter a valid email address';
	}

	//Check if message has been entered
	if (!$_POST['message']) {
		$errMessage = 'Please enter your message';
	}
	//Check if simple anti-bot test is correct
	$q = $_SESSION['captcha_questions'];	// questions that were asked

	for ($i=0; $i < $number_of_questions; $i++) {
		$angles_count = $shape[$q[$i]];		// number of angles in the i th question that was asked to the user
		$correct_answer = count(array_keys($_SESSION['captcha_angles'], $angles_count));  // the number of shapes in the figure
		if ($human[$i] !== $correct_answer)
			$errHuman = 'Your anti-spam is incorrect ';
	}

	// If there are no errors, send the email
	if (!$errName && !$errEmail && !$errMessage && !$errHuman) {
		if (mail ($to, $subject, $body, $from)) {
			$result='<div class="alert alert-success">Thank You! I will be in touch</div>';
		} else {
			$result='<div class="alert alert-danger">Sorry there was an error sending your message. Please try again later.</div>';
		}
		// session_unset();
		// session_destroy();
	}
}

$_SESSION['captcha_questions'] = array_rand($shape, $number_of_questions);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Bootstrap contact form with PHP example by BootstrapBay.com.">
    <meta name="author" content="BootstrapBay.com">
    <title>Bootstrap Contact Form With PHP Example</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
  </head>
  <body>
  	<div class="container">
  		<div class="row">
  			<div class="col-md-6 col-md-offset-3">
  				<h1 class="page-header text-center">Contact Form Example</h1>
				<form class="form-horizontal" role="form" method="post" action="index.php">
					<div class="form-group">
						<label for="name" class="col-sm-2 control-label">Name</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="name" name="name" placeholder="First & Last Name" value="<?php echo htmlspecialchars($_POST['name']); ?>">
							<?php echo "<p class='text-danger'>$errName</p>";?>
						</div>
					</div>
					<div class="form-group">
						<label for="email" class="col-sm-2 control-label">Email</label>
						<div class="col-sm-10">
							<input type="email" class="form-control" id="email" name="email" placeholder="example@domain.com" value="<?php echo htmlspecialchars($_POST['email']); ?>">
							<?php echo "<p class='text-danger'>$errEmail</p>";?>
						</div>
					</div>
					<div class="form-group">
						<label for="message" class="col-sm-2 control-label">Message</label>
						<div class="col-sm-10">
							<textarea class="form-control" rows="8" name="message"><?php echo htmlspecialchars($_POST['message']);?></textarea>
							<?php echo "<p class='text-danger'>$errMessage</p>";?>
						</div>
					</div>
						<div <?php 	if ($errHuman)
													echo "class=\"form-group has-error\"";
												else
													echo "class=\"form-group\"";
												?>>
							<label for="human" class="col-sm-2 control-label">Anti spam</label>
							<div id="human" class="col-sm-5">
								<div class="captcha">
									<img src="shapecap.php" alt="Shapecap Captcha" class="img-responsive"><img>
								</div>
							</div>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="human0" name="human0" placeholder="Number of <?php echo $_SESSION['captcha_questions'][0]."s"?>" autocomplete="off">
							</div>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="human1" name="human1" placeholder="Number of <?php echo $_SESSION['captcha_questions'][1]."s"?>" autocomplete="off">
							</div>
							<div class="col-sm-10 col-sm-offset-2">
								<?php echo "<p class='text-danger'>$errHuman</p>";?>
							</div>
						</div>
					<div class="form-group">
						<div class="col-sm-10 col-sm-offset-2">
							<input id="submit" name="submit" type="submit" value="Send" class="btn btn-primary">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-10 col-sm-offset-2">
							<?php echo $result; ?>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
  </body>
</html>
