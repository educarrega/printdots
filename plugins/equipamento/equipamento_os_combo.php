<? @session_start();
#require_once "../../conn.php";

#obtendo os equipamentos do produto
$SQLpdt = mysql_query("SELECT descricao,equipamento FROM produto WHERE codigo='$produto'");
$pdt = mysql_fetch_array($SQLpdt);
$equipamentos = explode(',',$pdt[equipamento]);
$descricao_produto = $pdt[descricao];

#monta o where para selecionar apenas os equipamentos listados
$wreq = "";
foreach($equipamentos as $keq => $eq){
	$wreq .= "codigo='$eq' OR ";
} $wreq = substr($wreq,0,strlen($wreq)-4);


?>

<script>
$('#codigo_equipamento_<?= $producao ?>').bind('change',function(){
	var produto = $('#codigo_produto_<?= $producao ?>').val();
	var equipamento_selecionado = $('#codigo_equipamento_<?= $producao ?>').val();
	//alert(equipamento_selecionado);
	$('#equipamento_<?= $producao ?>').load("utf2iso.php?file=plugins/equipamento/equipamento_os_combo.php",{produto: produto, producao:'<?= $producao ?>', equipamento_selecionado: equipamento_selecionado});
});
</script>

<select multiple size="10" name="codigo_equipamento" id="codigo_equipamento_<?= $producao ?>" class="historico"><?
#monta os options
$SQLe = mysql_query("SELECT * FROM equipamento WHERE $wreq ORDER BY titulo");
while($eq = mysql_fetch_array($SQLe)){
	?><option value="<?= $eq[codigo] ?>" <? 
	if($eq[codigo]==$equipamento_selecionado){
		$descricao_equipamento = $eq[descricao];
		echo selected;
	}
	?>><?= ($eq[titulo]) ?></option><?
	
} 

# escreve nos campos as descricões de cada equipamento apontado no onchange
?>
</select>
<script>
$('#descricao_produto_<?= $producao ?>').html('<?= $descricao_produto ?>');
$('#descricao_equipamento_<?= $producao ?>').html('<?= $descricao_equipamento ?>');
</script>
