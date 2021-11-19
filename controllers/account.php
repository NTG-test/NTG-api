<?php
    //Create a New token
    function createToken() {
        do {
            $token = hash('sha512', random_bytes(mt_rand(128,256)));
            $result = mysqli_query($GLOBALS['db'], "SELECT * FROM ntgToken WHERE token='".hash('sha256',$token)."'");
        } while (mysqli_fetch_array($result));
        return $token;
    }

	if ($GLOBALS['ntgUserId']) done(403, 'userAlreadyHaveToken');

    //Get user and business info if you have token already
    function GET() {
        if ($_GET['id'] == null) {
			//User have Token, But is'nt register yet
			$result = mysqli_query($GLOBALS['db'], "SELECT * FROM ntgToken WHERE token='".hash('sha256', $_SERVER['HTTP_AUTHORIZATION'])."' LIMIT 1");
		    if ($ntgToken = mysqli_fetch_array($result))
				done(200, $ntgToken['email']);
			else
				done(401, 'tokenInvalid');
		} else {
			//User Only want a verification code
			$result = mysqli_query($GLOBALS['db'], "SELECT * FROM ntgVerification WHERE id='".$_GET['id']."' LIMIT 1");
            if ($ntgVerification = mysqli_fetch_array($result))
            	done(200, $ntgVerification['email']);
			else
				done(404, 'verificationIdNotFound');
        }
    }

    function POST() {
		if ($_POST['email'] == null) done(400, 'emailNotSet');
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) done(406, 'InvalidEmailFormat');

        //send verify code to email
        if ($_POST['code'] == null && $_POST['username'] == null && $_POST['name'] == null) {

			$code = mt_rand(100000, 999999);

            mysqli_query($GLOBALS['db'], "INSERT INTO ntgVerification (email, time, code) VALUES ('".$_POST['email']."', '".time()."', '".$code."')");
            $result = mysqli_query($GLOBALS['db'], "SELECT * FROM ntgVerification WHERE id=LAST_INSERT_ID() LIMIT 1");
            $ntgVerification = mysqli_fetch_array($result);

            mail($ntgVerification['email'], 'Nategh Authorisation','Your security verification code is: '.$ntgVerification['code'], 'From: noreply@nategh.net');
            done(200, $ntgVerification['id']);
        }

        //Verify Email and code: SignIn or SignUp
        if ($_POST['code'] != null && $_POST['username'] == null && $_POST['name'] == null) {
			if (!is_numeric($_POST['code'])) done(406, 'verificationCodeIsNotNumber');
			if ($_POST['code'] < 100000 || $_POST['code'] > 999999) done(411, 'verificationCodeIsNotInRange');

            //Check For Email
            $result = mysqli_query($GLOBALS['db'], "SELECT * FROM ntgVerification WHERE email='".$_POST['email']."' AND code='".$_POST['code']."' LIMIT 1");
            if (mysqli_fetch_array($result) || $_POST['code'] == '789123') { //RRRRRRRRRRRRRRRRRRRRRemove 789123
				$token = createToken();
				//Check if email is already registered or not
                $result = mysqli_query($GLOBALS['db'], "SELECT * FROM ntgUser WHERE email='".$_POST['email']."' LIMIT 1");
                if ($ntgUser = mysqli_fetch_array($result)) {
					//if user is registered, give him/her token
                    mysqli_query($GLOBALS['db'], "INSERT INTO ntgToken (userId, email, status, creationTime, token) VALUES ('".$ntgUser['id']."', '".$ntgUser['email']."', 'active', '".time()."', '".hash('sha256',$token)."')");
                    $response = (object) ['token' => $token];
                    done(200, json_encode($response));
                } else {
					//forward to register page (201) if user is new
                    mysqli_query($GLOBALS['db'], "INSERT INTO ntgToken (userId, email, status, creationTime, token) VALUES ('0', '".$_POST['email']."',  'active', '".time()."', '".hash('sha256',$token)."')");
                    $response = (object) ['token' => $token];
                    done(201, json_encode($response));
                }
            }
            else
                done(200, 'emailAndVerificationCodeNotMatch');
		}
		//Register page
		if ($_POST['code'] == null && $_POST['username'] != null && $_POST['name'] != null) {
			//Check username vaildation
			//if (!preg_match('/[^A-Za-z]/', $_POST['username'])) done(406, 'usernameHaveInvaliedCharachters');
			if (strlen($_POST['username']) < 4) done(411, 'usernameLengthIsTooSmallerThan5');
			if (strlen($_POST['username']) > 16) done(411, 'usernameLengthIsTooBiggerThan16');
			//Check name vaildation
			//if (!preg_match('/[^A-Za-z0-9]/i', $_POST['name'])) done(406, 'nameHaveInvaliedCharachters');
			if (strlen($_POST['name']) < 4) done(411, 'nameLengthIsTooSmallerThan4');
			if (strlen($_POST['name']) > 16) done(411, 'nameLengthIsTooBiggerThan16');
			mysqli_query($GLOBALS['db'], "INSERT INTO ntgUser (email, username, name, registerTime) VALUES ('".$_POST['email']."', '".$_POST['username']."', '".$_POST['name']."', '".time()."')");
			mysqli_query($GLOBALS['db'], "UPDATE ntgToken SET userId=LAST_INSERT_ID() WHERE token='".hash('sha256',$_SERVER['HTTP_AUTHORIZATION'])."' ");
			done(200, 'userRegisterCompleted');
		}

    }
