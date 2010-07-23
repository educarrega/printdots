<? 
session_start();
#require_once "../../conn.php";

$table = "pedido";
if($gravar=="incluir" || $gravar=="incluir_sub"){
	if($gravar=="incluir_sub"){
	$cols = "codigo_rel,material_quantidade,valor_unitario,valor_acrescimo,valor_total,ativo";
	$values = "'$codigo','1','0','0','0','0'";
	}else{
	$cols = "data_criacao,data_lancamento,transporte_valor";
	$values = "'". date("Y-m-d") ."','0000-00-00','0.00'";
	}
	ins($table,$cols,$values);
	
	#obter o codigo da inclusao
	$SQLm = "SELECT MAX(codigo) as codigo FROM $table";
	$QRm = mysql_query($SQLm);
	$rsm = mysql_fetch_array($QRm);
	?>
	
	<script language="javascript">
	//var filter = '<?= substr($titulo,0,1) ?>';
	<? if($gravar=="incluir"){ 
	$codigo = $rsm[codigo]; ?>
	$('#<?= $table ?>_todas').load('utf2iso.php?file=plugins/<?= $table ?>/<?= $table ?>_lista.php',{editar:'<?= $codigo ?>'});
	<? }else{ 
	$codigo_sub = $rsm[codigo];?>
	$(document).ready(function(){
		$('#editar_item_<?= $codigo ?>').slideDown('slow');
		editar_item_<?= $codigo ?>(<?= $codigo_sub ?>,'','1','0','0')
	})
	<? } ?>
	</script>
	<?
}
if($gravar=="excluir"){
	
	#verificando se nao ha relacionados para exclui-los junto
	$SQLr = "SELECT codigo FROM $table WHERE codigo_rel='$codigo'";
	$QRr = mysql_query($SQLr);
	while($rsr = mysql_fetch_array($QRr)){
		del($table,"WHERE codigo=$rsr[codigo]");
	}
	
	#verificando se nao ha pedido pai para limpar campo cliente_codigo em caso do pedido estar vazio
	$SQLc = "SELECT codigo_rel FROM $table WHERE codigo='$codigo'";
	$QRc = mysql_query($SQLc);
	$rsc = mysql_fetch_array($QRc);
		$codigo_pai = $rsc[codigo_rel];
		del($table,"WHERE codigo=$codigo");
	
	#limpando o campo cliente
	if($codigo_pai){
	$SQLp = "SELECT codigo FROM $table WHERE codigo_rel='$codigo_pai'";
	$QRp = mysql_query($SQLp);
	$rsp = mysql_fetch_array($QRp);
		if(!$rsp[codigo]){
		$cols_cliente = "cliente_codigo = NULL";
		$condition_cliente = " WHERE codigo=$codigo_pai";
		upd($table,$cols_cliente,$condition_cliente);
		}else{
		echo "1"; 
		#ha filhos
		#destinado ao pedido_excluir para nao dar reload em caso de codigo filho existente
		}
	}
	
	$codigo_pai = "";
	$codigo = "";
	die();
}
if($gravar=="editar"){
	if($codigo_sub){
		$condition = " WHERE codigo=$codigo_sub";
		$cols = "
		material_codigo = '$material_codigo',
		material_quantidade = '$material_quantidade',
		valor_unitario = '$valor_unitario',
		valor_acrescimo = '$valor_acrescimo',
		valor_total = '$valor_total',
		peso = '$peso'
		";
		#echo $condition.$cols;
		if($cliente_codigo){
			$SQLc = "SELECT codigo FROM $table WHERE codigo='$codigo' AND (cliente_codigo='' OR ISNULL(cliente_codigo))";
			$QRc = mysql_query($SQLc);
			if(mysql_num_rows($QRc)>0){
				$cols_cliente = "cliente_codigo = '$cliente_codigo'";
				$condition_cliente = " WHERE codigo=$codigo";
				#echo $cols_cliente;
				#echo $condition_cliente;
				upd($table,$cols_cliente,$condition_cliente);
			}			
		} #die;
	}else{
		$condition = " WHERE codigo=$codigo";
		$cols = "
		cliente_contato = '$cliente_contato',
		cliente_email = '$cliente_email',
		transporte_codigo = '$transporte_codigo',
		transporte_valor = '$transporte_valor',
		transporte_lancar = '$transporte_lancar',
		valor_total = '$valor_total',
		descricao = '$descricao',
		email = '$email'
		";
		if($data_criacao) $cols .= ",data_criacao = '". mydate($data_criacao) ."'";
		if($data_lancamento) $cols .= ",data_lancamento = '". mydate($data_lancamento) ."'";
	}
	upd($table,$cols,$condition);
}
if($gravar=="efetivar"){
	$condition = " WHERE codigo=$codigo";
	$cols = "
	ativo = '1',
	data_lancamento = '".date("Y-m-d")."'
	";
	upd($table,$cols,$condition);
	die($cols);
}

