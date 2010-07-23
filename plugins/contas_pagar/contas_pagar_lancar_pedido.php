<? 
@session_start();
#require_once "../../conn.php";

#echo "codigo: $codigo_pedido ";
#echo "alvo: $alvo ";
#echo "cliente: $codigo_cliente ";
#echo "parcela: $codigo_parcela ";

$gravar = "incluir";
if($codigo_parcela) $gravar = "editar";

if($select=="monta_banco"){
	$wr = "WHERE aceita_saida='1'";
	$qr = mysql_query("SELECT * FROM banco $wr");
	#echo $wr;
	while($rs = mysql_fetch_array($qr)){
		$carteiras = explode(',',$rs[codigo_carteira]);	?>
		<!--<option value="0"><?= $codigo_banco ?></option>-->
		<? if(in_array($codigo_carteira,$carteiras)){ ?>
		<option value="<?= $rs[codigo] ?>" <? if($codigo_banco==$rs[codigo]) echo selected ?> ><?= $rs[codigo] ?> - <?= $rs[banco] ?></option>
		<? }
	}
	die;
}

$wr = "WHERE codigo_pedido='$codigo_pedido' AND codigo_cliente='$codigo_cliente'";
$qr = mysql_query("SELECT * FROM contas_pagar $wr");
$rs = mysql_fetch_array($qr);
$id = "$codigo_pedido$codigo_cliente";
#echo $wr;

if($codigo_parcela){ #editando uma parcela apos incluida em contas pagar
	$qp = mysql_query("SELECT * FROM contas_pagar WHERE codigo='$codigo_parcela'");
	$rp = mysql_fetch_array($qp);
	$rs_data_lancamento = mydate($rp[data_lancamento]);
	$rs_data_vencimento = mydate($rp[data_vencimento]);
	$rs_valor_acrescimo = $rp[valor_acrescimo];
	$rs_valor = $rp[valor];
	$rs_data_pagamento = mydate($rp[data_pagamento]);
	$rs_codigo_carteira = $rp[codigo_carteira];
	$rs_codigo_banco = $rp[codigo_banco];
	$rs_sequencia_carteira = $rp[sequencia_carteira];
	$rs_documento = $rp[documento];
	$rs_descricao = $rp[descricao];	
}elseif(!$rs[codigo]){ #se não houver nenhuma parcela na contas a pagar, é inicio do processo
	$qp = mysql_query("SELECT * FROM pedido WHERE codigo='$codigo_pedido'");
	$rp = mysql_fetch_array($qp);
	
	if($alvo=="lancamento_fornecedor"){
		$valor = $rp[valor_total];
		if($rp[transporte_lancar]) $valor = number_format(($rp[valor_total]-$rp[transporte_valor]),2,'.','');
	}else{
		$valor = $rp[transporte_valor];
	}
	
	$rs_data_vencimento = date("d/m/Y");
	$rs_valor_acrescimo = '0.00';
	$rs_valor = $valor;
	$rs_data_pagamento = '00/00/0000';
	$rs_codigo_carteira = '';
	$rs_codigo_banco = '';
	$rs_sequencia_carteira = '';
	$rs_documento = '';
	$rs_descricao = '';	
}

#saber se o pedido ja esta efetivado
$qpe = mysql_query("SELECT codigo FROM pedido WHERE codigo='$codigo_pedido' AND ativo=1");
$rpe = mysql_fetch_array($qpe);
if($rpe[codigo]){
	$efetivado = 1;
	$readonly = " READONLY ";
}
?>

<style type="text/css">
@import url("plugins/contas_pagar/contas_pagar.css?uid=<?= $uid ?>");
</style>

<script language="javascript">

$('#form_lancar_<?= $id ?>').bind('submit',function(){
	var formContent = $('#form_lancar_<?= $id ?>').serialize();
	$.post('utf2iso.php?file=plugins/contas_pagar/contas_pagar_editar.php',formContent,function(){
	//alert(data);
	$('#full_frame').load('utf2iso.php?file=plugins/pedido/pedido_lancar.php',{codigo:'<?= $codigo_pedido ?>'});
	});
	return false;
})

function excluir_parcela_<?= $id ?>(codigo_parcela){
	if(confirm('Lembre-se, ao excluir uma parcela, as outras restantes deverão ser reajustadas para novos valores, ou excluídas e geradas novamente para a adequação automática.\nDeseja excluir esta parcela?')){
	$.post('utf2iso.php?file=plugins/contas_pagar/contas_pagar_editar.php',{gravar:'excluir', codigo_parcela:codigo_parcela},function(){
	$('#full_frame').load('utf2iso.php?file=plugins/pedido/pedido_lancar.php',{codigo:'<?= $codigo_pedido ?>'});	
	});
	}
}

function monta_banco_<?= $id ?>(){
	alvo = 'codigo_banco_<?= $id ?>';
	codigo_carteira = $('#codigo_carteira_<?= $id ?>').val();
	codigo_banco = $('#codigo_banco').val()
	$('#'+alvo).load('utf2iso.php?file=plugins/contas_pagar/contas_pagar_lancar_pedido.php',{select:'monta_banco',codigo_banco:codigo_banco,codigo_carteira:codigo_carteira});
}

