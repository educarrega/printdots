<meta http-equiv="Content-Type"
    content="text/html; charset=utf-8" />

<?
include "conn.php";

$tx_old = array('Ã£', 'Ã©','Ã¡','Ãª','Ãº ','Ã§','â€“','Ã”','Â°','Ã‰','Ã','Ã“','Ã‡','Ãƒ','Ã','Ã­');
$tx_new = array('�','�','�','�','ú','�','à','ô','°','É','�','Ó','Ç','Ã','Á','�');
#$tabela = array('transporte','producao_status','cliente','contas_receber','controle','historico','plotter','producao','produto');
$tabela = array('contas_receber');

foreach ($tabela as $tb) {
	$sql = "SELECT * FROM $tb";
	$qr = mysql_query($sql);
	$cl = mysql_num_fields($qr);
	#echo $cl.'<br>';
	for($x=0; $x<$cl; $x++) {
		$nome_campo = mysql_field_name($qr,$x);
		#echo $nome_campo.'<br>';
		for($f = 0; $f < count($tx_old); $f++) {
		    	mysql_query("UPDATE $tb SET $nome_campo = replace($nome_campo,'".$tx_old[$f]."','".$tx_new[$f]."')");
			echo "UPDATE $tb SET $nome_campo = replace($nome_campo,'$tx_old[$f]','$tx_new[$f]');<br />";
		}
	} 
}

?>