if($codigo){
	$wr = "WHERE codigo=$codigo";
	$qr = mysql_query("SELECT * FROM pedido $wr");
	$rs = mysql_fetch_array($qr);
	
	$gravar = "editar";
	$pedido_acao = "Editando o";
	$alvo = "#pedido_$codigo";
	
	if(substr(mydate($rs[data_lancamento]),0,2)!="00"){
		$efetivado = 1;
		$pedido_acao = "Visualizando o";
	}
}else{
	die;
}


?>

<script type="text/javascript">
busca_status('<?= $pedido_acao ?> Pedido');

$('.bt_voltar_<?= $codigo ?>').click(function(){
	<? if($codigo){ ?>
	$('<?= $alvo ?>').load('utf2iso.php?file=plugins/pedido/pedido_lista.php',{codigo:'<?= $codigo ?>'});
	$('<?= $alvo ?>').css({'overflow':'hidden', 'height': '65px'});
	$('#pedido_status_<?= $codigo ?>').show();
	<? }else{ ?>
	$('<?= $alvo ?>').slideUp('slow', function(){
		$('<?= $alvo ?>').html('');
		$('#bt_add_pedido').fadeIn();
	});
	<? } ?>
});
$("#bt_send_pedido_<?= $rs[codigo] ?>").click(function(){
	pedido_send('<?= $rs[codigo] ?>');
	var p = setTimeout(function(){
		$("#full_background").fadeIn(500);
		$("#full_background").click(function(){
		$("<?= $alvo ?>").load('utf2iso.php?file=plugins/pedido/pedido_editar.php',{codigo: '<?= $rs[codigo] ?>'});
		});
	}, 5000);
});
$("#bt_print_pedido_<?= $rs[codigo] ?>").click(function(){
	print_pedido('<?= $rs[codigo] ?>');
	var p = setTimeout(function(){
		$("#full_background").fadeIn(500);
		$("#full_background").click(function(){
		$("<?= $alvo ?>").load('utf2iso.php?file=plugins/pedido/pedido_editar.php',{codigo: '<?= $rs[codigo] ?>'});
		});
	}, 5000);
});
//titulos
$('[title]').tooltip({ 
	track: false, 
	delay: 0, 
	showURL: false, 
	showBody: true, 
	fade: 30 
});

$('#form_sub_<?= $codigo ?>').bind('submit',function(){
	var formContent = $('#form_sub_<?= $codigo ?>').serialize();
	//alert(formContent);
	$('<?= $alvo ?>').load('utf2iso.php?file=plugins/pedido/pedido_editar.php',formContent);
	return false;
});

$('#form_pedido_<?= $codigo ?>').bind('submit',function(){
	var formContent = $('#form_pedido_<?= $codigo ?>').serialize();
	//alert(formContent);
	$('<?= $alvo ?>').load('utf2iso.php?file=plugins/pedido/pedido_editar.php',formContent);
	return false;
});

$('#bt_add_item_<?= $codigo ?>').click(function(){
	$('<?= $alvo ?>').load('utf2iso.php?file=plugins/pedido/pedido_editar.php',{gravar:'incluir_sub', codigo:'<?= $codigo ?>'});
})

function excluir_item_<?= $codigo ?>(codigo){
	$.post('utf2iso.php?file=plugins/pedido/pedido_editar.php',{gravar:'excluir',codigo:codigo},function(filhos){
		$('#editar_item_<?= $codigo ?>').slideUp('slow');
		$('#pedido_item_'+codigo).slideUp('slow',function(){
			$('#editar_item_<?= $codigo ?>').html();
			if(filhos){
				$('#editar_item_<?= $codigo ?>').html();
				monta_item_<?= $codigo ?>();
			}else{
				$('<?= $alvo ?>').load('utf2iso.php?file=plugins/pedido/pedido_editar.php',{codigo:'<?= $codigo ?>'});
			}
		});
	});
}