function calcula_total(id){
	//alert(id);
	var acrescimo = $('#valor_acrescimo_'+id).val();
	var valor = $('#valor_'+id).val();
	var resultado = parseFloat(valor)+parseFloat(acrescimo);
	$('#valor_parcelado'+id).val(resultado.toFixed(2));
	calcula_parcela(id);
}

<? if(!$codigo_parcela){ ?>
function calcula_parcela(id){
	//alert(id);
	var parcela = $('#parcela_'+id).val();
	var acrescimo = $('#valor_acrescimo_'+id).val();
	var valor = $('#valor_'+id).val();
	var resultado = (parseFloat(valor)+parseFloat(acrescimo))/parcela;
	$('#valor_parcelado_'+id).val(resultado.toFixed(2));
}
<? } ?>

<? if(!$efetivado) { ?>
$('#data_vencimento_<?= $id ?>').click(function(){
	$(this).calendario({
		target:'#data_vencimento_<?= $id ?>'
	});
});
<? } ?>

$('#data_pagamento_<?= $id ?>').click(function(){
	$(this).calendario({
		target:'#data_pagamento_<?= $id ?>'
	});
});

$('[title]').tooltip({ 
track: false, 
delay: 0, 
showURL: false, 
showBody: true, 
fade: 30 
});

</script>


<? if($codigo_parcela || !$rs[codigo]){ ?>
<hr />
<form action="" name="form_lancar" id="form_lancar_<?= $id ?>">
<input type="hidden" name="codigo_pedido" value="<?= $codigo_pedido ?>"/>
<input type="hidden" name="codigo_cliente" value="<?= $codigo_cliente ?>"/>
<input type="hidden" name="codigo_parcela" value="<?= $codigo_parcela ?>"/>
<input type="hidden" name="codigo_banco" id="codigo_banco" value="<?= $rs_codigo_banco ?>"/>
<input type="hidden" name="gravar" value="<?= $gravar ?>"/>

<div class="contas_pagar_coluna_5">
<label>Vencimento</label>
<input type="text" class="text" name="data_vencimento" id="data_vencimento_<?= $id ?>" value="<?= $rs_data_vencimento ?>" <?= $readonly ?>/>
</div>

<div class="contas_pagar_coluna_5">
<label>Acréscimo</label>
<input type="text" class="text" name="valor_acrescimo" id="valor_acrescimo_<?= $id ?>" value="<?= $rs_valor_acrescimo ?>" onkeyup="calcula_total(<?= $id ?>); if(!this.value) this.value=0.00"/>
</div>

<div class="contas_pagar_coluna_5">
<label>Valor</label>
<input type="text" class="text" name="valor" id="valor_<?= $id ?>" value="<?= $rs_valor ?>" onkeyup="calcula_total(<?= $id ?>); if(!this.value) this.value=0.00" <?= $readonly ?> />
</div>

<div class="contas_pagar_coluna_5">
<label>Pagamento</label>
<input type="text" class="text" name="data_pagamento" id="data_pagamento_<?= $id ?>" value="<?= $rs_data_pagamento ?>" />
</div>

<div class="contas_pagar_coluna_5">
<label>Carteira</label>
<select class="full" name="codigo_carteira" id="codigo_carteira_<?= $id ?>" onchange="monta_banco_<?= $id ?>()">
<?
$qrc = mysql_query("SELECT * FROM carteira WHERE ativo='1' ORDER BY titulo ASC");
while($rc = mysql_fetch_array($qrc)){ ?>
<option value="<?= $rc[codigo] ?>" <? if($rs_codigo_carteira==$rc[codigo]) echo selected ?>><?= $rc[codigo] ?> - <?= $rc[titulo] ?></option>
<? } ?>
</select>
</div>

<div class="contas_pagar_coluna_5">
<label>Banco</label>
<select name="codigo_banco" id="codigo_banco_<?= $id ?>">
</select>
</div>
<script>monta_banco_<?= $id ?>()</script>

<div class="contas_pagar_coluna_3">
<label>Sequência da carteira</label>
<input type="text" class="full" name="sequencia_carteira" id="sequencia_<?= $id ?>" value="<?= $rs_sequencia_carteira ?>" />
<small>(linha digitada do boleto, recibo...)</small>							 
</div>

<div class="contas_pagar_coluna_3">
<label>Documento</label>
<input type="text" class="full" name="documento" id="documento_<?= $id ?>" value="<?= $rs_documento ?>" />
<small>(Numero/Serie Nota Fiscal)</small>
</div>

<? if(!$codigo_parcela){ ?>
<div class="contas_pagar_coluna_5" style="width:120px;">
<label>Parcelas</label>
<input type="text" style="width: 20px;" class="text" name="parcela" id="parcela_<?= $id ?>" value="1"  onkeyup="calcula_parcela(<?= $id ?>); if(!this.value) this.value=1" /> R$ 
<input type="text" style="width: 50px;" class="text" name="valor_parcelado" id="valor_parcelado_<?= $id ?>" value="<?= $rs_valor ?>"  onkeyup="calcula_parcela(<?= $id ?>)"/> 
</div>

<div class="contas_pagar_coluna_5">
<label>Período</label>
<input type="text" style="width: 20px;" class="text" name="periodo" id="periodo_<?= $id ?>" value="0" /> Dias
</div>
<div class="clear_left"></div>
<? } ?>

<div class="contas_pagar_coluna_3">
<label>Descrição </label>
<input type="text" class="full" name="descricao" id="descricao_<?= $id ?>" value="<?= $rs_descricao ?>" />
<small>Breve (opcional)</small>
</div>

<div style="float:right; margin-right: 0px; margin-top: 28px; width:50px">
	<? if(!$codigo_parcela){ ?>
	<input type="image" style="width:24px" src="images/bt_mais.png" title="Adiciona ou divide o vencimento, baseado no nÃºmero de parcelas e períodos informados" /> 
	<? }else{ ?>
	<input type="image" style="width:24px" src="images/bt_check.png" title="Atualizar" align="top" /> 
	<? } ?>
</div>
</form>
<div class="clear_left"></div>
<br /><br />
<? }#codigo_parcela ?>

