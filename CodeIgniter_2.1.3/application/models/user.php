<?php

Class User extends CI_Model
{

	function login($username, $password)
	{
		$db = new PDO('sqlite:./application/db/guild');
		$result = $db->query("SELECT * FROM Users WHERE username='" . $username . "' AND password='" . $password . "' LIMIT 1;");


		if(count($result) == 1)
		{
		     return $result;
		}
		else
		{
		     return false;
		}
	}
        
        function getEsl($username)
        {
                $db = new PDO('sqlite:./application/db/guild');
                $result = $db->query("SELECT esl FROM Users WHERE username='" . $username . "' LIMIT 1;");
                
                if(count($result) == 1)
                {
                    foreach ($result as $row)
                    {
                        $esl = $row['esl'];
                    }
                    
                    return $esl;
                }
                
                return '';
        }
        
        function setEsl($username, $esl)
        {
                $db = new PDO('sqlite:./application/db/guild');
                $result = $db->query("UPDATE Users SET esl='" . $esl . "' WHERE username='" . $username . "';");
        }
     
        
        function register($username, $password, $usertype, $esl)
        {
                $db = new PDO('sqlite:./application/db/guild');
                $result = $db->query("INSERT INTO Users VALUES ('" . $username . "','" . $password . "','" . $usertype . "','" . $esl . "',0);");
        }
        
        function getALlEsls()
        {
                $db = new PDO('sqlite:./application/db/guild');
                $result = $db->query("SELECT esl FROM Users;");

                if(count($result) >= 1)
                {
                    $data = array();

                    foreach ($result as $row)
                    {
                        array_push($data, $row['esl']);
                    }

                    return $data;
                }
                
                return $result;
        }

        function getTop3Esls()
        {
            $db = new PDO('sqlite:./application/db/guild');
                $result = $db->query("SELECT esl FROM Users ORDER BY rating DESC LIMIT 3;");

                if(count($result) >= 1)
                {
                    $data = array();

                    foreach ($result as $row)
                    {
                        array_push($data, $row['esl']);
                    }

                    return $data;
                }

                return $result;
        }

        function raiseRank($user = '')
        {
            $db = new PDO('sqlite:./application/db/guild');
            $result = $db->query("SELECT rating FROM Users WHERE username='".$user."';");

            if(count($result) >= 1)
                {
                    $data = array();

                    foreach ($result as $row)
                    {
                        $rating = $row['rating'] + 0.5;
                        $db->query("UPDATE Users SET rating='".$rating."' WHERE username='".$user."';");
                    }

                    return $data;
                }
        }
}
?>