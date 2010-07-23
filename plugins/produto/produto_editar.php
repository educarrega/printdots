<? 
session_start();
#require_once "../../conn.php";

$table = "produto";
if($gravar=="incluir"){
	$cols = "titulo";
	$values = "'$titulo'";
	ins($table,$cols,$values);
	
	#obter o codigo da inclusao
	$SQLm = "SELECT MAX(codigo) as codigo FROM $table";
	$QRm = mysql_query($SQLm);
	$rsm = mysql_fetch_array($QRm);
	$codigo = $rsm[codigo];
	?>
	<script language="javascript">
	var filter = '<?= substr($titulo,0,1) ?>';
	$('#<?= $table ?>_todas').load('utf2iso.php?file=plugins/<?= $table ?>/<?= $table ?>_lista.php',{filter: filter, editar:'<?= $codigo ?>'});
	</script>
	<?
}
if($gravar=="excluir"){
	$condition = "WHERE codigo=$codigo";
	del($table,$condition);
	$codigo = "";
	die();
}
if($gravar=="editar"){
	$condition = " WHERE codigo=$codigo";
	$cols = "
	titulo = '$titulo',
	descricao = '$descricao', 
	equipamento = '".implode(",",$equipamento)."',
	ppm = '$ppm',
	ativo = '$ativo'
	";
	upd($table,$cols,$condition);
}
if($gravar=="status"){
	$condition = " WHERE codigo=$codigo";
	$cols = "
	ativo = '$status'
	";
	upd($table,$cols,$condition);
	die;
}
$gravar = "incluir";
$produto_acao = "Incluindo";
$alvo = "#add_produto";
if($codigo){
$gravar = "editar";
$produto_acao = "Editando";
$alvo = "#produto_$codigo";
}

?>

<script type="text/javascript">
busca_status('Editando o cadastro de Produtos');

$('#bt_voltar_<?= $codigo ?>').click(function(){
	<? if($codigo){ ?>
	$('<?= $alvo ?>').load('utf2iso.php?file=plugins/produto/produto_lista.php',{codigo:'<?= $codigo ?>'});
	$('<?= $alvo ?>').css({'overflow':'hidden', 'height': '65px'});
	$('#produto_status_<?= $codigo ?>').show();
	<? }else{ ?>
	$('<?= $alvo ?>').slideUp('slow', function(){
		$('<?= $alvo ?>').html('');
		$('#bt_add_produto').fadeIn();
	});
	<? } ?>
});

//titulos
$('[title]').tooltip({ 
	track: false, 
	delay: 0, 
	showURL: false, 
	showBody: true, 
	fade: 30 
});

$('#form_produto_<?= $codigo ?>').bind('submit',function(){
	var formContent = $('#form_produto_<?= $codigo ?>').serialize();
	$('<?= $alvo ?>').load('utf2iso.php?file=plugins/produto/produto_editar.php',formContent);
	return false;
});

function ampliar_equipamento(codigo){
	$("#full_frame").load('utf2iso.php?file=plugins/equipamento/equipamento_os.php',{equipamento: codigo});
	$("#full_frame").slideDown(function(){
		$("#full_background").fadeIn(500);
	});
}
</script>

<fieldset class="fieldset_infos">
<legend>
<img id="bt_voltar_<?= $codigo ?>" class="bt_voltar" src="images/bt_voltar.png" align="absmiddle" title="Cancela o processo de edição" /> 
<? if($codigo){ ?>
<img src="images/bt_editar.png" align="absmiddle" /> 
<? }else{ ?>
<img src="images/bt_mais.png" align="absmiddle" /> 
<? } ?>
<?= $produto_acao ?> produto </legend>

<?
if($codigo){
	$wr = "WHERE codigo=$codigo";
	$qr = mysql_query("SELECT * FROM produto $wr");
	$rs = mysql_fetch_array($qr);
}
?>
<form name="form_produto_<?= $codigo ?>" id="form_produto_<?= $codigo ?>" action="">
<input type="hidden" name="gravar" value="<?= $gravar ?>" />
<input type="hidden" name="codigo" value="<?= $rs[codigo] ?>" />

<div>
<label for="servico">Título</label>
<input type="text" name="titulo" class="titulo_servico" value="<?= $rs[titulo] ?>" />
</div>

