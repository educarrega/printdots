<? @session_start();
#require_once "../../conn.php";

$SQLp = "SELECT cliente_codigo FROM pedido WHERE codigo='$codigo_pedido'";
$QRp = mysql_query($SQLp);
if(mysql_num_rows($QRp)>0){
	$rp = mysql_fetch_array($QRp);
	$cliente_codigo = $rp[cliente_codigo];
}

if($cliente_codigo){
	$SQLc = "SELECT codigo_material FROM cliente WHERE codigo = '$cliente_codigo'";
	$QRc = mysql_query($SQLc);
	$rc = mysql_fetch_array($QRc);
	$codigo_material = $rc[codigo_material];
}

if($codigo_material!='undefined' && $codigo_material!=''){
	$materiais = explode(',',$codigo_material);
	$wr_material .= " (";
	foreach($materiais as $k=>$v){
		$wr_material .= " codigo='$v' OR ";
	} $wr_material = substr($wr_material,0,strlen($wr_material)-3);
	$wr_material .= ") ";
}else{
	$wr_material = "ativo = '1'";
}

if($filtro){
	$filtro."<hr />";
	$campos = 'titulo,descricao,base_calculo,peso,largura,altura,profundidade,litro,hora,valor,codigo_fornecedor';
	$campos = explode(',',$campos);
	if($codigo_material) $wr_material .= " AND (";
	foreach($campos as $k=>$v){
		$wr_material .= "$v LIKE '%$filtro%' OR ";
	} $wr_material = substr($wr_material,0,strlen($wr_material)-3);
	if($codigo_material) $wr_material .= ")";
}

$SQL_material = "SELECT * FROM material WHERE $wr_material ORDER BY ativo ASC, titulo ASC";
#echo $SQL_material;
$QR_material = mysql_query($SQL_material);
while($rm = mysql_fetch_array($QR_material)){ ?>
	<label>
	<li id="material_<?= $rm[codigo] ?>">
	<?
	$checked = "";
	$type = "radio";
	if(count($materiais)>0){
		if(($rm[codigo]==$selected)) $checked = "checked";
		#$type = "checkbox";
		$onclick = "set_valores_material_$codigo_pedido('". $rm[codigo] ."','". $rm[valor] ."','". $rm[peso] ."');";
	}else{
		$onclick = "monta_cliente_$codigo_pedido('". $rm[codigo] ."'); set_valores_material_$codigo_pedido('". $rm[codigo] ."','". $rm[valor] ."','". $rm[peso] ."');";
	}
	#echo $onclick; ?>
	<input type="<?= $type ?>" name="codigo_material" value="<?= $rm[codigo] ?>" <?= $checked ?> onclick="<?= $onclick ?>"> 
	<b><?= $rm[titulo] ?></b><br />
	<?= $rm[descricao] ?>
	</li>
	</label>
<? } ?>
