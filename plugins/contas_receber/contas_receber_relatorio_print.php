<? 
session_start();
#require_once "../../conn.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title><?= utf8_decode("Relatório de Contas à Receber") ?></title>

<script language="javascript" src="../../scripts.js" type="text/javascript"></script>
<script language="javascript" src="../../jquery.js" type="text/javascript"></script>
<script language="javascript" src="../../jquery.pngFix.js" type="text/javascript"></script>
<script language="javascript" src="../../jquery.tooltip.js" type="text/javascript"></script>
<script language="javascript">
//titulos
$(document).ready(function(){
	$('[title]').tooltip({ 
		track: false, 
		delay: 0, 
		showURL: false, 
		showBody: true, 
		fade: 30 
	});
})

function exibeDiv(qualDiv){
	if (document.getElementById(qualDiv).style.display == 'none') {
		document.getElementById(qualDiv).style.display = 'block';
	} else {
		document.getElementById(qualDiv).style.display = 'none';
	}
}

//altura = screen.height;
//largura = screen.width;
//self.resizeTo(760,altura-120);
//self.moveTo(largura/2-380,60);
self.focus();
</script>

<style type="text/css">
@import url("../../fieldset.css");
.fieldset_contas{
	background: #FFFFFF url('images/bt_transporte.png') no-repeat 100% 5px;
	color: #000;
	padding-bottom: 10px;
	border-color: #000;
}
.fieldset_contas legend {
	color: #FFF;
	background: #000;
	border: 2px solid #000;
	padding: 9px;
	font-size: 20px;
	font-family: "Liberation Sans", Arial, Verdana;
	margin: 10px 0;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
} 
a { 
	color:#FFFFFF;
	text-decoration: none;
}
body{
	background-color: #FFF;
	color: #000;
	font-family: "Liberation sans", "Segoe ui", Helvetica, Arial, Tahoma, Verdana;
	font-size: 15px;
	margin: 0;
	padding: 20px;
	width: 700px;
	position: absolute;
	left: 50%;
	margin-left: -371px;
}
table {
	border-width: 0px;
	border-spacing: 0px;
	border-style: none;
	border-color: #000;
	border-collapse: collapse;
	background-color: white;
}
table th {
	border-width: 2px;
	padding: 3px;
	border-style: solid;
	border-color: #FFF;
	background-color: black;
	color: #FFFFFF;
	font-family: "Liberation Sans", Arial, Verdana;
	font-size:14px;
	font-weight: normal;
	text-align: left;
	cursor: move;
	-moz-border-radius: 5px;
}
table td {
	border-width: 1px 0 0 0;
	padding: 3px;
	border-style: dotted;
	border-color: black;
	background-color: white;
	font-family: "Liberation Sans", Arial, Verdana;
	font-size:11px;
	vertical-align: top;
}
</style>
</head>


<body onLoad="self.focus()">

<fieldset class="fieldset_contas">
<legend onclick="recarregar()" style="cursor:pointer" title="Clique para restaurar as colunas"><?= utf8_decode("Relatório de Contas à Receber") ?></legend>
<?
if($codigo_cliente){
	$SQL = mysql_query("SELECT * FROM cliente WHERE codigo=$codigo_cliente");
	$ag = mysql_fetch_object($SQL);
	echo utf8_decode("$ag->codigo - <b>". strtoupper($ag->nome_fantasia) ."</b> | $ag->cidade $ag->estado<br /> | ");
}

echo "Em ".date("d/m/Y") ." | ";

if($busca){
	echo utf8_decode("Busca: $busca | ");
}

if($venc=="aberto") echo " Com vencimentos em aberto | ";
if($venc=="pagos") echo " Com vencimentos quitados | ";

if($bv=="aberto") echo " Com BV em aberto | ";
if($bv=="pagos") echo " Com BV pagos | ";

if($fat && $fat!="*"){
	$qrc = mysql_query("SELECT codigo,titulo FROM carteira WHERE codigo='$fat'");
	$rc = mysql_fetch_array($qrc);
	echo utf8_decode(" Carteira: $rc[codigo] - $rc[titulo] | ");
}
/*
if($fat=="boleto") echo " Carteira: Boleto | ";
if($fat=="deposito") echo utf8_decode(" Carteira: Depósito | ");
if($fat=="cheque") echo " Carteira: Cheque | ";
if($fat=="recibo") echo " Carteira: Recibo | ";
if($fat=="indefinido") echo " Carteira: Indefinida | ";
*/

