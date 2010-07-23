<? 
session_start();
#require_once "../../conn.php";
upd("producao","agencia=1","WHERE agencia='0' OR agencia=null",1);
echo "(feito)<br />";
$SQLr = sel("contas_receber","codigo_producao","WHERE codigo_cliente='0'",1);
print_r($SQLr);
echo mysql_num_rows($SQLr);
while($rs = mysql_fetch_array($SQLr)){
	$codigo_cliente = campo("producao","agencia","WHERE codigo='$rs[codigo_producao]'");
	#echo "$codigo_cliente<br />";
	upd("contas_receber","codigo_cliente=$codigo_cliente","WHERE codigo_producao=$rs[codigo_producao]",1);
	echo "(feito)<br />";
}
?>
