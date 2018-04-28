<?php 

    //VALIDATION///////////////////////////////////////////////////////////////////////
	function verifyPassword($pass1, $pass2){
		$pass1 = trim($pass1);
		$pass2 = trim($pass2);
		if ( $pass1 === $pass2){
			return true;
		}
	}
	
	
	function checkEmptyFields($formFields){
		$form_Errors = array();
		
		foreach($formFields as $formField){
			if( !isset($_POST[$formField]) || $_POST[$formField] == NULL ){
					$form_Errors[] = $formField . " is a required field.";
				}
			}
		return $form_Errors;
	}
	
	
	function validateEmail($email){
		$form_Errors = array();
		
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$emailErr = "Invalid email format"; 
		}
		return $form_Errors;
	}
	
	
	function checkDuplicate($db, $tableName, $colName, $value){

		try{
			$sqlQuery = "SELECT * FROM " . $tableName . " WHERE " . $colName . " =:value";
			
			$statement = $db->prepare($sqlQuery);
			
			
			$statement->execute(array(':value'=> $value));
			
			if ( $statement->rowCount() >= 1 ){
				return true;
			}
			
		}
		catch(PDOException $ex){
			echo $ex->getMessage();
		}
		
		return false;
	}
	
	function checkDuplicate2($db, $tableName, $colName, $value1, $value2){

		try{
			$sqlQuery = "SELECT * FROM " . $tableName . " WHERE " . $colName . " =:value AND user_id = " . $value2 ;
			
			$statement = $db->prepare($sqlQuery);
			
			
			
			$statement->execute(array(':value'=> $value1));
			
			if ( $statement->rowCount() >= 1 ){
				return true;
			}
			
		}
		catch(PDOException $ex){
			echo $ex->getMessage();
		}
		
		return false;
	}
	
	function checkMinLength($formsWithLengths){
		$form_errors = array();
		
		foreach($formsWithLengths as $formWithLength => $formMinValue){
			if( strlen(trim($_POST[$formWithLength])) <= $formMinValue ){
				$form_errors[] =  $formWithLength . " must be at least " . $formMinValue . " characters";
			}
		}
		return $form_errors;
	}
	
	function compareValues($valuesArray){
		$form_errors = array();
		
		if ($valuesArray[0] != $valuesArray[1]){
			$form_errors[] = 'Passwords do not match';
		}
		
		return $form_errors;
    }

    function isCookieValid($db){
		
		$isValid = false;
		
		if ( isset($_COOKIE['rememberMeCookie'])) {
			$decryptedCookie = base64_decode($_COOKIE['rememberMeCookie']);
			$user_ID = explode("secretkey", $decryptedCookie);
			$userID = $user_ID[1];
			
			try{
				$sqlQuery = "SELECT * FROM users WHERE user_id = :userid";
				
				$stmtQuery = $db->prepare($sqlQuery);
				
				$stmtQuery->execute(array(':userid'=>$userID));
				
				//if match record is found, create session variables
				if ( $rs = $stmtQuery->fetch() ){
					$id = $rs['user_id'];
					$username = $rs['username'];
					
					$_SESSION['id'] = $id;
					$_SESSION['username'] = $username;
					$isValid = true;
				}
				else{
					$isValid = false;
					signOut();
				}
			}
			catch(PDOException $ex){
				// error in querying database
			}
		}
		
		return $isValid;
		
	}
    

    function validateToken($theToken){
		$valid = false;
		
		if ( isset($theToken) && hash_equals($theToken, $_SESSION['token']) ){
			$valid = true;
			unset($_SESSION['token']);
		}
		
		return $valid;
    }
    
    function checkFingerprint(){
		$time_limit = 60 * 10;
		$isValidFingerprint = true;
		
		// $fingerprint = $_SESSION['fingerprint'];
		$current_fingerprint = getEncodedValue($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
		
		
		
		
		if ( isset($_SESSION['fingerprint']) && ($current_fingerprint !=  $_SESSION['fingerprint'])){
			$isValidFingerprint = false;
			signOut();
		}
		elseif ( isset($_SESSION['fingerprint']) && isset($_SESSION['last_active']) ) {
			$session_time = time() - $_SESSION['last_active'];
			if ( $session_time > $time_limit ){
				$isValidFingerprint = false;
				signOut();
			}
			else{
				$_SESSION['last_active'] = time();
				// $isValidFingerprint = true;
			}
		}
		else{
			
			$isValidFingerprint = false;
			// signOut();
		}
	
		return $isValidFingerprint;
	}

    //MISC FUNCTIONS///////////////////////////////////////////////////////////
	
	function redirectTo($page){
		header("location:" . $page . ".php");
	}
	
	function getHashValue($raw){
		return password_hash($raw, PASSWORD_DEFAULT);
    }
    
    //GET FUNCTIONS/////////////////////////////////

    function getEncodedValue($key, $value){
		$encoded_data = "";
			
			if (!empty($key) && !empty($value) ){
				$encoded_data = md5($key . $value);
			}
		
		return $encoded_data;
	}
	
	function getBase64EncodedValue($key, $value){
		$encoded_data = "";
			
			if (!empty($key) && !empty($value) ){
				$encoded_data = base64_encode($key . $value);
			}
		
		return $encoded_data;
	}
	
	function getBase64DecodedValue($key, $value){
		$decodedData = "";
			try{
				if (!empty($key) && !empty($value) ){
					$decoded_data = base64_decode($value);
					$decoded_Data = explode($key,$decoded_data);
					if( isset($decoded_Data[1]) ){
						$decodedData = $decoded_Data[1];
					}
				}
			}
			catch(Exception $ex){
				
			}
		
		return $decodedData;
	}
	
	function generateToken(){
		$randomToken = getBase64EncodedValue("usertoken", openssl_random_pseudo_bytes(32));	
		$_SESSION['token'] = $randomToken;
		
		return $randomToken;
	}


    function decodeHash($key, $hash){
		$decodedValue = "";
		
		if ( !empty($key) && !empty($hash) ){
			$tempValue = base64_decode($hash);
			$_decodedValue = explode($key, $tempValue);
			$decodedValue = $_decodedValue[1];
		}
		
		return $decodedValue;
    }

    function getRecord($db, $tableName, $colName, $value){
		
		$data = array();
		
		try{
			$sqlQuery = "SELECT * FROM " . $tableName . " WHERE user_id = " . $value ;
			
			$statement = $db->prepare($sqlQuery);
			
			$statement->execute(array(':value'=> $value));
			
			while( $rs = $statement->fetch() ){
				$data = $rs;
			}
			
		}
		catch(PDOException $ex){
			echo $ex->getMessage();
		}
		
		return $data;
	}
    
    

    //SET FUNCTIONS//////////////////////////////////
	
	function setFingerprint(){
		$_SESSION['fingerprint'] = getEncodedValue($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
		$_SESSION['last_active'] = time();
	}
    
    


	function rememberMe($userID){
		$encryptedID = base64_encode("secretkey" . $userID);
		
		//set a cookie that will expire after 30days
		setcookie("rememberMeCookie", $encryptedID, time()+60*60*24*100,"/");
    }
    
    function setValue($db, $tableName, $colName, $colvalue, $key, $keyvalue){
		
		$updated = false;
		
		try{
			$sqlQuery = "UPDATE " . $tableName . " SET " . $colName . " =  :colvalue WHERE " . $key ." = :keyvalue LIMIT 1";
			
			$statement = $db->prepare($sqlQuery);
			
			$statement->execute(array(':colvalue'=>$colvalue, ':keyvalue'=>$keyvalue));
			
			if ( $statement->rowCount() == 1 ){
				$updated = true;
			}
			
		}
		catch(PDOException $ex){
			//
		}
		
		return $updated;
    }
    
    //EXECUTIONARIES //////////////////////////////////////
	
	function signOut(){
		// remove all session variables
		session_unset($_SESSION['id']);
		session_unset($_SESSION['username']);
		
		//destroy all cookies
		if ( isset($_COOKIE['rememberMeCookie']) ){
			unset($_COOKIE['rememberMeCookie']);
			setcookie('rememberMeCookie', null, -1, '/');
		}

		// destroy the session 
		session_destroy(); 
		session_regenerate_id(true);

		redirectTo("index");
	}

	
	function flashMessage($msg,$flag){
		if ( $flag == 1){
			$result = "<div class='result-success'>" . $msg . "</div>";
		}
		else{
			$result = "<div class='result-failed'>" . $msg . "</div>";
		}
		return $result;
	}
	
	// function uploadAvatar($username, $currentAvatar, $rawfilename){


	// 	// Upload and Rename File

	// 	$filename = $rawfilename;
	// 	$default_avatar = 'default.png';
	// 	$file_basename = substr($filename, 0, strripos($filename, '.')); // get file extention
	// 	$file_ext = substr($filename, strripos($filename, '.')); // get file name
	// 	$filesize = $_FILES["profilepic"]["size"];
	// 	$allowed_file_types = array('.png','.jpg','.gif','.jpeg');	

	// 	if (in_array($file_ext,$allowed_file_types) && ($filesize < 200000))
	// 	{	
	// 		// Rename file
	// 		$avatarsuffix = date("his");
	// 		$newfilename = $username . $avatarsuffix . $file_ext;
	// 		if ( file_exists("uploads/" . $currentAvatar) && $currentAvatar != $default_avatar )
	// 		{
	// 			// file already exists error
	// 		    unlink("uploads/" . $currentAvatar);
	// 		}
					
	// 		if( !move_uploaded_file($_FILES["profilepic"]["tmp_name"], "uploads/" . $newfilename) ){
	// 			$newfilename = $default_avatar;
	// 		}
	// 		else{
	// 			$newfilename = $username . $avatarsuffix . $file_ext;
	// 		}
	// 		return $newfilename;
				
			
	// 	}
	// 	elseif (empty($file_basename))
	// 	{	
	// 		// file selection error
	// 		echo "Please select a file to upload.";
	// 	} 
	// 	elseif ($filesize > 200000)
	// 	{	
	// 		// file size error
	// 		echo "The file you are trying to upload is too large.";
	// 	}
	// 	else
	// 	{
	// 		// file type error
	// 		echo "Only these file typs are allowed for upload: " . implode(', ',$allowed_file_types);
	// 		unlink($_FILES["profilepic"]["tmp_name"]);
	// 	}
	// 	return $newfilename;
	// }
	
	
	
	function sendEmail($mailobject, $email, $subject, $username, $message, $successmessage, $failmessage){
		
		$result = "";
		// $encoded_id = $encoded_id;
		
		//prepare $mail object
		// SMTP configuration
		// $mailobject->SMTPDebug = true;
		$mailobject->isSMTP();
		$mailobject->Host = 'smtp.mailgun.org';
		$mailobject->SMTPAuth = true;
		$mailobject->Username = 'postmaster@sandbox1cd5efa92e2f4f6d80601c22a60b78af.mailgun.org';
		$mailobject->Password = '3d453cfd4bc926c23b6b90560883b7e1';
		// $mail->SMTPSecure = 'tls';
		$mailobject->Port = 587;

		// $mail->setFrom('donotreply@.mailgun.org', 'DYC');
		$mailobject->From = 'donotreply@mydomain.com';
		$mailobject->addReplyTo('christiandimayacyac@gmail.com', 'CMD');

		// Set email format to HTML
		$mailobject->isHTML(true);
		
		
		
		
		// $mailobject->addAddress($email);
		// $mailobject->Subject($subject);
		// $mailobject->Body($message);
		
		
		//prepare and send the email
					$mailBody = $message;
						
					$mailobject->addAddress($email, $username);
					$mailobject->Subject = $subject;
					$mailobject->Body = $mailBody;
					
					// if ( $mailobject->Send() ){
						// $result = flashMessage("Your account has been created. Please check your email to activate your account.",1);
					// }
					// else{
						// $result = flashMessage("Failed to send an activation email." . $mailobject->get,0);
					// }
					try{
						$mailobject->Send();
						$result = flashMessage($successmessage,1);
					}
					catch (phpmailerException $e) {
						$result = flashMessage($failmessage . $e->errorMessage(),0);
					} catch (Exception $e) {
						$result = flashMessage($failmessage . $e->errorMessage(),0);
					}
		return $result;
	}
	
	function prepLogin($id, $username, $remember){
		//create session variables
		$_SESSION['id'] = $id;
		$_SESSION['username'] = $username;
		
		//add session security
		setFingerprint();

		
		//create cookie as user checks remember me checkbox
		if ($remember === "yes"){
			rememberMe($id);
			// var_dump($_COOKIE['rememberMeCookie']);
		}
		
		//redirect user to main page
		redirectTo("index");
	}
	
	// function getInputValue(){
	// 	return "xxx";
    // }





    //DATABASE RELATED FUNCTIONS//////////////////////////////////////////////////////
    
    // function deleteRecord($db, $tableName, $key, $keyvalue){
		
	// 	$deleted = false;
		
	// 	try{
	// 		$sqlDelete = "DELETE FROM " . $tableName . " WHERE " . $key ." = :keyvalue LIMIT 1";
			
	// 		$statement = $db->prepare($sqlDelete);
			
	// 		$statement->execute(array(':keyvalue'=>$keyvalue));
			
	// 		if ( $statement->rowCount() == 1 ){
	// 			$deleted = true;
	// 		}
			
	// 	}
	// 	catch(PDOException $ex){
	// 		//
	// 	}
		
	// 	return $deleted;
	// }
	

	
?>