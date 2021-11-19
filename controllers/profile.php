<?php
    function GET() {
        $result = mysqli_query($GLOBALS['db'], "SELECT * FROM ntgUser WHERE id='".$GLOBALS['ntgUserId']."' ");
		$ntgUser = mysqli_fetch_array($result);
		$response = (object) ['email'=>$ntgUser['email'], 'username'=>$ntgUser['username'], 'name'=>$ntgUser['name']];
		done(200, json_encode($response));
    }

	function POST() {
		if ($_POST['name'] == null) done(400, 'nameNotSet');
		//if (!preg_match('/[^A-Za-z0-9]/i', $_POST['name'])) done(406, 'nameHaveInvaliedCharachters');
		if (strlen($_POST['name']) < 4) done(411, 'nameLengthIsTooSmallerThan4');
		if (strlen($_POST['name']) > 16) done(411, 'nameLengthIsTooBiggerThan16');
		mysqli_query($GLOBALS['db'], "UPDATE ntgUser SET name='".$_POST['name']."' WHERE id='".$GLOBALS['ntgUserId']."' ");
		done(200, 'userUpdated');
	}

	function DELETE() {
		$result = mysqli_query($GLOBALS['db'], "SELECT * FROM ntgUser WHERE id='".$GLOBALS['ntgUserId']."' ");
		$ntgUser = mysqli_fetch_array($result);
		if ($_POST['email'] == $ntgUser['email']) {
			mysqli_query($GLOBALS['db'], "UPDATE ntgUser SET email='' AND name='' WHERE id='".$GLOBALS['ntgUserId']."' ");
			mysqli_query($GLOBALS['db'], "UPDATE ntgToken SET status='terminated' WHERE token='".hash('sha256', $_SERVER['HTTP_AUTHORIZATION'])."' ");
			done(200, 'userDeleted');
		}
	}
