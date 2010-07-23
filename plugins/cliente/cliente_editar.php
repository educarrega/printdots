<? 
session_start();
#require_once "../../conn.php";
#$return = 1;
$table = "cliente";
if($gravar=="incluir"){
	$cols = "nome_fantasia";
	$values = "'$nome_fantasia'";
	ins($table,$cols,$values);
	
	#obter o codigo da inclusao
	$SQLm = "SELECT MAX(codigo) as codigo FROM $table";
	$QRm = mysql_query($SQLm);
	$rsm = mysql_fetch_array($QRm);
	$codigo = $rsm[codigo];
	?>
	<script language="javascript">
	var filter = '<?= substr($nome_fantasia,0,1) ?>';
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
	nome_fantasia = '$nome_fantasia',
	razao_social = '$razao_social',
	cnpj = '$cnpj',
	inscrest = '$inscrest',
	inscrmun = '$inscrmun',
	cidade = '$cidade',
	estado = '$estado',
	endereco = '$endereco',
	cep = '$cep',
	telefone = '$telefone',
	website = '$website',
	email = '$email',
	contato = '$contato',
	messenger = '$messenger',
	ref_comerciais = '$ref_comerciais',
	ref_bancarias = '$ref_bancarias',
	obs = '$obs',
	texto_comercial = '$texto_comercial',
	usuario = '$usuario',
	senha = '$senha',
	visita = '$visita',
	desde='".mydate($desde)."',
	ativo = '$ativo',
	ativo_site = '$ativo_site',
	tipo_representante = '$tipo_representante',
	tipo_cliente = '$tipo_cliente',
	tipo_fornecedor = '$tipo_fornecedor',
	tipo_transporte = '$tipo_transporte',
	codigo_material = '".@implode(',',$materiais)."',
	codigo_transporte = '".@implode(',',$transportes)."'
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
$cliente_acao = "Incluindo";
$alvo = "#add_cliente";
if($codigo){
$gravar = "editar";
$cliente_acao = "Editando";
$alvo = "#cliente_$codigo";
}

?>

<script type="text/javascript">
busca_status('Editando o cadastro de clientes');

$('#bt_voltar_<?= $codigo ?>').click(function(){
	<? if($codigo){ ?>
	$('<?= $alvo ?>').load('utf2iso.php?file=plugins/cliente/cliente_lista.php',{codigo:'<?= $codigo ?>'});
	$('<?= $alvo ?>').css({'overflow':'hidden', 'height': '65px'});
	$('#cliente_status_<?= $codigo ?>').show();
	<? }else{ ?>
	$('<?= $alvo ?>').slideUp('slow', function(){
		$('<?= $alvo ?>').html('');
		$('#bt_add_cliente').fadeIn();
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

$('#form_cliente_<?= $codigo ?>').bind('submit',function(){
	var formContent = $('#form_cliente_<?= $codigo ?>').serialize();
	$('<?= $alvo ?>').load('utf2iso.php?file=plugins/cliente/cliente_editar.php',formContent);
	return false;
});

function ampliar_material(codigo){
	$("#full_frame").load('utf2iso.php?file=plugins/material/material_ampliar.php',{codigo: codigo});
	$("#full_frame").slideDown(function(){
		$("#full_background").fadeIn(500);
	});
}

function ampliar_transporte(codigo){
	$("#full_frame").load('utf2iso.php?file=plugins/cliente/cliente_os.php',{agencia: codigo});
	$("#full_frame").slideDown(function(){
		$("#full_background").fadeIn(500);
	});
}

</script>

<fieldset class="fieldset_clientes">
<legend>
<img id="bt_voltar_<?= $codigo ?>" class="bt_voltar" src="images/bt_voltar.png" align="absmiddle" title="Cancela o processo de edição" /> 
<? if($codigo){ ?>
<img src="images/bt_editar.png" align="absmiddle" /> 
<? }else{ ?>
<img src="images/bt_mais.png" align="absmiddle" /> 
<? } ?>
<?= $cliente_acao ?> Cliente </legend>

<?
if($codigo){
	$wr = "WHERE codigo=$codigo";
	$qr = mysql_query("SELECT * FROM cliente $wr");
	$rs = mysql_fetch_array($qr);
}
?>
<form name="form_cliente_<?= $codigo ?>" id="form_cliente_<?= $codigo ?>" action="" method="post">
<input type="hidden" name="gravar" value="<?= $gravar ?>" />
<input type="hidden" name="codigo" value="<?= $rs[codigo] ?>" />

<div>
<label for="nome_fantasia">Nome fantasia</label>
<input type="text" name="nome_fantasia" class="titulo_servico" value="<?= $rs[nome_fantasia] ?>" />
</div>

<?
if($rs){
for($t=1;$t<mysql_num_fields($qr);$t++){ ?>
	<? if(mysql_field_name($qr,$t)!="nome_fantasia"){ ?>
	<? if(mysql_field_name($qr,$t)=="codigo_transporte") { ?><div class="clear_left"></div><? } ?>
	<div class="colunas_2">
	<label for="<?= mysql_field_name($qr,$t) ?>"><?= comment($dbase,'cliente',mysql_field_name($qr,$t)) ?></label>
	<? if(strstr("endereco,telefone,email,contato,messenger,ref_comerciais,ref_bancarias,obs,
razao_social,texto_comercial,website",mysql_field_name($qr,$t))){ ?>
	<textarea rows="3" name="<?= mysql_field_name($qr,$t) ?>"><?= $rs[mysql_field_name($qr,$t)] ?></textarea>
	<? }elseif(mysql_field_len($qr,$t)=='1'){ ?>
		<input type="hidden" id="<?= mysql_field_name($qr,$t) ?>_<?= $rs[codigo] ?>" name="<?= mysql_field_name($qr,$t) ?>" value="<?= $rs[mysql_field_name($qr,$t)] ?>" />
		<? $check = $rs[mysql_field_name($qr,$t)]; ?>
		<div id="check_<?= mysql_field_name($qr,$t) ?>_<?= $rs[codigo]?>" class="checkbox" title="Ativar/Desativar"></div>
		<? $check = $check ? 0 : 1; ?>
		<script language="javascript">check(<?= $check ?>,<?= $rs[codigo]?>,'<?= mysql_field_name($qr,$t) ?>',1)</script>
	
	<? }elseif(strstr("ultima,desde",mysql_field_name($qr,$t))){ ?>
	<input type="text" class="text" name="<?= mysql_field_name($qr,$t) ?>" value="<? if($rs[mysql_field_name($qr,$t)]) echo mydate($rs[mysql_field_name($qr,$t)]) ?>" />
	<? }elseif(strstr("estado",mysql_field_name($qr,$t))){ ?>
	<select name="estado">
	<option value>Estados</option>
	<option value="AC"<? if(strtoupper($rs[estado])=="AC") echo " selected" ?>>Acre</option>
	<option value="AL"<? if(strtoupper($rs[estado])=="AL") echo " selected" ?>>Alagoas</option>
	<option value="AM"<? if(strtoupper($rs[estado])=="AM") echo " selected" ?>>Amazonas</option>
	<option value="AP"<? if(strtoupper($rs[estado])=="AP") echo " selected" ?>>Amapá</option>
	<option value="BA"<? if(strtoupper($rs[estado])=="BA") echo " selected" ?>>Bahia</option>
	<option value="CE"<? if(strtoupper($rs[estado])=="CE") echo " selected" ?>>Ceará</option>
	<option value="DF"<? if(strtoupper($rs[estado])=="DF") echo " selected" ?>>Distrito Federal</option>
	<option value="ES"<? if(strtoupper($rs[estado])=="ES") echo " selected" ?>>Espírito Santo</option>
	<option value="GO"<? if(strtoupper($rs[estado])=="GO") echo " selected" ?>>Goiás</option>
	<option value="MA"<? if(strtoupper($rs[estado])=="MA") echo " selected" ?>>Maranhão</option>
	<option value="MT"<? if(strtoupper($rs[estado])=="MT") echo " selected" ?>>Mato Grosso</option>
	<option value="MS"<? if(strtoupper($rs[estado])=="MS") echo " selected" ?>>Mato Grosso do Sul</option>
	<option value="MG"<? if(strtoupper($rs[estado])=="MG") echo " selected" ?>>Minas Gerais</option>
	<option value="PA"<? if(strtoupper($rs[estado])=="PA") echo " selected" ?>>Pará</option>
	<option value="PB"<? if(strtoupper($rs[estado])=="PB") echo " selected" ?>>Paraíba</option>
	<option value="PR"<? if(strtoupper($rs[estado])=="PR") echo " selected" ?>>Paraná</option>
	<option value="PE"<? if(strtoupper($rs[estado])=="PE") echo " selected" ?>>Pernambuco</option>
	<option value="PI"<? if(strtoupper($rs[estado])=="PI") echo " selected" ?>>Piauí</option>
	<option value="RJ"<? if(strtoupper($rs[estado])=="RJ") echo " selected" ?>>Rio de Janeiro</option>
	<option value="RN"<? if(strtoupper($rs[estado])=="RN") echo " selected" ?>>Rio Grande do Norte</option>
	<option value="RO"<? if(strtoupper($rs[estado])=="RO") echo " selected" ?>>RondÃ´nia</option>
	<option value="RS"<? if(strtoupper($rs[estado])=="RS") echo " selected" ?>>Rio Grande do Sul</option>
	<option value="RR"<? if(strtoupper($rs[estado])=="RR") echo " selected" ?>>Roraima</option>
	<option value="SC"<? if(strtoupper($rs[estado])=="SC") echo " selected" ?>>Santa Catarina</option>
	<option value="SE"<? if(strtoupper($rs[estado])=="SE") echo " selected" ?>>Sergipe</option>
	<option value="SP"<? if(strtoupper($rs[estado])=="SP") echo " selected" ?>>São Paulo</option>
	<option value="TO"<? if(strtoupper($rs[estado])=="TO") echo " selected" ?>>Tocantins</option>
	</select>
	
	<? }elseif(mysql_field_name($qr,$t)=="codigo_material"){ ?>
	Se Fornecedor, selecione os Materiais e Serviços oferecidos.<br /><br />
	<div class="div_textarea_scroll" style="height: 300px;">
	<?
	#selecionados
	$materiais = explode(',',$rs[mysql_field_name($qr,$t)]);
	$SQLe = "SELECT * FROM material ORDER BY ativo, titulo ASC";
	$QRe = mysql_query($SQLe);
	while($eq = mysql_fetch_array($QRe)){ 
		if(in_array("$eq[codigo]",$materiais)){ ?>
	
		<label>
		<li id="material_<?= $tr[codigo] ?>">
		<span class="material_status">
		<img src="images/bt_ampliar.png" onclick="ampliar_material('<?= $eq[codigo] ?>')" alt="ampliar" title="Mais detalhes" /> 
		<? if(!$eq[ativo]){ ?><img src="images/bt_atencao.png" alt="i" title="Inativo" /><? } ?>
		</span>
		
		<input type="checkbox" class="checkbox" name="materiais[]" value="<?= $eq[codigo] ?>" checked ><b><?= $eq[codigo] ?> - <?= $eq[titulo] ?></b><br />
		<?= $eq[descricao] ?>
		</li>
		</label>
		
		<? }
	}
	
	#nao selecionados
	$QRe2 = mysql_query($SQLe);
	while($eq2 = mysql_fetch_array($QRe2)){ 
		if(in_array("$eq2[codigo]",$materiais)){}else{ ?>
		
		<label>
		<li id="material_<?= $tr[codigo] ?>">
		<span class="material_status">
		<img src="images/bt_ampliar.png" onclick="ampliar_material('<?= $eq2[codigo] ?>')" alt="ampliar" title="Mais detalhes" /> 
		<? if(!$eq2[ativo]){ ?><img src="images/bt_atencao.png" alt="i" title="Inativo" /><? } ?>
		</span>
		
		<input type="checkbox" class="checkbox" name="materiais[]" value="<?= $eq2[codigo] ?>"><b><?= $eq2[codigo] ?> - <?= $eq2[titulo] ?></b><br />
		<?= $eq2[descricao] ?>
		</li>
		</label>
		
		<? }
	} ?>
	</div>


	<? }elseif(mysql_field_name($qr,$t)=="codigo_transporte"){ ?>
	Escolha os meios de transporte utilizadas por este Cliente ou Fornecedor.<br /><br />
	<div class="div_textarea_scroll" style="height: 300px;">
	<?
	#selecionados
	$transportes = explode(',',$rs[mysql_field_name($qr,$t)]);
	$SQLe = "SELECT codigo,nome_fantasia,contato,telefone,obs,ativo FROM cliente where tipo_transporte='1' ORDER BY ativo, nome_fantasia ASC";
	$QRe = mysql_query($SQLe);
	while($eq = mysql_fetch_array($QRe)){ 
		if(in_array("$eq[codigo]",$transportes)){ ?>
		
		<label>
		<li id="transporte_<?= $tr[codigo] ?>">
		<span class="material_status">
		<img src="images/bt_ampliar.png" onclick="ampliar_transporte('<?= $eq[codigo] ?>')" alt="ampliar" title="<?= nl2br($eq[obs]) ?>" /> 
		<? if(!$eq[ativo]){ ?><img src="images/bt_atencao.png" alt="i" /><? } ?>
		</span>
		
		<input type="checkbox" name="transportes[]" value="<?= $eq[codigo] ?>" checked ><b><?= $eq[codigo] ?> - <?= $eq[nome_fantasia] ?></b><br />
		<small><?= $eq[contato] ?> <?= $eq[telefone] ?></small>
		</li>
		</label>
		
		<? }
	}
	
	#nao selecionados
	$QRe2 = mysql_query($SQLe);
	while($eq2 = mysql_fetch_array($QRe2)){ 
		if(in_array("$eq2[codigo]",$transportes)){}else{ ?>
		
		<label>
		<li id="transporte_<?= $tr[codigo] ?>">
		<span class="material_status">
		<img src="images/bt_ampliar.png" onclick="ampliar_transporte('<?= $eq2[codigo] ?>')" alt="ampliar" title="<?= nl2br($eq2[obs]) ?>"/> 
		<? if(!$eq2[ativo]){ ?><img src="images/bt_atencao.png" alt="i" /><? } ?>
		</span>
		
		<input type="checkbox" name="transportes[]" value="<?= $eq2[codigo] ?>"><b><?= $eq2[codigo] ?> - <?= $eq2[nome_fantasia] ?></b><br />
		<small><?= $eq2[contato] ?> <?= $eq2[telefone] ?></small>
		</li>
		</label>
		
		<? }
	} ?>
	</div>


	<? }elseif(mysql_field_name($qr,$t)!="nome_fantasia"){ ?>
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
