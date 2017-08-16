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
	*    -6 Ha habido un fallo al hacer el registro
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
		$values = ''; 
		foreach ($vals as $key => $value) {
			$fils .= "$key, ";
			$values .= "$value, ";
		}
		$fils .= ")";
		$values .= ")";

		switch ($toMake) {
			case 'insert':
				$query = "INSERT INTO $table $fils VALUES $values";
				break;			
			default:
				# code...
				break;
		}
		return $query;
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


	public function newUser($name, $lastname, $nick, $cours, $mail, $pssword1, $pssword2){
		if ($nick == '' || $pssword1 == '' || $psswrd2 == '' || $mail == '') return -4;
		if ($pssword1 !== $psswrd2) return -5;
		$userName = mysqli_real_escape_string($this->sql, $userName);
		$mail = mysqli_real_escape_string($this->sql, $mail);
		$vals = [
			'name' => "'$name'",
			'lastname' => "'$lastname'",
			'cours' => "'$cours'",
			'nick' => "'$nick'", 
			'mail' => "'$mail'", 
			'psswrd' => "'$psswrd'"
			];

		$query = $this->makeQuery('insert', 'users', $vals);
		return  (mysqli_query($sql, $query)) ? 2 : -6;
	}


}
echo "todo correcto";
$u = new User();




?>