<? 
session_start();
#require_once "../../conn.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Entregas para <?= $data_inicial ?></title>
<style type="text/css">
@import url("../../fieldset.css");
.fieldset_entrega{
	background: #FFFFFF url('images/bt_transporte.png') no-repeat 98% 5px;
	color: #000;
	padding-bottom: 10px;
	border-color: #000;
}
.fieldset_entrega legend {
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
	border-bottom: 1px dotted #ccc;
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
	-moz-border-radius: 5px;
}
table td {
	border-width: 1px 0 0 0;
	padding: 3px;
	border-style: dotted;
	border-color: black ;
	background-color: white;
	font-family: "Liberation Sans", Arial, Verdana;
	font-size:11px;
	vertical-align: top;
}
</style>

<script language="javascript" src="../../jquery.js" type="text/javascript"></script>
<script language="javascript" src="../../jquery.pngFix.js" type="text/javascript"></script>
<script language="javascript" src="../../jquery.tooltip.js" type="text/javascript"></script>

<script language="javascript">
	
//titulos
$('[title]').tooltip({ 
	track: true, 
	delay: 2, 
	showURL: false, 
	showBody: true, 
	fade: 300 
});

//altura = screen.height;
//largura = screen.width;
//self.resizeTo(760,altura-120);
//self.moveTo(largura/2-380,60);
self.focus();
</script>

<head>

</head>
<body onLoad="self.focus()">




<fieldset class="fieldset_entrega">
<a href="?data_inicial=<?= $data_inicial ?>" title="Filtro original, mais novos primeiro.">
<legend>Entregas para <?= $data_inicial ?></legend>
</a>

<?
#fazer a consulta
$SQL = "SELECT cliente.nome_fantasia, cliente.cidade, cliente.estado, cliente.telefone, cliente.contato, producao.codigo, producao.titulo_servico, producao.quantidade, producao.data_prevista, producao.data_entrega, producao.entrega_obs, producao.transporte_obs, producao.cod_transporte, producao.historico FROM
 cliente,producao 
WHERE 
cliente.codigo=producao.agencia 
AND TO_DAYS(NOW()) - TO_DAYS(data_prevista) <= 15
AND 
(
(producao.data_entrega='".mydate($data_inicial)."' OR producao.data_prevista='".mydate($data_inicial)."') 
OR 
(producao.data_prevista<'".mydate($data_inicial)."' AND (producao.status='5' OR producao.status='11' OR producao.status='2' OR producao.status='7') AND producao.status<>'12' AND producao.transporte_obs='')
)
GROUP BY producao.codigo";

if($order){
$SQL .= " ORDER BY $order ASC";
}else{
$SQL .= " ORDER BY producao.data_prevista desc, cliente.nome_fantasia ASC, producao.agencia asc,producao.titulo_servico ASC";
}
#echo $SQL;
$QR = mysql_query($SQL);
?>

<table>
	<tr>
		<th><a href="?data_inicial=<?= $data_inicial ?>&order=codigo">OS</a></th>
		<th style="width:150px"><a href="?data_inicial=<?= $data_inicial ?>&order=titulo_servico"><strong>Título</strong></a>|<a href="?data_inicial=<?= $data_inicial ?>&order=data_prevista"><strong>Data</strong></a></th>
		<th><a href="?data_inicial=<?= $data_inicial ?>&order=agencia">Agência</a></th>
		<th><a href="?data_inicial=<?= $data_inicial ?>&order=quantidade">Qt</a></th>
		<th><a href="?data_inicial=<?= $data_inicial ?>&order=cod_transporte">Transporte</a></th>
		<th>HistÃ³rico</th>
		<th style="width:150px">Enviado/Retirado por:</th>
	</tr>
	
	<? while($rs = mysql_fetch_array($QR)){ ?>
		<? if($udata!=mydate($rs[data_prevista])) { ?>
		<? if($rs[data_prevista]!=mydate($data_inicial)){ ?>
		<tr><td colspan="7" style="background:#000; color:#FFF">
			<?
			$udata = mydate($rs[data_prevista]);
			if($rs[data_entrega]){
			echo "(".mydate($rs[data_entrega]).")";
			}else{
			echo "(".mydate($rs[data_prevista]).")";
			} ?>
		</td></tr>
		<? } ?>
		<? } ?>
	<tr>
		<td><?= $rs[codigo] ?></td>
		<td>
		<strong><?= strtoupper($rs[titulo_servico]) ?></strong><br />
		</td>
		<td><?= strtoupper($rs[nome_fantasia]) ?><br /><small><?= $rs[cidade] ?> <?= $rs[estado] ?></small></td>
		<?
		$SQLq = mysql_query("SELECT SUM(quantidade) as quantidade FROM producao_itens WHERE codigo_producao='$rs[codigo]'");
		$rsq = mysql_fetch_array($SQLq);
		?>
		<td><?= $rsq[quantidade] ?></td>
		<td><? if($rs[cod_transporte]){
			$SQLt = mysql_query("SELECT nome_fantasia FROM cliente WHERE codigo='$rs[cod_transporte]'");
			$rst = mysql_fetch_array($SQLt); ?>
			<strong><?= str_replace("TRANSP. ","",strtoupper($rst[nome_fantasia])) ?></strong><br />
			<? } ?>
		<?= nl2br($rs[entrega_obs]) ?></td>
		<td><?= nl2br($rs[historico]) ?></td>
		<td><?= nl2br($rs[transporte_obs]) ?></td>
	</tr>
	<? } ?>
</table>
</fieldset>

</body>
</html>