function filtro_material_<?= $codigo ?>(){
	var texto = $('#filtro_material_<?= $codigo ?>').val();
	$.post('utf2iso.php?file=plugins/pedido/pedido_editar_combo_material.php',{filtro:texto,codigo_pedido:'<?= $codigo ?>'},function(data){
	$('#select_material_<?= $codigo ?>').html(data);
	});
}
$('#filtro_material_<?= $codigo ?>').keyup(function(){filtro_material_<?= $codigo ?>();})
$('#filtro_material_<?= $codigo ?>').focus(function(){
	if($(this).val('Pesquise')){$(this).val('')}
})
$('#filtro_material_<?= $codigo ?>').blur(function(){
	$(this).val('Pesquise');
})

function monta_material_<?= $codigo ?>(codigo_material,selected_material){
	$.post('utf2iso.php?file=plugins/pedido/pedido_editar_combo_material.php',{codigo_material:codigo_material,selected:selected_material,codigo_pedido:'<?= $codigo ?>'},function(data){
	$('#select_material_<?= $codigo ?>').html(data);
	});
}

function monta_cliente_<?= $codigo ?>(codigo_material){
	//var codigo_material = $('input[name=codigo_material[]]:checked').val();
	$.post('utf2iso.php?file=plugins/pedido/pedido_editar_combo_cliente.php',{codigo_material:codigo_material,codigo_pedido:'<?= $codigo ?>'},function(data){
	$('#select_cliente_<?= $codigo ?>').html(data);
	});
}

function monta_item_<?= $codigo ?>(){
	$('#select_item_<?= $codigo ?>').load('utf2iso.php?file=plugins/pedido/pedido_editar_combo_item.php',{codigo_pedido: '<?= $codigo ?>'});
}

function editar_item_<?= $codigo ?>(codigo,material_codigo,material_quantidade,peso_unitario,valor_unitario,valor_acrescimo){
	//alert(codigo +' - '+ material_codigo +' - '+ material_quantidade +' - '+ valor_unitario +' - '+ valor_acrescimo);
	$('#editar_item_<?= $codigo ?>').slideDown('slow');
	$('#valor_acrescimo_<?= $codigo ?>').val(valor_acrescimo);
	$('#material_quantidade_<?= $codigo ?>').val(material_quantidade);
	$('#codigo_sub_<?= $codigo ?>').val(codigo);
	monta_material_<?= $codigo ?>(material_codigo,material_codigo);
	monta_cliente_<?= $codigo ?>(material_codigo);
	set_valores_material_<?= $codigo ?>(material_codigo,valor_unitario,peso_unitario);
	/**/
}

function set_valores_material_<?= $codigo ?>(codigo,valor,peso_unitario){
	$('#material_codigo_<?= $codigo ?>').val(codigo);
	$('#valor_unitario_<?= $codigo ?>').val(valor);
	$('#valor_unitario_<?= $codigo ?>_bkp').val(valor);
	$('#peso_unitario_<?= $codigo ?>').val(peso_unitario);
	calcula_material_<?= $codigo ?>();
	//alert(codigo +' '+ valor);
}

function calcula_material_<?= $codigo ?>(){
	//alert('calcula_material_<?= $codigo ?>');
	mu = $('#valor_unitario_<?= $codigo ?>').val();
	mq = $('#material_quantidade_<?= $codigo ?>').val();
	ma = $('#valor_acrescimo_<?= $codigo ?>').val();
	valor_total = parseFloat(ma) + mu * mq;
	
	mp = $('#peso_unitario_<?= $codigo ?>').val();
	peso_total = mp * mq;
	
	$('#valor_total_<?= $codigo ?>').val(valor_total.toFixed(2));
	$('#material_peso_<?= $codigo ?>').val(peso_total.toFixed(2));
}

function pedido_totalizacao_<?= $codigo ?>(){
	vf = parseFloat($('#transporte_valor_<?= $codigo ?>').val())+parseFloat($('#total_ca_<?= $codigo ?>').val());
	tt = vf.toFixed(2);
	$('#pedido_valor_total_<?= $codigo ?>').val(tt);
	$('#pedido_total_<?= $codigo ?>').text(tt);
	//alert(tt);
}

