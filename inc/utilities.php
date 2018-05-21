<?php 
	// if __CONFIG__ is not defined, do not load this file
    if( !defined('__CONFIG__') ) {
        exit('Config File is not defined.');
    }
	require_once "config.php";
	

	//DB FUNCTIONS

	function updateTableField($con, $table_name, $field_name, $field_value, $matching_filed, $matching_field_value) {
		$sql_update = "UPDATE $table_name SET $field_name = :field_value WHERE $matching_filed = :matching_field_value LIMIT 1";
		$stmt = $con->prepare($sql_update);
		$stmt->execute(array(":field_value"=>$field_value, ":matching_field_value"=>$matching_field_value));

		if ( $stmt->rowCount() == 1 ) {
			return true;
		}
		else {
			return false;
		}
	}


	//MISC FUNCTIONS///////////////////////////////////////////////////////////
	
	function redirectTo($page){
		header("location:" . $page . ".php");
	}


	function setUserCookie($user_name) {
		//encrypt cookie value
		$encrypted_cookie_value = getBase64EncodedValue(Constant::$userNameEncKey, $user_name);

		//set a cookie named: "rememberFesbuk" and will expire after 30days
		setcookie("rememberFesbuk", $encrypted_cookie_value, time()+60*60*24*100,"/");
		return;
	}


	//requires encryption key and string to encrypt
	//encrypts using base64_encode
	function getBase64EncodedValue($key, $value){
		$encoded_data = "";
			
			if (!empty($key) && !empty($value) ){
				$encoded_data = base64_encode($key . $value);
			}
		
		return $encoded_data;
	}
	
	//requires encryption key and encrypted string
	//decrypts using base64_decode and explode() 
	//last parameter is the raw data
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

	function getTrimmedEncodedValue($key, $value) {
		$encoded_string = getBase64EncodedValue($key, $value);
		//count the number of "=" and append the result to the string after removing the "=" symbol
		$num_of_uquals = substr_count($encoded_string, "=" );
		$encoded_string = str_replace("=", "", $encoded_string);
		$encoded_string.= "$num_of_uquals";
				
		return $encoded_string;
	}

	function getTrimmedDecodedValue($key, $value) {
		//get the last character of the string that denotes the number of "=" to be appended
		
		$num_of_uquals = substr($value, -1);
		$value = substr_replace($value, "", -1, $num_of_uquals);
		if ( $num_of_uquals > 0 ) {
			for ($i=1; $i <= $num_of_uquals; $i++) {
				$value .= "=";
			}
		}
		$encoded_string = getBase64DecodedValue($key, $value);

		return $encoded_string;
	}

	//checks if the save cookie named: "rememberFesbuk" is valid
    function isCookieValid($con){
		
		$isValid = false;
		
		if ( isset($_COOKIE['rememberFesbuk'])) {
			$user_name = getBase64DecodedValue(Constant::$userNameEncKey, $_COOKIE['rememberFesbuk']);
			
			try{
				$sqlQuery = "SELECT * FROM users WHERE user_name = :user_name";
				
				$stmtQuery = $con->prepare($sqlQuery);
				
				$stmtQuery->execute(array(':user_name'=>$user_name));
				
				//if match record is found, create session variables
				if ( $rs = $stmtQuery->fetch() ){
					$id = $rs['user_id'];
					$username = $rs['user_name'];
					
					$_SESSION['user_id'] = getTrimmedEncodedValue(Constant::$userIdEncKey, $id);
					$_SESSION['user_name'] = $username;
					$isValid = true;
				}
				else{
					$isValid = false;
					redirectTo('logout');
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
		session_unset($_SESSION['user_name']);
		
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
		$_SESSION['user_name'] = $username;
		
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