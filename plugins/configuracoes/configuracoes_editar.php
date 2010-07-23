<? 
session_start();
#require_once "../../conn.php";
login();

$table = "controle";
if($gravar=="duplicar"){
	//selecao da origem
	$SQL = "SELECT rel,titulo,valor,descricao,usuario,ordem,ativo FROM $table WHERE codigo=$codigo";
	$QR = mysql_query($SQL);
	$rs = mysql_fetch_array($QR);
	if(mysql_num_rows($QR)>0){
		for($r=0;$r<mysql_num_fields($QR);$r++){
		$campo = mysql_field_name($QR,$r);
		$$campo = $rs[$campo];
		}
	$gravar = "incluir";
	$codigo = $rs[rel];
	}
}
if($gravar=="incluir"){
	if(!$titulo) $titulo = "Título";
	if(!$descricao) $descricao = "Descrição";
	$cols = "rel,titulo,valor,descricao,usuario,ordem,ativo";
	$values = "'$codigo','$titulo','$valor','$descricao','$usuario','$ordem','$ativo'";
	//echo $values;
	//die;
	ins($table,$cols,$values);
	
	#obter o codigo da inclusao
	$SQLm = "SELECT MAX(codigo) as codigo FROM $table";
	$QRm = mysql_query($SQLm);
	$rsm = mysql_fetch_array($QRm);
	
	/*
	if($duplicar){
		$duplicar = 0;
		$gravar = "duplicar";
		$selected = $rsm[codigo];
	}else{ */
		$codigo = $rsm[codigo];
		echo $codigo;
		die();
	#}
}
if($gravar=="excluir"){
	$condition = "WHERE codigo=$codigo";
	del($table,$condition);
	$codigo = "";
	die();
}
if($gravar=="editar"){
	if(is_array($usuario)) $usuario = implode(',',$usuario);
	$condition = " WHERE codigo=$codigo";
	$cols = "
	titulo = '$titulo',
	descricao = '$descricao',
	rel = '$rel',
	valor = '$valor',
	usuario = '$usuario',
	ordem = '$ordem', 
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

if($gravar=="duplicar"){
	$configuracoes_acao = "Duplicando";
	if(!$selected) $selected = $codigo;
}elseif($codigo){
	$gravar = "editar";
	$configuracoes_acao = "Editando";
}

$_SESSION[volta] = 0;
function configuracoes($rel,$selected){
	$filter = "WHERE rel='$rel'";
	if($_SESSION[volta]=="0"){
		$filter = "WHERE codigo='$rel'";
		$_SESSION[volta]='1';
		$rel = NULL;
	}$wr = "$filter  ORDER BY titulo ASC, ativo DESC";
	$SQL = "SELECT * FROM controle $wr";
	#echo $SQL;
	$QR = mysql_query($SQL);
	if(mysql_num_rows($QR)>0){
		$_SESSION[endent]++;
		while($rs = mysql_fetch_array($QR)){ 
			if(strstr($rs[titulo],'user')){?>
				<optgroup label="<?= $rs[descricao] ?>">
			<? }elseif(strstr($rs[titulo],'menu')){?>
				<optgroup label="<?= $rs[descricao] ?>">
			<? }elseif(strstr($rs[titulo],'plugin_inicial')){ ?>
				<optgroup label="<?= $rs[descricao] ?>">
			<? } ?>
				<option value="<?= $rs[codigo] ?>" <? if($selected==$rs[codigo]) echo selected ?>><?= str_pad('',$_SESSION[endent],'_') ?><?= $rs[descricao] ?></option>
			<? #} <?= $rs[codigo] 
			configuracoes($rs[codigo],$selected);		
		}
		$_SESSION[endent]--;
		if($_SESSION[endent]<0) $_SESSION[endent] = 0;
	}
}

//obter o codigo correto do menu
$SQLm = "SELECT codigo FROM controle WHERE titulo='menu'";
$QRm = mysql_query($SQLm);
$rm = mysql_fetch_array($QRm);
$codigo_menu = $rm[codigo];
?>

<script type="text/javascript">
busca_status('Editando as Configurações');

$('#bt_voltar_<?= $codigo ?>').click(function(){
	$('#full_background').hide();
	$('#full_frame').hide();
});

//titulos
$('[title]').tooltip({ 
	track: true, 
	delay: 2, 
	showURL: false, 
	showBody: true, 
	fade: 300 
});

$('#form_duplicar_<?= $codigo ?>').bind('submit',function(){
	var formContent = $('#form_configuracoes_<?= $codigo ?>').serialize();
	$('#full_frame').load('utf2iso.php?file=plugins/configuracoes/configuracoes_editar.php',formContent);
	//$('#configuracoes_todas').load('utf2iso.php?file=plugins/configuracoes/configuracoes_lista.php');
	return false;
});

$('#form_configuracoes_<?= $codigo ?>').bind('submit',function(){
	var formContent = $('#form_configuracoes_<?= $codigo ?>').serialize();
	$('#full_frame').load('utf2iso.php?file=plugins/configuracoes/configuracoes_editar.php',formContent);
	$('#configuracoes_todas').load('utf2iso.php?file=plugins/configuracoes/configuracoes_lista.php');
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
<?= $configuracoes_acao ?> Configurações 
</legend>


<? if($gravar=="duplicar"){ ?>
<form name="form_duplicar_<?= $codigo ?>" id="form_configuracoes_<?= $codigo ?>" action="">
	<input type="image" src="images/bt_avancar.png" name="submit" class="bt_duplicar" title="Duplicar" />
	<input type="hidden" name="gravar" value="<?= $gravar ?>" />
	<input type="hidden" name="duplicar" value="1" />
	
	<div class="colunas_2">
	<label>Selecione o conjunto que deseja copiar (mÃºltiplos)</label>
	<select rows="4" multiple class="text100" style="height: 260px" name="codigo">
	<? configuracoes($codigo,$selected); ?>
	</select>
	</div>

	<div class="colunas_2">
	<label>Destino das novas configurações (apenas 1)</label>
	<select rows="4" multiple="false" class="text100" style="height: 260px" name="rel">
	<option value="">Nenhum</option>
	<? configuracoes($_SESSION["user_codigo"],$selected); ?>
	</select>
	</div>
	
	<div class="clear_left"></div>

<? }else{ ?>
<form name="form_configuracoes_<?= $codigo ?>" id="form_configuracoes_<?= $codigo ?>" action="">
	<?
	$wr = "WHERE codigo=$codigo";
	$qr = mysql_query("SELECT * FROM controle $wr");
	$rs = mysql_fetch_array($qr);
	?>

	<input type="hidden" name="gravar" value="<?= $gravar ?>" />
	<input type="hidden" name="codigo" value="<?= $rs[codigo] ?>" />
	
	<div>
	<label for="titulo">Descrição</label>
	<input type="text" name="descricao" class="titulo_servico" value="<?= $rs[descricao] ?>" />
	</div>

	<?
	if($rs){
	for($t=1;$t<mysql_num_fields($qr);$t++){ ?>
		<? if(mysql_field_name($qr,$t)!="descricao"){ ?>
		<div class="colunas_2">
		<? if((mysql_field_name($qr,$t)=="usuario")){
		}else{ ?>
		<label for="<?= mysql_field_name($qr,$t) ?>"><?= comment($dbase,'controle',mysql_field_name($qr,$t)) ?></label>
		<? } ?>
		
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
		
		<? }elseif(strstr("rel",mysql_field_name($qr,$t))){ ?>
			<select rows="4" multiple class="text100" style="height: 200px" name="<?= mysql_field_name($qr,$t) ?>" id="<?= mysql_field_name($qr,$t).'_'.$rs[codigo] ?>">
			<option value="">Nenhum</option>
			<? configuracoes($_SESSION["user_codigo"],$rs[mysql_field_name($qr,$t)]); ?>
			</select>
			
		<? }elseif(mysql_field_name($qr,$t)=="usuario" && $rs[rel]==$codigo_menu){ ?>
		<label for="<?= mysql_field_name($qr,$t) ?>"><?= comment($dbase,'controle',mysql_field_name($qr,$t)) ?></label>
			<select rows="4" multiple class="text100" style="height: 200px; margin-bottom: 5px;" name="<?= mysql_field_name($qr,$t) ?>[]" id="<?= mysql_field_name($qr,$t).'_'.$rs[codigo] ?>">
			<option value="">Nenhum</option>
			<?
			$usr = explode(',',$rs[mysql_field_name($qr,$t)]);
			$wru = "WHERE titulo LIKE '%user%' ORDER BY titulo ASC";
			$qru = mysql_query("SELECT * FROM controle $wru");
			while($rsu = mysql_fetch_array($qru)){ ?>
			<option value="<?= $rsu[codigo] ?>"<? if(in_array($rsu[codigo],$usr)) echo selected ?>><?= $rsu[descricao] ?></option>
			<? } ?>
			</select>
			<small>Utilize o Control ou Shift para selecionar Multiplos Usuarios</small>
			
		<? }elseif(mysql_field_name($qr,$t)!="descricao" && mysql_field_name($qr,$t)!="usuario"){ ?>
			<input type="text" class="text100" name="<?= mysql_field_name($qr,$t) ?>" value="<?= $rs[mysql_field_name($qr,$t)] ?>" />
			
		<? } ?>
		</div>
		<? } #str
	} #for ?> 
	
	<div class="clear_left"></div>
	<div class="imageright"><hr />
	<input type="image" src="images/button_ok.png" name="submit" class="image" title="Enviar os Dados" /></div>

	<? } #rs
} #duplicar 
?>

</form>
</fieldset>