function ampliar_cliente_<?= $codigo ?>(codigo){
	$("#full_frame").load('utf2iso.php?file=plugins/cliente/cliente_os.php',{agencia: codigo});
	$("#full_frame").slideDown(function(){
		$("#full_background").fadeIn(500);
	});
}
<? if(!$efetivado){ ?>
$('#data_criacao_<?= $codigo ?>').focus(function(){
	$(this).calendario({
		target:'#data_criacao_<?= $codigo ?>'
	});
});
<? } ?>

$(document).ready(function(){
	monta_material_<?= $codigo ?>('','');
	monta_item_<?= $codigo ?>();
})

</script>

<? if(!$efetivado){ ?>
<form name="form_sub_<?= $codigo ?>" id="form_sub_<?= $codigo ?>" action="">
<? } ?>
<input type="hidden" name="gravar" value="editar" />
<input type="hidden" name="codigo_sub" id="codigo_sub_<?= $codigo ?>" value="" />
<input type="hidden" name="codigo" value="<?= $codigo ?>" />

<? if(!$efetivado){ ?>
<fieldset class="fieldset_infos">
<? }else{ ?>
<fieldset class="fieldset_atencao">
<? } ?>
<legend>
<img class="bt_voltar_<?= $codigo ?> bt_voltar" src="images/bt_voltar.png" align="absmiddle" title="Voltar para a o modo lista" /> 
<? if(!$efetivado){ ?>
<img src="images/bt_editar.png" align="absmiddle" /> 
<? } ?>
<?= $pedido_acao ?> Pedido</legend>

<div>
<label>Cod. Pedido</label>
<div class="titulo_servico"><?= $rs[codigo] ?></div>
</div>
	
<div id="editar_item_<?= $codigo ?>" class="editar_item">

	<div class="colunas_2">
	<label>Escolha Material ou Serviço</label>
	<input type="text" id="filtro_material_<?= $codigo ?>" value="Pesquise" style="margin-bottom:1px;">
	<div class="div_textarea_scroll" style="height: 200px" id="select_material_<?= $codigo ?>">
	Carregando materiais...
	</div>
	</div>

	<div class="colunas_2">
	<label>Escolha o fornecedor</label>
	<div class="div_textarea_scroll" style="height: 227px" id="select_cliente_<?= $codigo ?>">
	<img src="images/bt_infos.png" style="float: left"> Informe um material ou serviço para obter os fornecedores disponíveis.
	</div>
	</div>

	<div class="clear_left"></div>
		
		<div class="pedido_coluna_5">
		<label>Quantidade</label>
		<input type="text" class="text" name="material_quantidade" id="material_quantidade_<?= $codigo ?>" value="1" onkeyup="calcula_material_<?= $codigo ?>()" />
		</div>
		
		<div class="pedido_coluna_5">
		<label>Cod. Material</label>
		<input type="text" class="text" name="material_codigo" id="material_codigo_<?= $codigo ?>" value="" READONLY />
		</div>
		
		<div class="pedido_coluna_5">
		<label>Peso</label>
		<input type="hidden" id="peso_unitario_<?= $codigo ?>" value="" />
		<input type="text" class="text" name="peso" id="material_peso_<?= $codigo ?>" value="" READONLY />
		</div>

		<div class="pedido_coluna_5">
		<label>Valor Unitário</label>
		<input type="text" class="text" name="valor_unitario" id="valor_unitario_<?= $codigo ?>" value="0" onkeyup="calcula_material_<?= $codigo ?>()" onblur="if(this.value==''){this.value = document.form_sub_<?= $codigo ?>.valor_unitario_bkp.value }" />
		<input type="hidden" name="valor_unitario_bkp" id="valor_unitario_<?= $codigo ?>_bkp" value="0" />
		</div>

		<div class="pedido_coluna_5">
		<label>Valor total</label>
		<input type="text" class="text" name="valor_total" id="valor_total_<?= $codigo ?>" value="0" onkeyup="calcula_material_<?= $codigo ?>()" READONLY />
		</div>
	
		<div class="pedido_coluna_5">
		<label>Acréscimo</label>
		<input type="text" class="text" name="valor_acrescimo" id="valor_acrescimo_<?= $codigo ?>" value="0"  onkeyup="calcula_material_<?= $codigo ?>()" />
		</div>
	

	<div class="imageright">
	<input type="image" src="images/button_ok.png" name="submit" class="image" title="Gravar Dados" />
	</div>
	