if(!$consulta){
	if(!strstr($data_inicial,"/00")) echo utf8_decode(" � partir de ". mydate(mydate($data_inicial)));
	if(!strstr($data_final,"/00") && $data_final && strlen($data_final)>1) echo utf8_decode(" | at� ". mydate(mydate($data_final)));
}else{
	echo "Consulta simples";
}
?>
</fieldset>

<?
if(!$consulta){
	#montar o where
	//$wr = "WHERE ";
	if($busca){
		#producao
		$campos = "codigo,titulo_servico,orcamento_obs,historico,entrega_obs,transporte_obs,email_transporte,email_vencimento";
		$campos_producao = explode(",", $campos);
		$wrb = "(";
		foreach($campos_producao as $k => $v){
			$wrb .= "$v LIKE '%$busca%' OR ";
		}
		$wrb = substr($wrb,0,strlen($wrb)-4).")";
		$SQLr = "SELECT codigo FROM producao WHERE $wrb GROUP BY codigo ORDER BY codigo DESC";
		#echo $SQLr;
		$QRr = mysql_query($SQLr);
		if(mysql_num_rows($QRr)>0){
			$wr .= " (";
			while($rs = mysql_fetch_array($QRr)){
				$wr.= " codigo_producao='$rs[codigo]' OR ";
			}
			$wr = substr($wr,0,strlen($wr)-4)." OR ";
		}
		#echo $wr;
		$wr .= " documento LIKE '%$busca%' OR bv_documento LIKE '%$busca%' OR obs LIKE '%$busca%')";
	}
	
	if($codigo_cliente){
		if($wr) $wr .= " AND ";
		$wr.= " codigo_cliente='$codigo_cliente'";
	}
	
	if(!strstr($data_inicial,"/00")){
		if($wr) $wr .= " AND ";
		$data_inicial = mydate($data_inicial);
		if($data_final && !strstr($data_final,"/00") && strlen($data_final)>1){
			$data_final = mydate($data_final);
			$wr .= "$tipo_data BETWEEN '$data_inicial' AND '$data_final'";
		}else{
			$wr .= "$tipo_data >= '$data_inicial'";
		}
	}
	
	if($wr && $venc!="*") $wr .= " AND ";
	if($venc=="aberto")$wr .= " (ISNULL(data_pagamento) OR data_pagamento LIKE '%0000%') ";
	if($venc=="pagos")$wr .= " (NOT ISNULL(data_pagamento) AND data_pagamento NOT LIKE '%0000%') ";
	
	/*
	if($wr && $bv!="*") $wr .= " AND ";
	if($bv=="aberto")$wr .= " (ISNULL(bv_data_pagamento) OR bv_data_pagamento LIKE '%0000%') AND NOT ISNULL(bv_valor) ";
	if($bv=="pagos")$wr .= " (NOT ISNULL(bv_data_pagamento) AND bv_data_pagamento NOT LIKE '%0000%') AND NOT ISNULL(bv_valor)";
	*/
	
	if($wr && $fat && $fat!="*"){
		#$wr .= " AND ";
		#if($fat=="boleto")$wr .= " LENGTH(documento)>1 AND documento NOT LIKE '%dep%' AND documento NOT LIKE '%cheque%' AND documento NOT LIKE '%recibo%'";
		#if($fat=="deposito")$wr .= " documento LIKE '%dep%'";
		#if($fat=="cheque")$wr .= " documento LIKE '%cheque%'";
		#if($fat=="recibo")$wr .= " documento LIKE '%recibo%'";
		#if($fat=="indefinido")wr .= " LENGTH(documento)<1";
		
		#novo
		$qrc = mysql_query("SELECT codigo,titulo FROM carteira WHERE codigo='$fat'");
		$rc = mysql_fetch_array($qrc);
		$wr .= " carteira='$fat'";
		#fim do novo
	}#fat
	
	if($ord=="data_vencimento") $ord = $tipo_data;
	
	$wr = "WHERE $wr";
	
	$SQLr = "SELECT * FROM contas_receber $wr ORDER BY $ord DESC, codigo";
	if($return) echo $SQLr; #die;
}else{
	$SQLr = str_replace('\\','',$consulta);
}
#echo $SQLr;
$QR = mysql_query(trim($SQLr));

