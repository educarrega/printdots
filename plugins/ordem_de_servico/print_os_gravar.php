<?
@session_start();
#require_once "../../conn.php";
$QR = mysql_query("SELECT impressao FROM producao WHERE codigo=$codigo");
$rs = mysql_fetch_array($QR);
#gravar o dia e a hora que a impressão da OS foi feita
$impressao = date("d/m/Y H:i:s ").$_SESSION[user_detalhes]."\n".$rs[impressao];
upd("producao","impressao='$impressao'","WHERE codigo='$codigo'");
echo '<small>'.substr($impressao,0,strpos($impressao,"\n")).'</small>';
atualizar();
?>
