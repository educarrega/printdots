<? 
@session_start();
#require_once "../../conn.php";

$table = "material";
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
if($gravar=="excluir"){	$condition = "WHERE codigo=$codigo";
	del($table,$condition);
	$codigo = "";
	die();
}
if($gravar=="editar"){
	//$campos = 'codigo,titulo,descricao,base_calculo,peso,largura,altura,profundidade,litro,hora,valor,codigo_fornecedor,ativo';
	$condition = " WHERE codigo=$codigo";
	$cols = "
	titulo = '$titulo',
	descricao = '$descricao',
	base_calculo = '$base_calculo',
	peso = '$peso', 
	largura = '$largura',
	altura = '$altura',
	profundidade = '$profundidade',  
	litro = '$litro',
	hora = '$hora',
	valor = '$valor',
	codigo_fornecedor = '$codigo_fornecedor',
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
$acao = "Incluindo";
$alvo = "#add_material";
if($codigo){
$gravar = "editar";
$acao = "Editando";
$alvo = "#material_$codigo";
}
?>

<script type="text/javascript">
busca_status('Editando o cadastro de Materiais e Serviços');

$('#bt_voltar_<?= $codigo ?>').click(function(){
	<? if($codigo){ ?>
	$('<?= $alvo ?>').load('utf2iso.php?file=plugins/material/material_lista.php',{codigo:'<?= $codigo ?>'});
	$('<?= $alvo ?>').css({'overflow':'hidden', 'height': '65px'});
	$('#material_status_<?= $codigo ?>').show();
	<? }else{ ?>
	$('<?= $alvo ?>').slideUp('slow', function(){
		$('<?= $alvo ?>').html('');
		$('#bt_add_material').fadeIn();
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

$('#form_material_<?= $codigo ?>').bind('submit',function(){
	var formContent = $('#form_material_<?= $codigo ?>').serialize();
	$('<?= $alvo ?>').load('utf2iso.php?file=plugins/material/material_editar.php',formContent);
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
<?= $acao ?></legend>

<?
if($codigo){
	$wr = "WHERE codigo=$codigo";
	$qr = mysql_query("SELECT * FROM material $wr");
	$rs = mysql_fetch_array($qr);
}
?>
<form name="form_material_<?= $codigo ?>" id="form_material_<?= $codigo ?>" action="">
<input type="hidden" name="gravar" value="<?= $gravar ?>" />
<input type="hidden" name="codigo" value="<?= $rs[codigo] ?>" />

<div>
<label for="servico">Título</label>
<input type="text" name="titulo" class="titulo_servico" value="<?= $rs[titulo] ?>" />
</div>

<?
if($rs){
for($t=1;$t<mysql_num_fields($qr);$t++){ ?>
	<? if(mysql_field_name($qr,$t)!="titulo"){ ?>	<div class="colunas_2">	<label for="<?= mysql_field_name($qr,$t) ?>"><?= comment($dbase,'material',mysql_field_name($qr,$t)) ?></label>
	
	<? if(strstr("descricao",mysql_field_name($qr,$t)) || strstr("endereco",mysql_field_name($qr,$t))){ ?>	<textarea rows="3" name="<?= mysql_field_name($qr,$t) ?>"><?= $rs[mysql_field_name($qr,$t)] ?></textarea>
	<? }elseif(strstr("data",mysql_field_name($qr,$t))){ ?>		<input type="text" class="text" name="<?= mysql_field_name($qr,$t) ?>" id="<?= mysql_field_name($qr,$t) ?>" value="<? if($rs[mysql_field_name($qr,$t)]) echo mydate($rs[mysql_field_name($qr,$t)]) ?>" />		<script language="javascript">
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
			<? }elseif(strstr("base_calculo",mysql_field_name($qr,$t))){ ?>
		<select id="<?= mysql_field_name($qr,$t) ?>_<?= $rs[codigo] ?>" name="<?= mysql_field_name($qr,$t) ?>" class="text">
		<?
		$qrb = mysql_query("SELECT valor FROM controle WHERE titulo='".mysql_field_name($qr,$t)."' LIMIT 1");
		$rsb = mysql_fetch_array($qrb);
		$base = explode(',',$rsb[valor]);
		foreach($base as $k=>$v){ ?>
			<option value="<?= $v ?>"<? if($v == $rs[mysql_field_name($qr,$t)]) echo selected ?>><?= $v ?></option>";
		<? } ?>
		</select>
		
	<? }elseif(mysql_field_name($qr,$t)!="titulo"){ ?>	<input type="text" name="<?= mysql_field_name($qr,$t) ?>" value="<?= $rs[mysql_field_name($qr,$t)] ?>" />	<? } ?>
	</div>
	<? } ?>
<? }} ?>

<div class="clear_left"></div>
<div class="imageright"><hr /><input type="image" src="images/button_ok.png" name="submit" class="image" title="Enviar os Dados" /></div>
<br /><br />
</form>
</fieldset>


