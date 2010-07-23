<? @session_start();
#require_once "../../conn.php";
if($codigo){
	$SQL = "SELECT * FROM producao_itens WHERE codigo='$codigo'";
	$qr = mysql_query($SQL);
	$rs = mysql_fetch_array($qr);
}?>
	
<div id="equipamento_os_editar_<?= $producao ?>">
<style type="text/css">
.produto_item, #bt_novo_produto{
	border:2px solid #999;
	padding: 5px;
	margin: 5px 0;
	color: #FFF;
	background: #555;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
}
#bt_novo_produto, .bt_novo_produto{
	width: 32px;
	background: #666;
	padding: 3px;
	margin-top: 0px;
}
.produto_results span, .produto_results_r span, .produto_results_f span, .produto_results_m span{
	font-size: 9px;
	color: #999;
}
.produto_results, .produto_results_r, .produto_results_m{
	width: 70px;
	height: 30px;
	display: block;
	float: left;
	border-right: 1px dotted #ccc;
	padding-left: 3px;
	margin-bottom: 5px;
}
.produto_results_m{
	width: 300px;
	height: auto;
}
.produto_results_r, .produto_results_m{
	border:0;
}
.produto_results_f{
	padding-left: 3px;
	margin-bottom: 5px;
}

.bt_controles{
	float: right;
	border:2px solid #999;
	padding: 5px;
	margin: 5px 2px 2px 0;
	color: #FFF;
	background: #444;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
}
.bt_controles img{
	height: 20px;
}
.colunas_5{
	width: 100px;
	float: left;
	margin-right: 10px;
}
</style>

<script language="javascript">

$('#codigo_produto_<?= $producao ?>').bind('change',function(){
	var produto = $('#codigo_produto_<?= $producao ?>').val();
	var equipamento_selecionado = $('#codigo_equipamento_selecionado_<?= $producao ?>').val();
	$('#equipamento_<?= $producao ?>').load("utf2iso.php?file=plugins/equipamento/equipamento_os_combo.php",{produto: produto, producao:'<?= $producao ?>', equipamento_selecionado: equipamento_selecionado});
});


$('#form_impressora_<?= $producao ?>').bind('submit',function(){
	var formContent = $("#form_impressora_<?= $producao ?>").serialize();
	//alert(formContent);
	var equipamento = $("#codigo_equipamento_<?= $producao ?>").val();
	if(!equipamento){
		alert('Informe um equipamento para continuar');
	}else{
		$.post("utf2iso.php?file=plugins/producao/producao_editar.php",formContent,function(){
		$('#equipamento_os_editar_<?= $producao ?>').load("utf2iso.php?file=plugins/equipamento/equipamento_os_editar.php",{producao: '<?= $producao ?>',  data_prevista: '<?= $data_prevista ?>', target: '<?= $target ?>'});
		});
	}
	return false;
	
});

$(".bt_voltar_<?= $target ?>").click(function(){
	$("#producao_os_<?= $target ?>").load("utf2iso.php?file=plugins/producao/producao_os.php",{codigo:'<?= $producao ?>', target: '<?= $target ?>'});
	$('#producao_os_<?= $target ?>').css({'overflow':'hidden', 'height': '130px'});
	$('#full_frame').hide();
	window.location.hash = '#dia_<?= $data_prevista ?>';
});

$('#bt_mais_produto_<?= $producao ?>').click(function(){
	$('#bt_mais_produto_<?= $producao ?>').fadeOut();
	$('#equipamento_os_editar_<?= $producao ?>').load("utf2iso.php?file=plugins/equipamento/equipamento_os_editar.php",{producao: '<?= $producao ?>', data_prevista: '<?= $data_prevista ?>', mais:'1', target: '<?= $target ?>'});
});

function excluir_item(codigo){
	if(confirm('Deseja mesmo excluir este Item?')){
	$.post("utf2iso.php?file=plugins/producao/producao_editar.php",{gravar: 'excluir_itens', codigo: codigo});
	$('#equipamento_os_editar_<?= $producao ?>').load("utf2iso.php?file=plugins/equipamento/equipamento_os_editar.php",{producao: '<?= $producao ?>', data_prevista: '<?= $data_prevista ?>', target: '<?= $target ?>'});
	}
	window.location.hash = '#equipamento_os_editar_<?= $producao ?>';
}
		
function editar_item(codigo){
	$('#equipamento_os_editar_<?= $producao ?>').load("utf2iso.php?file=plugins/equipamento/equipamento_os_editar.php",{producao: '<?= $producao ?>', codigo: codigo, data_prevista: '<?= $data_prevista ?>', target: '<?= $target ?>'});
	window.location.hash = '#equipamento_os_editar_<?= $producao ?>';
}