</div>

<? if($efetivado){ ?>
	<label>Pedido Efetivado</label>
	Este pedido encontra-se efetivado e não pode mais ser editado, pois o mesmo já consta como um lançamento em Contas Ã  Pagar.<br/><br/>
	Ã‰ possível porém, duplicá-lo e efetuar um novo pedido similar, podendo editá-lo da forma que necessitar antes de lançá-lo como um novo item em Contas Ã  Pagar, deseja fazer isto?<br /><br/>
	<input type="button" value="Duplicar Pedido">
<? }else{ ?>
	Escolha um dos itens abaixo para editar, ou adicione um novo item.<br/><br/>
<? } ?>

</fieldset>
</form>

<? if(!$efetivado){ ?>
<input type="image" src="images/bt_mais.png" name="submit" class="bt_add_item" id="bt_add_item_<?= $codigo ?>" title="Adicionar Item" />
<? } ?>

<? $SQLi = "SELECT codigo FROM pedido WHERE codigo_rel='$codigo'";
$SQLi = mysql_query($SQLi);
if(mysql_num_rows($SQLi)==0){ ?>
<fieldset class="fieldset_atencao">
<? }else{ ?>
<fieldset class="fieldset_itens">
<? } ?><legend>
<img class="bt_voltar_<?= $codigo ?> bt_voltar" src="images/bt_voltar.png" align="absmiddle" title="Voltar para a o modo lista" /> 
Itens</legend>
	<div id="select_item_<?= $codigo ?>">Carregando itens do pedido...</div>
	<br />
</fieldset>