<?
if($rs){
for($t=1;$t<mysql_num_fields($qr);$t++){ ?>
	<? if(mysql_field_name($qr,$t)!="titulo"){ ?>
	<div class="colunas_2">
	<label for="<?= mysql_field_name($qr,$t) ?>"><?= comment($dbase,'produto',mysql_field_name($qr,$t)) ?></label>
	<? if(strstr("descricao",mysql_field_name($qr,$t)) || strstr("endereco",mysql_field_name($qr,$t)) || strstr("site",mysql_field_name($qr,$t)) || strstr("prazo_entrega",mysql_field_name($qr,$t))){ ?>
	<textarea rows="3" name="<?= mysql_field_name($qr,$t) ?>"><?= $rs[mysql_field_name($qr,$t)] ?></textarea>
	<? }elseif(strstr("data",mysql_field_name($qr,$t))){ ?>
		<input type="text" class="text" name="<?= mysql_field_name($qr,$t) ?>" id="<?= mysql_field_name($qr,$t) ?>" value="<? if($rs[mysql_field_name($qr,$t)]) echo mydate($rs[mysql_field_name($qr,$t)]) ?>" />
		<script language="javascript">
		$('#<?= mysql_field_name($qr,$t) ?>').focus(function(){
		$(this).calendario({target:'#<?= mysql_field_name($qr,$t) ?>'});
		});
		</script>
		
	<? }elseif(strstr("ativo",mysql_field_name($qr,$t))){ ?>
		<input type="hidden" id="<?= mysql_field_name($qr,$t) ?>_<?= $rs[codigo] ?>" name="<?= mysql_field_name($qr,$t) ?>" value="<?= $rs[mysql_field_name($qr,$t)] ?>" />
		<? $check = $rs[mysql_field_name($qr,$t)]; ?>
		<div id="check_<?= mysql_field_name($qr,$t) ?>_<?= $rs[codigo]?>" class="checkbox" title="Ativar/Desativar"></div>
		<? $check = $check ? 0 : 1; ?>
		<script language="javascript">check(<?= $check ?>,<?= $rs[codigo]?>,'<?= mysql_field_name($qr,$t) ?>',1)</script>
		
	<? }elseif(mysql_field_name($qr,$t)=="equipamento"){ ?>
	Escolha os equipamentos que podem produzir este produto.<br /><br />
	<div class="div_textarea_scroll" style="height: 300px;">
	<?
	$equipamentos = explode(',',$rs[equipamento]);
	$SQLe = "SELECT * FROM equipamento ORDER BY ativo DESC, titulo";
	$QRe = mysql_query($SQLe);
	while($eq = mysql_fetch_array($QRe)){ 
		if(in_array("$eq[codigo]",$equipamentos)){ ?>
	
		<span class="equipamento_status">
		<img src="images/bt_ampliar.png" onclick="ampliar_equipamento('<?= $eq[codigo] ?>')" alt="ampliar" title="Mais detalhes deste Equipamento" /> 
		<? if(!$eq[ativo]){ ?><img src="images/bt_atencao.png" alt="i" title="Inativo" /><? } ?>
		</span>
		
		<input type="checkbox" class="checkbox" name="equipamento[]" value="<?= $eq[codigo] ?>" checked ><b><?= $eq[codigo] ?> - <?= $eq[titulo] ?></b><br />
		<?= $eq[descricao] ?>
		<hr />
		<? }
	}
	$QRe2 = mysql_query($SQLe);
	while($eq2 = mysql_fetch_array($QRe2)){ 
		if(in_array("$eq2[codigo]",$equipamentos)){}else{ ?>
		<span class="equipamento_status">
		<img src="images/bt_ampliar.png" onclick="ampliar_equipamento('<?= $eq2[codigo] ?>')" alt="ampliar" title="Mais detalhes deste Equipamento" /> 
		<? if(!$eq2[ativo]){ ?><img src="images/bt_atencao.png" alt="i" title="Inativo" /><? } ?>
		</span>
		
		<input type="checkbox" class="checkbox" name="equipamento[]" value="<?= $eq2[codigo] ?>"><b><?= $eq2[codigo] ?> - <?= $eq2[titulo] ?></b><br />
		<?= $eq2[descricao] ?>
		<hr />
		<? }
	} ?>
	</div>
	<? }elseif(mysql_field_name($qr,$t)!="servico"){ ?>
	<input type="text" name="<?= mysql_field_name($qr,$t) ?>" value="<?= $rs[mysql_field_name($qr,$t)] ?>" />
	<? } ?>
	</div>
	<? } ?>
<? }} ?>

<div class="clear_left"></div>
<div class="imageright"><hr /><input type="image" src="images/button_ok.png" name="submit" class="image" title="Enviar os Dados" /></div>
<br /><br />
</form>
</fieldset>


