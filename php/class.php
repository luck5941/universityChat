<?php
require ('.errors.php');
define("HOST", "localhost");
define("USER_DB", "luck");
define("PASSWD", "");
define("NAME_BD", "chat");

class User {
	/*
	*Esta clase se encarga de determinar todas las acciones que
	*puede hacer con su cuenta.
	*
	*Conectecta con la base de datos cada vez que se crea una instancia suya.
	*Devuelve los siguientes codigos:
	*--------------------ERRORES--------------------------------
	*    -1: El usuario no está registrado
	*    -2: La contraseña no está en la base de datos
	*    -3: No coincide la contraseña para el usuario
	*    -4: En el registro, hay campos obligatorios vacios
	*    -5: Las contraseñas no coinciden
	*    -6: El nick ya está registrado
	*    -7: El mail ya está registrado
	*    -8: Fallo en el registro
	*--------------------EXITO--------------------------------
	*     1: Login correcto
	*     2: Nuevo usuario creado
	*
	*/
	
	function __construct(){
		$this->sql = mysqli_connect(HOST, USER_DB, PASSWD, NAME_BD);
	}

	private function makeQuery($toMake, $table, $vals) {
		$query = '(';
		$filds = '(';
		$values = '('; 
		foreach ($vals as $key => $value) {
			$filds .= "$key, ";
			$values .= "$value, ";
		}
		$vals = explode(', ', $values);
		$filds = explode(', ', $filds);
		array_pop($vals);
		array_pop($filds);
		$vals = join(", ", $vals);
		$filds = join(", ", $filds);
		$filds .= ")";
		$vals .= ")";

		switch ($toMake) {
			case 'insert':
				$query = "INSERT INTO $table $filds VALUES $vals";
				break;			
			default:
				echo $toMake;
				break;
		}
		return $query;
	}

	private function checkIsLogin($query, $nick, $mail) {
		$check =  mysqli_query($this->sql, $query);
		$names = [];
		$mails = [];
		while ($fila = mysqli_fetch_assoc($check)) {
			$names[] = $fila['nick'];
			$mails[] = $fila['mail'];
		}
		if (in_array($nick, $names)) return -6;
		if (in_array($mail, $mails)) return -7;
		return -8;
	}

	
	public function login($user, $pssw) {
		$user = mysqli_real_escape_string($this->sql, $user);
		$check =  mysqli_query($this->sql, "SELECT id_users from users where mail='$user' or nick='$user'");
		$ids = [];
		$i = 0;
		while ($fila = mysqli_fetch_assoc($check)) {
			$id_u = $fila['id_users'];
		}
		if (!isset($id_u))
			return -1;

		$psswrd = mysqli_real_escape_string($this->sql, $pssw);
		$check =  mysqli_query($this->sql, "SELECT id_users from users where psswrd='$psswrd'");
		while ($fila = mysqli_fetch_assoc($check)) {
			$ids[] = $fila['id_users'];
		}
		if 	(count($ids) === 0)
			return -2;

		return in_array($id_u, $ids) ? 1 : -3;		
	}


	//public function newUser($name, $lastname, $nick, $cours, $mail, $pssword1, $pssword2){
	public function newUser($data){
		if ($data['nick'] == '' || $data['pssword1'] == '' || $data['pssword2'] == '' || $data['mail'] == '') return -4;
		if ($data['pssword1'] !== $data['pssword2']) return -5;
		$name = mysqli_real_escape_string($this->sql, $data['name']);
		$lastname = mysqli_real_escape_string($this->sql, $data['lastname']);
		$nick = mysqli_real_escape_string($this->sql, $data['nick']);
		$mail = mysqli_real_escape_string($this->sql, $data['mail']);
		$psswrd = password_hash(mysqli_real_escape_string($this->sql, $data['pssword1']), PASSWORD_BCRYPT, ['cost' => 12]);
		$cours = mysqli_real_escape_string($this->sql, $data['cours']);
		$vals = [
			'name' => "'$name'",
			'lastname' => "'$lastname'",
			'cours' => "'$cours'",
			'nick' => "'$nick'", 
			'mail' => "'$mail'", 
			'psswrd' => "'$psswrd'"
			];
		
		$query = $this->makeQuery('insert', 'users', $vals);
		if (mysqli_query($this->sql, $query)) return "2" ;
		//Comprobamos si existe el nombre o el mail en la bbdd o si el error es por otro motivo
		$query = "SELECT nick, mail from users where mail='$mail' or nick='$nick'";
		return $this->checkIsLogin($query, $nick, $mail);
		
		//return  (mysqli_query($this->sql, $query)) ? "2" : "-6";
	}


}





?>