$(document).ready(function(){
	
	$('[title]').tooltip({ 
		track: false, 
		delay: 0, 
		showURL: false, 
		showBody: true, 
		fade: 30 
	});
	
	$('#form_impressora_<?= $producao ?>').hide();

	<? if($codigo){ ?>	
	var produto = $('#codigo_produto_<?= $producao ?>').val();
	var equipamento_selecionado = $('#codigo_equipamento_selecionado_<?= $producao ?>').val();
	$('#equipamento_<?= $producao ?>').load("utf2iso.php?file=plugins/equipamento/equipamento_os_combo.php",{produto: produto, producao:'<?= $producao ?>', equipamento_selecionado: equipamento_selecionado});
	$('#form_impressora_<?= $producao ?>').slideDown('slow');
	<? } ?>
	
	<? if($mais){ ?>
	window.location.hash = '#equipamento_os_editar_<?= $producao ?>';
	$('#form_impressora_<?= $producao ?>').slideDown('slow');
	<? } ?>
});

function calcula_metros(){
	var qt = $('#quantidade_<?= $producao ?>').val();
	var al = $('#altura_<?= $producao ?>').val();
	var la = $('#largura_<?= $producao ?>').val();
	var mu = $('#m2u_<?= $producao ?>').val();
	var mt = $('#m2t_<?= $producao ?>').val();
	
	$('#m2u_<?= $producao ?>').val((al*la).toFixed(3));
	mu = (al*la).toFixed(3);
	$('#m2t_<?= $producao ?>').val((mu*qt).toFixed(3));
}
</script>

	<fieldset class="fieldset_produtos">
	<legend><? if($producao){ ?><img class="bt_voltar_<?= $target ?>" src="images/bt_voltar.png" title="Retornar para o modo de fila" /><? } ?> Localização, Quantidades e Medidas dos Itens</legend>
	
	<form name="form_impressora" id="form_impressora_<?= $producao ?>" action="" method="post">	
	<?
	$gravar_value = "incluir_itens";
	if($codigo) $gravar_value = "editar_itens";
	?>
	<input type="hidden" name="gravar" id="gravar" value="<?= $gravar_value ?>" />
	<input type="hidden" name="atualiza" id="atualiza" value="1" />
	<input type="hidden" name="codigo" id="codigo" value="<?= $codigo ?>" />
	<input type="hidden" name="producao" value="<?= $producao ?>" />
	<input type="hidden" name="data_prevista" value="<?= $data_prevista ?>" />
	<input type="hidden" name="target" value="<?= $target ?>" />
	
	<label for="descricao">Localização e detalhes do Item</label>
	<textarea rows="1" name="descricao"><?= ($rs[descricao]) ?></textarea>
	<br />

	<div class="colunas_5">
	<label for="quantidade">Quantidade</label>
	<input type="text" class="text" name="quantidade" id="quantidade_<?= $producao ?>" value="<?= $rs[quantidade] ?>" onkeyup="calcula_metros()" />
	</div>

	<div class="colunas_5">
	<label for="Largura">Largura (0.00)</label>
	<input type="text" class="text" name="largura" id="largura_<?= $producao ?>" value="<? if(!$rs[largura]){ echo '1'; }else{ echo $rs[largura]; } ?>" onkeyup="calcula_metros()" />
	</div>

	<div class="colunas_5">
	<label for="altura">Altura (0.00)</label>
	<input type="text" class="text" name="altura" id="altura_<?= $producao ?>" value="<? if(!$rs[altura]){ echo '1'; }else{ echo $rs[altura]; } ?>" onkeyup="calcula_metros()" />
	</div>
	
	<div class="colunas_5">
	<label for="m2">MÂ² unitário</label>
	<input type="text" class="text" name="m2u" id="m2u_<?= $producao ?>" value="<? if(!$rs[m2u]){ echo '1'; }else{ echo $rs[m2u]; } ?>"  onkeyup="calcula_metros()" />
	</div>

	<div class="colunas_5">
	<label for="m2t">MÂ² totais</label>
	<input type="text" class="text" name="m2t" id="m2t_<?= $producao ?>" value="<? if(!$rs[m2t]){ echo '1'; }else{ echo $rs[m2t]; } ?>" onkeyup="calcula_metros()" />
	</div>

	<div class="clear_left"></div>
	
	<div class="colunas_2">
	<label>Produto</label>
	
	<select multiple size="10" name="codigo_produto" id="codigo_produto_<?= $producao ?>" class="historico" <? if(!plugin("ordem_de_servico")) echo disabled ?> > 
	<?
	$SQLpdt = mysql_query("SELECT * FROM produto WHERE ativo=1 ORDER BY titulo");
	while($pdt = mysql_fetch_array($SQLpdt)){
	?><option value="<?= $pdt[codigo] ?>" <? 
	if(isset($codigo_produto)){
		if($pdt[codigo]==$codigo_produto){
			echo "selected";
			$equipamentos = explode(',',$pdt[equipamento]);
			$descricao_produto = $pdt[descricao];
		}
	}else{
		if($pdt[codigo]==$rs[codigo_produto]){
			echo "selected";
			$equipamentos = explode(',',$pdt[equipamento]);
			$descricao_produto = $pdt[descricao];
		}
	} ?>>
	<?= ($pdt[titulo]) ?>
	</option><?
	}?>
	</select>
	<div id="descricao_produto_<?= $producao ?>"><?= $descricao ?></div>
	</div>
	
	<div class="colunas_2">
	<label>Equipamento</label>
	<div id="equipamento_<?= $producao ?>">Informe antes um produto ao lado.</div>
	<input type="hidden" id="codigo_equipamento_selecionado_<?= $producao ?>" value="<?= $rs[codigo_equipamento] ?>">
	<div id="descricao_equipamento_<?= $producao ?>"><?= $descricao ?></div>
	</div>
	
	<div class="imageright">
	<?
	$bt = "bt_mais.png";
	if($codigo) $bt = "button_ok.png";
	?>
	<input type="image" src="images/<?= $bt ?>" name="submit" class="image" title="Enviar os Dados" />
	</div>
	<hr />
	</form>
	
	
	
	
	
	
	
	
	
	<? 
	$SQL = "SELECT * FROM producao_itens WHERE codigo_producao='$producao'";
	#echo $SQL;
	$SQL = mysql_query($SQL);
	if(mysql_num_rows($SQL)>0 && !$mais){ ?>
	<img src="images/bt_mais.png" id="bt_mais_produto_<?= $producao ?>" class="image float_right" style="width: 32px; padding: 20px 10px 0 0;" title="Adicionar mais Itens" />
	<? }else{ ?>
	<script>$('#form_impressora_<?= $producao ?>').show();</script>
	<? } ?>
	<legend><img class="bt_voltar_<?= $target ?>" src="images/bt_voltar.png" title="Retornar para o modo de fila" /> Itens</legend>
	<br />
	<? if(mysql_num_rows($SQL)==0){ ?>
	<div class="produto_item">
	Nenhum item informado para produção.
	</div>
	<? }
	
	while($rs = mysql_fetch_array($SQL)){ 
	$border = "";
	if($rs[codigo]==$codigo) $border='border: 2px solid #FFCC00;';
	?>
	<div class="produto_item" style="<?= $border ?>">
	
		<div class="produto_results">
			<span>Quantidade</span><br />
			<?= $rs[quantidade] ?>&nbsp;
		</div>
	
		<div class="produto_results">
			<span>Largura</span><br/>
			<?= str_replace(".",",",$rs[largura]) ?>&nbsp;
		</div>

		<div class="produto_results">
			<span>Altura</span><br/>
			<?= str_replace(".",",",$rs[altura]) ?>&nbsp;
		</div>
	
		<div class="produto_results">
			<span>MÂ² unitário</span><br/>
			<?= $rs[m2u] ?>&nbsp;

		</div>
	
		<div class="produto_results_r">
			<span>MÂ² total</span><br/>
			<?= $rs[m2t] ?>&nbsp;
		</div>
		<hr />
		
		<div class="produto_results_m">
			<span>Produto</span><br/>
			<?
			$SQLpdt = mysql_query("SELECT titulo,descricao FROM produto WHERE codigo='$rs[codigo_produto]'");
			$pdt = mysql_fetch_array($SQLpdt);
			?>
			<?= $pdt[titulo] ?><br />
			<small><?= $pdt[descricao] ?></small><br/>
		</div>
		<div class="produto_results_m">
			<span>Equipamento</span><br/>
			<?
			$SQLe = mysql_query("SELECT titulo,descricao FROM equipamento WHERE codigo='$rs[codigo_equipamento]'");
			$eq = mysql_fetch_array($SQLe);
			?>
			<?= $eq[titulo] ?><br />
			<small><?= $eq[descricao] ?></small><br/>
		</div>
		<hr />
		
		<div class="bt_controles">		
			<img id="bt_editar_produto" src="images/bt_editar.png" alt="editar" title="Editar o produto" align="absmiddle" onclick="editar_item(<?= $rs[codigo] ?>)" /> &nbsp; 
			<img id="bt_delete_produto" src="images/bt_excluir.png" alt="excluir" title="Excluir o produto" align="absmiddle" onclick="excluir_item(<?= $rs[codigo] ?>)" />
		</div>
		
		<div class="produto_results_f">	
			<span>Localização e detalhes</span><br/>
			<?= nl2br($rs[descricao]) ?>&nbsp;<br/>
		</div>
		

		
		<div class="clear_right"></div>
		<div class="clear_left"></div>
	</div>
	<? }#while ?>
	<br />
	</fieldset>

<div>
