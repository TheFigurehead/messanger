<?php

	interface iAuth
	{
		public function authUser();
	}

	//class that allow to operate the users
	//start

	class Auth implements iAuth
	{

		static private $email;
		static private $password;
		static private $login;
		static private $db_connect;

		function __construct($email, $login, $password, $db_connect)
		{
			self::$email = $email;
			self::$password = $password;
			self::$login = $login;
			self::$db_connect = $db_connect;
		}

		public function authUser()
		{
			try
			{
				$sql =  self::$db_connect->prepare("SELECT * FROM users WHERE email=:email and password=:password");

				$sql->bindParam(':email', self::$email);
				$sql->bindParam(':password', self::$password);

				$sql->execute();

				$result = $sql->fetchColumn();

				if (!empty($result))
				{
					echo "<br/>Hi, ".self::$login.".";

					if((!isset($_COOKIE['email'])) && (!isset($_COOKIE['password'])))
					{
						setcookie("email", self::$email);
						setcookie("password", self::$password);
					}

					if(isset($_COOKIE['counter']))
					{
						$counter = 1+$_COOKIE['counter'];
						setcookie("counter",$counter);
						echo('<br />Вы посетили эту страницу '. $counter .' раз');
					}
					else
					{
						setcookie("counter",1);
					}

					$user = new User(self::$email, self::$login, self::$password);

					return $user;
				}
				else
				{
					echo "<br/>There is no this user.";
					return false;
				}
			}
			catch(PDOException $e)
			{
				echo "Error: " . $e->getMessage();
				return null;
			}
		}

		public function logOut()
		{
			if((isset($_COOKIE['email'])) && (isset($_COOKIE['password'])))
			{
				unset($_COOKIE['email']);
				unset($_COOKIE['password']);
			}
			else
			{
				echo "<br/>Something went wrong.";
				return null;
			}
		}
	}

	//end

	class User
	{
		static private $email;
		static private $password;
		static private $login;

		function __construct($email, $login, $password)
		{
			self::$email = $email;
			self::$password = $password;
			self::$login = $login;
		}
	}

	// class that allow to operate with datebase
	//begin

	class DBproc
	{
		public function connectDB($servername, $username, $db_password, $dbname)
		{
			try
			{
				$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				echo "Connected successfully";
				return $conn;
			}
			catch(PDOException $e)
			{
				echo "Connection failed: " . $e->getMessage();
			}
		}

		public function checkUserExist($email, $login, $db_connect)
		{
			try
			{
				$sql = ("SELECT * FROM users WHERE email=:email or login=:login");

				$sth = $db_connect->prepare($sql);

				$sth->execute(array(':email'=>$email, ':login'=>$login));

				$result = $sth->fetchColumn();

				if (empty($result))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			catch(PDOException $e)
			{
				echo "Error: " . $e->getMessage();
			}
		}

		public function insertUser($email, $login, $password, $db_connect)
		{
			$bool = DBproc::checkUserExist($email, $login, $db_connect);

			if($bool)
			{
				try
				{
					$sql = "INSERT INTO users (email, login, password) VALUES (:email, :login, :password)";

					$sth = $db_connect->prepare($sql);

					$sth->execute(array(':email'=>$email, ':login'=>$login, ':password'=>$password));

					echo "<br/> New user created successfully";
				}
				catch(PDOException $e)
				{
					echo $sql . "<br>" . $e->getMessage();
				}
			}
			else
			{
				echo "<br/> Soory, the user is already exist.";
				return null;
			}
		}
	}

	//end
