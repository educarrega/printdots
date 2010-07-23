<? 
session_start();
#require_once "../../conn.php";

$table = "equipamento";
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
	mph = '$mph',
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
$equipamento_acao = "Incluindo";
$alvo = "#add_equipamento";
if($codigo){
$gravar = "editar";
$equipamento_acao = "Editando";
$alvo = "#equipamento_$codigo";
}

?>

<script type="text/javascript">
busca_status('Editando o cadastro de Equipamentos');

$('#bt_voltar_<?= $codigo ?>').click(function(){
	<? if($codigo){ ?>
	$('<?= $alvo ?>').load('utf2iso.php?file=plugins/equipamento/equipamento_lista.php',{codigo:'<?= $codigo ?>'});
	$('<?= $alvo ?>').css({'overflow':'hidden', 'height': '65px'});
	$('#equipamento_status_<?= $codigo ?>').show();
	<? }else{ ?>
	$('<?= $alvo ?>').slideUp('slow', function(){
		$('<?= $alvo ?>').html('');
		$('#bt_add_equipamento').fadeIn();
	});
	<? } ?>
});

//titulos
$('[title]').tooltip({ 
	track: true, 
	delay: 2, 
	showURL: false, 
	showBody: true, 
	fade: 300 
});

$('#form_equipamento_<?= $codigo ?>').bind('submit',function(){
	var formContent = $('#form_equipamento_<?= $codigo ?>').serialize();
	$('<?= $alvo ?>').load('utf2iso.php?file=plugins/equipamento/equipamento_editar.php',formContent);
	return false;
});
</script>

<fieldset class="fieldset_infos">
<legend>
<img id="bt_voltar_<?= $codigo ?>" class="bt_voltar" src="images/bt_voltar.png" align="absmiddle" title="Cancela o processo de edição" /> 
<? if($codigo){ ?>
<img src="images/bt_editar.png" align="absmiddle" /> 
<? }else{ ?>
<img src="images/bt_mais.png" align="absmiddle" /> 
<? } ?>
<?= $equipamento_acao ?> equipamento </legend>

<?
if($codigo){
	$wr = "WHERE codigo=$codigo";
	$qr = mysql_query("SELECT * FROM equipamento $wr");
	$rs = mysql_fetch_array($qr);
}
?>
<form name="form_equipamento_<?= $codigo ?>" id="form_equipamento_<?= $codigo ?>" action="">
<input type="hidden" name="gravar" value="<?= $gravar ?>" />
<input type="hidden" name="codigo" value="<?= $rs[codigo] ?>" />

<div>
<label for="titulo">Título</label>
<input type="text" name="titulo" class="titulo_servico" value="<?= $rs[titulo] ?>" />
</div>

<?
if($rs){
for($t=1;$t<mysql_num_fields($qr);$t++){ ?>
	<? if(mysql_field_name($qr,$t)!="titulo"){ ?>
	<div class="colunas_2">
	<label for="<?= mysql_field_name($qr,$t) ?>"><?= comment($dbase,'equipamento',mysql_field_name($qr,$t)) ?></label>
	
	<? if(strstr("descricao",mysql_field_name($qr,$t))){ ?>
	<textarea rows="3" name="<?= mysql_field_name($qr,$t) ?>"><?= $rs[mysql_field_name($qr,$t)] ?></textarea>
	
	<? }elseif(strstr("data",mysql_field_name($qr,$t))){ ?>
		<input type="text" class="text" name="<?= mysql_field_name($qr,$t) ?>" id="<?= mysql_field_name($qr,$t) ?>" value="<? if($rs[mysql_field_name($qr,$t)]) echo mydate($rs[mysql_field_name($qr,$t)]) ?>" />
		<script language="javascript">
		$('#<?= mysql_field_name($qr,$t) ?>').focus(function(){
		$(this).calendario({target:'#<?= mysql_field_name($qr,$t) ?>'});
		});
		</script>
		
	<? }elseif(mysql_field_len($qr,$t)=='1'){ ?>
		<input type="hidden" id="<?= mysql_field_name($qr,$t) ?>_<?= $rs[codigo] ?>" name="<?= mysql_field_name($qr,$t) ?>" value="<?= $rs[mysql_field_name($qr,$t)] ?>" />
		<? $check = $rs[mysql_field_name($qr,$t)]; ?>
		<div id="check_<?= mysql_field_name($qr,$t) ?>_<?= $rs[codigo]?>" class="checkbox" title="Ativar/Desativar"></div>
		<? $check = $check ? 0 : 1; ?>
		<script language="javascript">check(<?= $check ?>,<?= $rs[codigo]?>,'<?= mysql_field_name($qr,$t) ?>',1)</script>
			
	<? }elseif(mysql_field_name($qr,$t)!="titulo"){ ?>
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


