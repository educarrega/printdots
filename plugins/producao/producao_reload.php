<? 
@session_start();
#require_once "../../conn.php";

$atualizacao = controle('alteracao');
if(isset($_SESSION[ultimas])){
	if($_SESSION[ultimas] != $atualizacao){
		$_SESSION[ultimas] = $atualizacao;
		if(!strstr($atualizacao,$_SESSION[user_detalhes])) echo "$atualizacao";
		#so atualiza a lista de producao se for outro usuario a fazer a alteracao
	}
}else{
	$_SESSION[ultimas] = $atualizacao;
}
?>