<? if($rs[cliente_codigo]){ ?>
<? if(!$efetivado){ ?>
<form name="form_pedido_<?= $codigo ?>" id="form_pedido_<?= $codigo ?>" action="">
<? } ?>
<input type="hidden" name="gravar" value="<?= $gravar ?>" />
<input type="hidden" name="codigo" value="<?= $codigo ?>" />
<input type="hidden" name="valor_acrescimo" id="pedido_valor_acrescimo_<?= $codigo ?>" value="" />
<input type="hidden" name="valor_total" id="pedido_valor_total_<?= $codigo ?>" value="" />
<fieldset class="fieldset_transporte">
<legend><img class="bt_voltar_<?= $codigo ?> bt_voltar" src="images/bt_voltar.png" align="absmiddle" title="Voltar para a o modo lista" /> 
Transporte</legend>
	<div class="colunas_2">
	<label>Sistema de Transporte</label>
	<div class="div_textarea_scroll" style="height: 150px" id="select_transporte_<?= $codigo ?>">
	<? 
	$SQLc = "SELECT codigo,nome_fantasia,razao_social,cnpj,telefone,contato,email,codigo_transporte FROM cliente WHERE codigo='$rs[cliente_codigo]'";
	$QRc = mysql_query($SQLc);
	$rc = mysql_fetch_array($QRc);
	if(strlen($rc[codigo_transporte])>0){
		$transportes = explode(',',$rc[codigo_transporte]);
		foreach($transportes as $k=>$v){
			$wr_transporte .= "codigo='$v' OR ";
		} 
		$wr_transporte = "(". substr($wr_transporte,0,strlen($wr_transporte)-3).") AND";
		$SQLt = "SELECT codigo,nome_fantasia,obs,telefone,contato FROM cliente WHERE $wr_transporte ativo = '1' ORDER BY nome_fantasia";
		#echo $SQLt;
		$QRt = mysql_query($SQLt);
		while($tr = mysql_fetch_array($QRt)){ ?>
		<label>
		<li id="transporte_<?= $tr[codigo] ?>" title="<?= nl2br($tr[obs]) ?>">
		<input type="radio" name="transporte_codigo" value="<?= $tr[codigo] ?>" <? if($rs[transporte_codigo]==$tr[codigo]) echo checked ?> <? if($efetivado) echo DISABLED ?>>
		<?= $tr[nome_fantasia] ?><br />
		<?= $tr[telefone] ?> <?= $tr[contato] ?></small>
		</li>
		</label>
		<? }#while
	}#codigo_tranporte
	?>
	</div>
	<label>Dados Cadastrais do Sistema de Transporte</label>
	<?
	#$codigo_transporte
	$SQLt = "SELECT * FROM cliente WHERE codigo='$rs[transporte_codigo]'";
	$QRt = mysql_query($SQLt);
	$rst = mysql_fetch_array($QRt);
	?>
	<b><?= $rst[nome_fantasia] ?></b><br />
	Contato: <?= $rst[contato] ?><br />
	Email: <?= $rst[email] ?><br />
	Telefone:<?= $rst[telefone] ?><br />
	Descrição:<br />
	<?= nl2br($rst[obs]) ?><br />
	<br />
	<? if($rs[entrega_obs]){ ?>
		<small>
		<b>Instruções adicionais do fornecedor</b><br />
		<?= (nl2br($rs[entrega_obs])) ?><br />
		</small>
	<? } ?>
	</div>
	
	<div class="colunas_2">
	<label>Valor estimado para o frete</label>
	<small>(Consulte a empresa ou a tabela do serviço)</small>
	<input type="text" name="transporte_valor" id="transporte_valor_<?= $codigo ?>" style="margin-bottom: 0;" value="<?= $rs[transporte_valor] ?>" onkeyup="pedido_totalizacao_<?= $codigo ?>()" <? if($efetivado) echo READONLY ?>>
	
	<label><input type="radio" name="transporte_lancar" style="margin-bottom: 0;" value="1" <? if($rs[transporte_lancar]) echo checked ?> <? if($efetivado) echo DISABLED ?>>Frete Ã  pagar na chegada</label>
	&bull; Pagamento para o fornecedor do transporte na entrega ou via boleto.<br />
	&bull; Este valor entrar como um novo lançamento em Contas Ã  Pagar em nome do Fornecedor do Transporte, apÃ³s a efetivação do pedido. <br/>
	
	<label><input type="radio" name="transporte_lancar" style="margin-bottom: 0;" value="0" <? if(!$rs[transporte_lancar]) echo checked ?> <? if($efetivado) echo DISABLED ?>>Frete incluso no pedido.</label>
	&bull; Inclui o valor do frete na totalização deste pedido quando lançado em Contas Ã  Pagar.<br />
	&bull; Ã‰ usado em frete PAGO NA ORIGEM.<br/><br />
	
	<hr/><br/>
	Lembre-se, PARA QUALQUER UMA DAS OPÃ‡Ã•ES, será necessário que o mesmo seja substituido pelo valor correto na chegada do pedido, assim efetuando um lançamento correto dos valores em Contas Ã  Pagar.<br />
	Deixe o valor vazio para esta opção ser ignorada.<br /><br />
	</div>
	
	<? if(!$efetivado){ ?>
	<div class="imageright">
	<hr />
	<input type="image" src="images/button_ok.png" name="submit" class="image" title="Salvar os Dados" />
	</div>
	<? } ?>
	
</fieldset>

<fieldset class="fieldset_dinheiro">
<legend><img class="bt_voltar_<?= $codigo ?> bt_voltar" src="images/bt_voltar.png" align="absmiddle" title="Voltar para a o modo lista" /> 
Totalização do Pedido</legend>
	<div align="center">
	<h1>R$ <span id="pedido_total_<?= $codigo ?>"><?= $rs[valor_total]?></span></h1><br />
	</div>
</fieldset>

<? if($rs[transporte_codigo]){ ?>
<fieldset class="fieldset_infos">
<legend><img class="bt_voltar_<?= $codigo ?> bt_voltar" src="images/bt_voltar.png" align="absmiddle" title="Voltar para a o modo lista" /> 
Dados Gerenciais</legend>
	<div class="colunas_2">
		<label>Fornecedor</label>
		<img src="images/bt_ampliar.png" onclick="ampliar_cliente_<?= $codigo ?>('<?= $rc[codigo] ?>')" alt="ampliar" title="Mais detalhes" style="float:right; width: 16px" /> 
		<b><?= $rc[nome_fantasia] ?></b><br/>
		Razão Social: <?= $rc[razao_social] ?><br />
		CNPJ: <?= $rc[cnpj] ?><br />
		Telefone: <?= $rc[telefone] ?><br />
		
		<?
		$cliente_contato = $rs[cliente_contato] ? $rs[cliente_contato] : $rc[contato];
		$cliente_email = $rs[cliente_email] ? $rs[cliente_email] : $rc[email];
		?>
		<label>Contato no Fornecedor</label>
		<input type="text" name="cliente_contato" id="cliente_contato_<?= $codigo ?>" value="<?= $cliente_contato ?>" <? if($efetivado) echo READONLY ?>><br>
		<label>E-mail</label>
		<input type="text" name="cliente_email" id="cliente_email_<?= $codigo ?>" value="<?= $cliente_email ?>" <? if($efetivado) echo READONLY ?>><br>
	</div>

	<div class="colunas_2">
		<label>Data de criação do Pedido</label>
		<input type="text" name="data_criacao" id="data_criacao_<?= $codigo ?>" value="<?= mydate($rs[data_criacao]) ?>" <? if($efetivado) echo READONLY ?> onblur="if(this.value=='')this.value='<?= date(d.'/'.m.'/'.Y) ?>'">
		
		<label>Data de lançamento em Contas Ã  Pagar</label>
		<input type="text" name="data_lancamento" id="data_lancamento_<?= $codigo ?>" value="<?= mydate($rs[data_lancamento]) ?>" READONLY >		
		<? if($efetivado){ ?>
		<label>Pedido Efetivado</label>
		<img src="images/bt_atencao.png" title="Pedido Efetivado!" style="float:left"> 
		Este pedido encontra-se efetivado e não pode mais ser editado, pois o mesmo já consta como um lançamento em Contas Ã  Pagar.<br/><br/>
		Ã‰ possível porém, duplicá-lo e efetuar um novo pedido similar, podendo editá-lo da forma que necessitar antes de lançá-lo como um novo item em Contas Ã  Pagar, deseja fazer isto?<br /><br/>
		<input type="button" value="Duplicar Pedido">
		<? } ?>
			
	</div>

	<div class="clear_left"></div>
	<label>Informações adicionais do Pedido</label>
	<textarea rows="3" name="descricao" <? if($efetivado) echo READONLY ?>><?= ($rs[descricao]) ?></textarea>
	<? if(!$efetivado){ ?>
	<div class="imageright">
	<hr />
	<input type="image" src="images/button_ok.png" name="submit" class="image" title="Salvar os Dados" />
	</div>
	<? } ?>
</fieldset>

<fieldset class="fieldset_email">
<legend><img class="bt_voltar_<?= $codigo ?> bt_voltar" src="images/bt_voltar.png" align="absmiddle" title="Voltar para a o modo lista" /> 
Notificação e Impressão</legend>
	<label>Impressão/visualização do Pedido</label>
	<textarea rows="3" name="impressao" <? if($efetivado) echo READONLY ?>><?= ($rs[impressao]) ?></textarea>
	<? if(!$efetivado){ ?>
	<img src="images/bt_impressoras.png" id="bt_print_pedido_<?= $codigo ?>" title="Imprimir o Pedido" />
	<? } ?>
	
	<label>Notificação do Pedido para o e-mail do fornecedor</label>
	<textarea rows="3" name="email" <? if($efetivado) echo READONLY ?>><?= ($rs[email]) ?></textarea>
	<? if(!$efetivado){ ?>
	<img src="images/bt_email_send.png" id="bt_send_pedido_<?= $codigo ?>" title="Enviar o Email para o Fornecedor" />
	<? } ?>
	<div class="clear_left"></div>
	
	<? if(!$efetivado){ ?>
	<div class="imageright">
	<hr />
	<input type="image" src="images/button_ok.png" name="submit" class="image" title="Salvar os Dados" />
	</div>
	<? } ?>
	
</fieldset>

<fieldset class="fieldset_atencao">
<legend><img class="bt_voltar_<?= $codigo ?> bt_voltar" src="images/bt_voltar.png" align="absmiddle" title="Voltar para a o modo lista" /> 
Lançamento</legend>
	<img src="images/bt_contas_pagar.png" title="Lançar em Contas Ã  Pagar" onclick="lancar_pedido('<?= $rs[codigo] ?>')" /> Certifique-se de ter informado corretamente todos os dados antes do lançamento!
	<div class="clear_left"></div>
	<br />
</fieldset>

<? }#codigo_transporte ?>
<? }#cliente_codigo ?>

<br /><br />
</form>
