<? 
session_start();
#require_once "../../conn.php";

$table = "banco";
if($gravar=="incluir"){
	$cols = "banco";
	$values = "'$banco'";
	ins($table,$cols,$values);
	
	#obter o codigo da inclusao
	$SQLm = "SELECT MAX(codigo) as codigo FROM $table";
	$QRm = mysql_query($SQLm);
	$rsm = mysql_fetch_array($QRm);
	$codigo = $rsm[codigo];
	?>
	<script language="javascript">
	var filter = '<?= substr($banco,0,1) ?>';
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
	banco = '$banco',
	banco_numero = '$banco_numero',
	agencia_numero = '$agencia_numero', 
	agencia_digito = '$agencia_digito',
	conta_numero = '$conta_numero',
	conta_digito = '$conta_digito',  
	conta_corrente = '$conta_corrente',
	conta_poupanca = '$conta_poupanca',
	codigo_carteira = '".implode(",",$carteiras)."',
	aceita_saida = '$aceita_saida',
	aceita_entrada = '$aceita_entrada',
	endereco = '$endereco',
	telefone = '$telefone',
	contato = '$contato',
	site = '$site', 
	email = '$email',
	descricao = '$descricao', 
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
$banco_acao = "Incluindo";
$alvo = "#add_banco";
if($codigo){
$gravar = "editar";
$banco_acao = "Editando";
$alvo = "#banco_$codigo";
}

?>

<script type="text/javascript">
busca_status('Editando o cadastro de Bancos e Contas  ');

$('#bt_voltar_<?= $codigo ?>').click(function(){
	<? if($codigo){ ?>
	$('<?= $alvo ?>').load('utf2iso.php?file=plugins/banco/banco_lista.php',{codigo:'<?= $codigo ?>'});
	$('<?= $alvo ?>').css({'overflow':'hidden', 'height': '65px'});
	$('#banco_status_<?= $codigo ?>').show();
	<? }else{ ?>
	$('<?= $alvo ?>').slideUp('slow', function(){
		$('<?= $alvo ?>').html('');
		$('#bt_add_banco').fadeIn();
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

$('#form_banco_<?= $codigo ?>').bind('submit',function(){
	var formContent = $('#form_banco_<?= $codigo ?>').serialize();
	$('<?= $alvo ?>').load('utf2iso.php?file=plugins/banco/banco_editar.php',formContent);
	return false;
});

function ampliar_carteira(codigo){
	$("#full_frame").load('utf2iso.php?file=plugins/carteira/carteira_os.php',{codigo: codigo});
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
<?= $banco_acao ?> Bancos e Contas   </legend>

<?
if($codigo){
	$wr = "WHERE codigo=$codigo";
	$qr = mysql_query("SELECT * FROM banco $wr");
	$rs = mysql_fetch_array($qr);
}
?>
<form name="form_banco_<?= $codigo ?>" id="form_banco_<?= $codigo ?>" action="">
<input type="hidden" name="gravar" value="<?= $gravar ?>" />
<input type="hidden" name="codigo" value="<?= $rs[codigo] ?>" />

<div>
<label for="servico">Título</label>
<input type="text" name="banco" class="titulo_servico" value="<?= $rs[banco] ?>" />
</div>

<?
if($rs){
for($t=1;$t<mysql_num_fields($qr);$t++){ ?>
	
	<? if(mysql_field_name($qr,$t)!="banco"){ ?>
	<div class="colunas_2">
	<label for="<?= mysql_field_name($qr,$t) ?>"><?= comment($dbase,'banco',mysql_field_name($qr,$t)) ?></label>
	
	<? if(strstr("descricao",mysql_field_name($qr,$t)) || strstr("endereco",mysql_field_name($qr,$t))){ ?>
	<textarea rows="3" name="<?= mysql_field_name($qr,$t) ?>"><?= $rs[mysql_field_name($qr,$t)] ?></textarea>
	
	<? }elseif(strstr("data",mysql_field_name($qr,$t))){ ?>
		<input type="text" class="text" name="<?= mysql_field_name($qr,$t) ?>" id="<?= mysql_field_name($qr,$t) ?>" value="<? if($rs[mysql_field_name($qr,$t)]) echo mydate($rs[mysql_field_name($qr,$t)]) ?>" />
		<script language="javascript">
		$('#<?= mysql_field_name($qr,$t) ?>').focus(function(){
		$(this).calendario({target:'#<?= mysql_field_name($qr,$t) ?>'});
		});
		</script>
	
	<? }elseif(mysql_field_name($qr,$t)=="codigo_carteira"){ ?>
	Escolha as carteiras suportadas por este banco.<br /><br />
	<div class="div_textarea_scroll" style="height: 300px;">
	<?
	#selecionados
	$carteiras = explode(',',$rs[codigo_carteira]);
	$SQLe = "SELECT * FROM carteira ORDER BY ativo, titulo ASC";
	$QRe = mysql_query($SQLe);
	while($eq = mysql_fetch_array($QRe)){ 
		if(in_array("$eq[codigo]",$carteiras)){ ?>
	
		<span class="carteira_status">
		<img src="images/bt_ampliar.png" onclick="ampliar_carteira('<?= $eq[codigo] ?>')" alt="ampliar" title="Mais detalhes desta Carteira" /> 
		<? if(!$eq[ativo]){ ?><img src="images/bt_atencao.png" alt="i" title="Inativo" /><? } ?>
		</span>
		
		<input type="checkbox" class="checkbox" name="carteiras[]" value="<?= $eq[codigo] ?>" checked ><b><?= $eq[codigo] ?> - <?= $eq[titulo] ?></b><br />
		<?= $eq[descricao] ?>
		<hr />
		<? }
	}
	
	#nao selecionados
	$QRe2 = mysql_query($SQLe);
	while($eq2 = mysql_fetch_array($QRe2)){ 
		if(in_array("$eq2[codigo]",$carteiras)){}else{ ?>
		<span class="carteira_status">
		<img src="images/bt_ampliar.png" onclick="ampliar_carteira('<?= $eq2[codigo] ?>')" alt="ampliar" title="Mais detalhes desta Carteira" /> 
		<? if(!$eq2[ativo]){ ?><img src="images/bt_atencao.png" alt="i" title="Inativo" /><? } ?>
		</span>
		
		<input type="checkbox" class="checkbox" name="carteiras[]" value="<?= $eq2[codigo] ?>"><b><?= $eq2[codigo] ?> - <?= $eq2[titulo] ?></b><br />
		<?= $eq2[descricao] ?>
		<hr />
		<? }
	} ?>
	</div>
	
	<? }elseif(mysql_field_len($qr,$t)=='1'){ ?>
		<input type="hidden" id="<?= mysql_field_name($qr,$t) ?>_<?= $rs[codigo] ?>" name="<?= mysql_field_name($qr,$t) ?>" value="<?= $rs[mysql_field_name($qr,$t)] ?>" />
		<? $check = $rs[mysql_field_name($qr,$t)]; ?>
		<div id="check_<?= mysql_field_name($qr,$t) ?>_<?= $rs[codigo]?>" class="checkbox" title="Ativar/Desativar"></div>
		<? $check = $check ? 0 : 1; ?>
		<script language="javascript">check(<?= $check ?>,<?= $rs[codigo]?>,'<?= mysql_field_name($qr,$t) ?>',1)</script>
		
	<? }elseif(mysql_field_name($qr,$t)!="banco"){ ?>
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


