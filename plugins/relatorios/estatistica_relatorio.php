<html>
<head>
<title>Estat&iacute;stica</title>
<style type="text/css">
body,td {
	font:11px Tahoma;
}
#bloco{
	background:#f6f6f6;
	padding:2px;
	border:1px solid #FFFFFF;
	overflow:hidden;
}
.borda {
	border:1px solid #333333;
}
a:link {
	color: #000000;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #000000;
}
a:hover {
	text-decoration: none;
	color: #666666;
}
a:active {
	text-decoration: none;
	color: #666666;
}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
<body onLoad="self.focus()">

<? if($voltar){ ?><a href="javascript:history.back();"><strong>Voltar</strong></a> | <? } ?>
<b>Estat&iacute;sticas de Produ&ccedil;&otilde;es: </b> 
<? require "conn.php" ?>
<? if($agencia!="todos"){
	$SQL = mysql_query("SELECT * FROM cliente WHERE codigo=$agencia");
	$ag = mysql_fetch_object($SQL);
	echo " | <b>$ag->nome_fantasia</b> ";
} else {
	echo "Todas as Agências ";
}
?>| <?= date("d/m/Y") ?> |&nbsp;
<? 
if(!strstr($data_inicial,"/00") && !strstr($data_final,"/00")) echo "De: $data_inicial até $data_final";
if(strstr($data_inicial,"/00") && !strstr($data_final,"/00")) echo "Até: $data_final";
if(!strstr($data_inicial,"/00") && strstr($data_final,"/00")) echo "Ã partir de: $data_inicial";
if(strstr($data_inicial,"/00") && strstr($data_final,"/00")) echo "Todos os períodos";
?>
	<hr noshade size="1">
	
	<table cellpadding='2' cellspacing='1' width='100%' bgcolor="#000000">
	
	<tr style="color:#FFFFFF;">
	<? if($agencia!="todos"){ ?>
	<td style="background-color:#000000; font-weight:bold; width:30px" align="center">Cod</td>
	<td style="background-color:#000000; font-weight:bold; ">Titulo</td>
	<td style="background-color:#000000; font-weight:bold; ">Qt</td>
	<td style="background-color:#000000; font-weight:bold; ">m2</td>
	<td style="background-color:#000000; font-weight:bold; ">Data</td>
	<? }else{ ?>
	<td style="background-color:#000000; font-weight:bold; ">Agência</td>
	<td style="background-color:#000000; font-weight:bold; ">m2</td>
	<td style="background-color:#000000; font-weight:bold; ">Qt</td>
	<td style="background-color:#000000; font-weight:bold; ">Gráfico</td>
	<? } ?>
	</tr>
	
	
	<? 
	#montar o where
	$wr = "WHERE quantidade<>''";
	if($agencia!="todos")$wr.= " AND agencia='$agencia'";
	if(!strstr($data_inicial,"/00")){
		$data_inicial_o = $data_inicial;
		$tm = explode("/",$data_inicial);
		$tmY = $tm[2]; if(!$tmY)$tmY = date("Y");
		$tmM = $tm[1]; if(!$tmM)$tmM = date("m");
		$tmD = $tm[0]; if(!$tmD)$tmD = $data_inicial;
		$vl = "'".$tmY."-".$tmM."-".$tmD."'";
		$data_inicial=$vl;
		if (!strstr($data_final,"/00")){	
			$data_final_o = $data_final;
			#between inicial e final
			$tm = explode("/",$data_final);
			$tmY = $tm[2]; if(!$tmY)$tmY = date("Y");
			$tmM = $tm[1]; if(!$tmM)$tmM = date("m");
			$tmD = $tm[0]; if(!$tmD)$tmD = $data_final;
			$vl = "'".$tmY."-".$tmM."-".$tmD."'";
			$data_final=$vl;
			$wr .= " AND data_entrega BETWEEN $data_inicial AND $data_final";
		}else{
			#maior que data inicial
			$wr .= " AND data_entrega >= $data_inicial";
		}
	}
	
	if($agencia=="todos"){
		$SQL = "SELECT agencia,SUM(quantidade) as soma, SUM(digital_folhas) as sm2 FROM producao $wr GROUP BY agencia ORDER BY sm2 DESC";
		$sm = mysql_fetch_object(mysql_query("SELECT SUM(quantidade) as soma, SUM(digital_folhas) as sm2 FROM producao $wr"));
		$total = $sm->soma;
		$sm2 = $sm->sm2;
		#echo $total;
	}else{
		$SQL = "SELECT codigo,agencia,titulo_servico,quantidade,digital_folhas,m2,data_entrega FROM producao $wr ORDER BY quantidade DESC";
	}
	#echo $SQL;
	$SQL = mysql_query($SQL);
	
	
	while($rs = mysql_fetch_array($SQL)){ ?>
	<tr valign="top">
	
	<? if($agencia=="todos"){ ?>
	<td bgcolor="#FFFFFF">
	<? #titulo
	#echo $rs[soma]; 
	$SQL3 = mysql_query("SELECT codigo,nome_fantasia FROM cliente WHERE codigo='$rs[agencia]'");
	$ag = mysql_fetch_object($SQL3); ?>
	<a href="?agencia=<?= $ag->codigo ?>&data_inicial=<?= $data_inicial_o ?>&data_final=<?= $data_final_o ?>&voltar=1"><b><?= $ag->nome_fantasia ?></b></a>
	</td>
	<? } ?>
	
	<? if($agencia!="todos"){ ?>
	<td align="center"><b style="color:#FFFFFF"><?= $rs[codigo] ?></b></td>

	<td bgcolor="#FFFFFF"><b><?= $rs[titulo_servico] ?></b></td>
	
	<td bgcolor="#FFFFFF"><?= $rs[quantidade] ?></td>
	
	<td bgcolor="#FFFFFF"><?= $rs[digital_folhas] ?></td>

	<td bgcolor="#FFFFFF"><? $dt = explode("-",$rs[data_entrega]); echo "$dt[2]/$dt[1]/$dt[0]" ?></td>
	
	<? 	
	$smg = $rs[quantidade] + $smg;
	$m2g = $rs[digital_folhas] + $m2g;
	} else { ?>
	<?
	$smg = $rs[soma] + $smg;
	$m2g = $rs[sm2] + $m2g;
	?>
	<td bgcolor="#FFFFFF"><?= $rs[sm2] ?></td>
	<td bgcolor="#FFFFFF"><?= $rs[soma] ?></td>
	<td bgcolor="#FFFFFF"><img src="images/quadro_preto.gif" height="5" width="<?= $rs[sm2]*100/$sm2*2 ?>"> <?= substr($rs[sm2]*100/$sm2,0,4) ?>%</td>
	<? } ?>
	
	</tr>
	<? }#while ?>
	
	
	
	
	<tr>
	<? if($agencia!="todos"){ ?>
	<td bgcolor="#000000" style="color:#FFFFFF ">&nbsp;</td>
	<? } ?>
	<td align="right" style="color:#FFFFFF ">Total: </td>
	<td bgcolor="#000000" style="color:#FFFFFF "><b><?= $m2g ?></b></td>
	<td bgcolor="#000000" style="color:#FFFFFF "><b><?= $smg ?></b></td>
	<td bgcolor="#000000" style="color:#FFFFFF ">&nbsp;</td>
	
	
	
	</tr>
</table>
	
</body>
</html>