if(!mysql_num_rows($QR)){ ?>
	<fieldset class='fieldset_atencao'>
	<legend><?= utf8_decode("Ops!") ?></legend>
	Sem registros localizados.<br /><br />
	</fieldset>
<? }else{ ?>

<table>
	<tr title="Ocultar coluna">
	<th id="c1_1" onclick="ocultar('c1')">Cod</th>
	<th id="c2_1" onclick="ocultar('c2')">Titulo</th>
	<? if(!$codigo_cliente){ ?>
	<th id="c3_1" onclick="ocultar('c3')">Cliente</th>
	<? } ?>
	<th id="c4_1" onclick="ocultar('c4')">Autor.</th>
	<th id="c5_1" onclick="ocultar('c5')">Venc.</th>
	<th id="c6_1" onclick="ocultar('c6')">Valor</th>
	<? if($venc!="aberto"){ ?>
	<th id="c7_1" onclick="ocultar('c7')">Pagamento</th>
	<? } ?>
	<th id="c8_1" onclick="ocultar('c8')">Documento</th>
	<th id="c9_1" onclick="ocultar('c9')">Obs</th>
	<th id="c10_1" onclick="ocultar('c10')">Transporte</th>
	<!--
	<th id="c11_1" onclick="ocultar('c11')">BV valor</th>
	<th id="c12_1" onclick="ocultar('c12')">BV pg</th>
	<th id="c13_1" onclick="ocultar('c13')">BV doc</th>
	-->
	</tr>

	<?
	$voltas = 2;
	while($rs = mysql_fetch_array($QR)){
	$bgt="";
	if($rs[data_pagamento] && $rs[data_pagamento]!="0000-00-00") $bgt = " style='background-color:#c7c7c7'"; ?>
	<tr>
	
	<!--onclick="vencimento_editar('<?= $rs[codigo_producao] ?>','<?= $rs[codigo_cliente] ?>','<?= $rs[codigo] ?>')" -->
	<td id="c1_<?= $voltas ?>"><b><? if ($rs[codigo_producao]){ echo $rs[codigo_producao]; }else{ echo "?"; } ?></b></td>
	
	<td id="c2_<?= $voltas ?>"<?= $bgt ?>>
	<? #titulo
	if ($rs[codigo_producao]){
	$SQL2 = mysql_query("SELECT titulo_servico,autorizado_data FROM producao WHERE codigo=$rs[codigo_producao]");
	$ti = mysql_fetch_object($SQL2);
	echo utf8_decode("<b>$ti->titulo_servico</b>");
	$aut = $ti->autorizado_data;
	}else{
	echo "<b>$rs[titulo]</b>";
	$aut = "";
	}
	?>
	</td>
	
	
	<? if(!$codigo_cliente){ ?>
	<td id="c3_<?= $voltas ?>"<?= $bgt ?>>
	<? #titulo
	$SQL3 = mysql_query("SELECT nome_fantasia FROM cliente WHERE codigo='$rs[codigo_cliente]'");
	$ag = mysql_fetch_object($SQL3); ?>
	<div style="overflow:hidden; height:14px"><b><?= utf8_decode("$ag->nome_fantasia") ?></b></div>
	</td>
	<? } ?>
	
	
	<td id="c4_<?= $voltas ?>">
	<? #autorizado
	if($aut){
	$ts = explode("-",$aut);
	echo $ts[2]."/".$ts[1]."/".$ts[0];
	}else{
	echo "__/__/____";
	}
	?>
	</td>
	
	
	<? $bg="";
	if((!$rs[data_pagamento] || $rs[data_pagamento]=='0000-00-00') && $rs[data_vencimento] <= date("Y-m-d")) $bg = "style='background-color:#FF0000; color:#FFFFFF;'"; ?>
	<td  id="c5_<?= $voltas ?>" <?= $bg ?> <? if(!$bg) echo $bgt ?>>
	<? #venciemnto
	if($rs[data_vencimento]){
	$ts = explode("-",$rs[data_vencimento]);
	echo $ts[2]."/".$ts[1]."/".$ts[0];
	}else{
	echo "__/__/____";
	}
	?>
	</td>
	
	<? #valor ?>
	<td id="c6_<?= $voltas ?>" align="right"<?= $bgt ?>>
	&nbsp;<?= $rs[valor] ?>
	<? if(!$total)$total=0;
	$total = $rs[valor]+$total ?>
	</td>
	
	
	<? if($venc!="aberto"){ ?>
	<td id="c7_<?= $voltas ?>"<?= $bgt ?>>
	<? #data pg
	if($rs[data_pagamento]){
	$ts = explode("-",$rs[data_pagamento]);
	echo $ts[2]."/".$ts[1]."/".$ts[0];
	}else{
	echo "&nbsp;";
	}
	?>
	</td>
	<? } ?>
	
	
	<td id="c8_<?= $voltas ?>"><?= $rs[documento] ?></td>
	<td id="c9_<?= $voltas ?>"><?= nl2br($rs[obs]) ?></td>
	<td id="c10_<?= $voltas ?>">
	<? #transporte
	if ($rs[codigo_producao] && !strstr($codigo_producao,$rs[codigo_producao])){
	$SQL3 = mysql_query("SELECT transporte_obs FROM producao WHERE codigo=$rs[codigo_producao]");
	$en = mysql_fetch_object($SQL3);
	echo "$en->transporte_obs";
	$codigo_producao .= "_".$rs[codigo_producao];
	}
	?>
	</td>
	
	<!--
	<td id="c11_<?= $voltas ?>"><?= $rs[bv_valor] ?></td>
	<td id="c12_<?= $voltas ?>">
	<? #pagamento bv
	if($rs[bv_data_pagamento]){
	$ts = explode("-",$rs[bv_data_pagamento]);
	echo $ts[2]."/".$ts[1]."/".$ts[0];
	}else{
	echo "&nbsp;";
	}
	?>
	</td>
	<td id="c13_<?= $voltas ?>"><?= $rs[bv_documento] ?></td>
	<? $ttbv = $rs[bv_valor]+$ttbv; ?>
	-->
	
	</tr>
	<? 
	$voltas++;
	} ?>
	<tr>
	<td id="c1_<?= $voltas ?>">&nbsp;</td>
	<td id="c2_<?= $voltas ?>">&nbsp;</td>
	<? if(!$codigo_cliente){ ?><td id="c3<?= $voltas ?>">&nbsp;</td><? } ?>
	<td id="c4_<?= $voltas ?>" align="right">&nbsp;</td>
	<td id="c5_<?= $voltas ?>" align="right">&nbsp;</td>
	<td id="c6_<?= $voltas ?>" align="right" style="width:70px">Total<br /><b>R$&nbsp;<?= number_format($total,2,",",".") ?></b></td>
	<? if($venc!="aberto"){ ?>
	<td id="c7_<?= $voltas ?>">&nbsp;</td>
	<? } ?>
	<td id="c8_<?= $voltas ?>">&nbsp;</td>
	<td id="c9_<?= $voltas ?>">&nbsp;</td>
	<td id="c10_<?= $voltas ?>">&nbsp;</td>
	<!--
	<td id="c11_<?= $voltas ?>" align="right" bgcolor="#000000" style="width:70px ">Total BV<br /><b>R$&nbsp;<?= number_format($ttbv,2,",",".") ?></b></td>
	<td id="c12_<?= $voltas ?>">&nbsp;</td>
	<td id="c13_<?= $voltas ?>">&nbsp;</td>
	-->
	</tr>
</table>

<? }#registros ?>

<form name="print">
<input type="hidden" name="consulta" value="<?= $consulta ?>" />
</form>
</body>

<script language="javascript">
function ocultar(id){
	for(k=1;k<=<?= $voltas ?>;k++){
		exibeDiv(id+"_"+k);
		//alert(id+"_"+k);
	}
}
<? if($bv=="*"){ ?>
ocultar('c11');
ocultar('c12');
ocultar('c13');
<? } ?>

function recarregar(){
	<? if($consulta){ ?>
            with(document.print)
            {
            method = "POST";
            action = "";
            submit();
            }
	<? }else{ ?>
	document.location.reload();
	<? } ?>
}
</script>

</html>