<?
$wr = "WHERE codigo_pedido='$codigo_pedido' AND codigo_cliente='$codigo_cliente'";
$qr2 = mysql_query("SELECT * FROM contas_pagar $wr ORDER BY data_vencimento, codigo");
while($rs = mysql_fetch_array($qr2)){ ?>

<div class="contas_pagar_item" id="contas_pagar_item_<?= $id ?>" <? if($codigo_parcela==$rs[codigo]) echo "style='border-color: #FFCC00;'" ?>>

	<div class="contas_pagar_results">
		<span>Lancamento</span><br />
		<?= mydate($rs[data_lancamento]) ?>&nbsp;
	</div>
	
	<div class="contas_pagar_results">
		<span>Vencimento</span><br />
		<?= mydate($rs[data_vencimento]) ?>&nbsp;
	</div>

	<div class="contas_pagar_results">
		<span>Valor</span><br/>
		<?= $rs[valor] ?>&nbsp;
	</div>
	
	<div class="contas_pagar_results">
		<span>Valor Acréscimo</span><br/>
		<?= $rs[valor_acrescimo] ?>&nbsp;
	</div>
	
	<div class="contas_pagar_results">
		<span>Valor total</span><br/>
		<?= number_format(($rs[valor]+$rs[valor_acrescimo]),2,'.','') ?>&nbsp;
	</div>
	
	<div class="contas_pagar_results">
		<span>Pagamento</span><br/>
		<?= mydate($rs[data_pagamento]) ?>&nbsp;
	</div>	

	<?
	$qrc = mysql_query("SELECT titulo FROM carteira WHERE codigo='$rs[codigo_carteira]'");
	$rc = mysql_fetch_array($qrc)?>
	<div class="contas_pagar_results" style="width:125px;">
		<span>Carteira</span><br />
		<?= $rs[codigo_carteira]?> - <?= $rc[titulo] ?>&nbsp;
	</div>
	
	<?
	$qrb = mysql_query("SELECT banco FROM banco WHERE codigo='$rs[codigo_banco]'");
	$rb = mysql_fetch_array($qrb);
	?>
	<div class="contas_pagar_results_r" style="width:125px;">
		<span>Banco</span><br />
		<?= $rs[codigo_banco] ?> - <?= $rb[banco] ?>&nbsp;
	</div>
	
	<hr />
	<div class="contas_pagar_results" style="width: 181px;">
		<span>Sequencia</span><br />
		<?= $rs[sequencia_carteira] ?>&nbsp;
	</div>
			
	<div class="contas_pagar_results"  style="width: 181px;">
		<span>Documento</span><br />
		<?= $rs[documento] ?>&nbsp;
	</div>
	
	<div class="contas_pagar_results_r"  style="width: 260px;">
		<span>Descrição</span><br />
		<?= $rs[descricao] ?>&nbsp;
	</div>
	
	
	<div class="bt_controles_item">		
		<img src="images/bt_editar.png" alt="editar" title="Editar a parcela" align="absmiddle" onclick="parcelas('<?= $alvo ?>',<?= $rs[codigo_cliente] ?>,<?= $rs[codigo] ?>);" />
		<?
		$qrp = mysql_query("SELECT codigo FROM pedido WHERE codigo='$codigo_pedido' AND ativo='1'");
		$rp = mysql_fetch_array($qrp);
		if(!$rp[codigo]){ ?> &nbsp;
		<img src="images/bt_excluir.png" alt="excluir" title="Excluir o item do pedido" align="absmiddle" onclick="excluir_parcela_<?= $id ?>(<?= $rs[codigo] ?>)" />
		<? } ?>
	</div>
	
	<div class="clear_right"></div>
	<div class="clear_left"></div>
</div>
<? }#while ?>