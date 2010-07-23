<?
@session_start(); 
$server = "cpmy0009.servidorwebfacil.com";
#$server = "localhost";

if(!$_SESSION[settings_user]){
    //se informar a empresa ou ainda nao informou nada 
    $user =     "printdot_settings";
    $password = "printdot";
    $dbase =    "printdot_settings";
}else{
    //se ja estiver logado, usa essa conexao com o BD
    // isto nao é o usuario do sistema, é so do BD
    $user =     $_SESSION[settings_user];
    $password = $_SESSION[settings_password];
    $dbase =    $_SESSION[settings_dbase];
    $empresa =  $_SESSION[settings_empresa];
}

require_once "functions_db.php";
require_once "functions_misc.php";
conn($server,$user,$password,$dbase);
#echo $server.$user.$password.$dbase;

$now = gmdate('D, d M Y H:i:s') . ' GMT';
@header('Expires: ' . $now);
@header('Last-Modified: ' . $now);
@header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
@header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
@header('Pragma: no-cache'); // HTTP/1.0

#echo $now;
//para upload de images e demais
?>
