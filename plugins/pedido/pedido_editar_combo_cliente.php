<? @session_start();
#require_once "../../conn.php";

$SQLp = "SELECT cliente_codigo FROM pedido WHERE codigo='$codigo_pedido'";
$QRp = mysql_query($SQLp);
if(mysql_num_rows($QRp)>0){
	$rp = mysql_fetch_array($QRp);
	$cliente_codigo = $rp[cliente_codigo];
}
			
#obtendo os CLIENTES
if($cliente_codigo){
	$wr_cliente = "WHERE codigo='$cliente_codigo'";
}elseif($codigo_material){
	$wr_cliente = "WHERE codigo_material LIKE '%$codigo_material%'";
}else{
	$wr_cliente = "WHERE tipo_fornecedor = '1'";
}

$SQLc = "SELECT * FROM cliente $wr_cliente";
$QRc = mysql_query($SQLc);
#echo $SQLc;
while($rc = mysql_fetch_array($QRc)){ 
	$materiais = explode(',',$rc[codigo_material]);
	if(in_array($codigo_material,$materiais) || !$codigo_material){ ?>
	<label>
	<li id="cliente_<?= $rc[codigo] ?>">
	<?
	$checked = "";
	if($rc[codigo]==$cliente_codigo) $checked = "checked";
	?>
	<input type="radio" name="cliente_codigo" value="<?= $rc[codigo] ?>" <?= $checked ?> onclick="monta_material_<?= $codigo_pedido ?>('<?= $rc[codigo_material] ?>','<?= $codigo_material ?>');"> 
	<b><?= $rc[nome_fantasia] ?></b><br />
	<small><?= $rc[razao_social] ?> | CNPJ: <?= $rc[cnpj] ?></small><br />
	<?= $rc[cidade] ?> <?= $rc[estado] ?>, <?= $rc[contato] ?> <?= $rc[telefone] ?>
	</li>
	</label>
	<? }
} ?>
