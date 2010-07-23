<? 
session_start();
#require_once "../../conn.php";
?>

<script language="javascript">

$('#data_inicial').click(function(){
	$(this).calendario({
		target:'#data_inicial'
	});
});

$('#data_final').click(function(){
	$(this).calendario({
		target:'#data_final'
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


<table class="days_top">
<tr>
<td class="days_top_1"></td>
<td class="days_top_2">Contas Ã  Receber</td>
<td class="days_top_3"></td>
<td class="days_top_4">&nbsp;</td>
<td class="days_top_5"></td>
</tr>
</table>

<div id="days_middle">




<fieldset>
<legend>RelatÃ³rio Avançado</legend>
Informe as características que deseja obter no relatÃ³rio de contas Ã  receber.
<br /><br />
<hr /><br />
<input onclick="contas_receber_escolha()" style="position: absolute; top: 127px; right: 50px; width: 32px;" type="image" src="images/bt_relatorios.png" value="OK" title="Alternar Modo de Lista / RelatÃ³rio">

	
	<form name="print_avancado" target="receber_print_<?= substr($uid,0,10) ?>" method="POST" action="plugins/contas_receber/contas_receber_relatorio_print.php">
	
	<fieldset>
	<legend>Clientes</legend><br />
	<select name="codigo_cliente">
	<option value="" selected>Todos</option>
	<?
	$qr = mysql_query("SELECT codigo,nome_fantasia,cidade,estado,ativo FROM cliente ORDER BY ativo DESC, nome_fantasia ASC");
	while($rsc = mysql_fetch_array($qr)){
		if(!$rsc[ativo] && !$in){
			echo "<optgroup label='Inativos'>";
			$in = 1;
		}
		echo "<option value='$rsc[codigo]'>".strtoupper(acento($rsc[nome_fantasia]))." | $rsc[cidade] $rsc[estado]</option>";
	}
	?>
	</optgroup>
	</select>
	<input type="image" src="images/button_ok.png" style="float:right; width:24px;" class="image" title="Gerar" />
	</fieldset>
	
	<fieldset>
	<legend>Busca</legend>
	<input type="image" src="images/button_ok.png" style="float:right; width:24px;" class="image" title="Gerar" />
	<label><input type="text" name="busca"/> Referência no vencimento (Boleto, Título do serviço, outros...)</label>
	</fieldset>
	
	<fieldset>
	<legend>Período</legend>
	<input type="image" src="images/button_ok.png" style="float:right; width:24px;" class="image" title="Gerar" />
	<label class="colunas_3">Data Inicial <input type="text" name="data_inicial" id="data_inicial" value="00/00/0000" onfocus="if(this.value=='00/00/0000')this.value=''" onblur="if(this.value=='')this.value='00/00/0000'" /></label> 
	<label class="colunas_3">Data Final <input type="text" name="data_final" id="data_final" value="00/00/0000" onfocus="if(this.value=='00/00/0000')this.value=''" onblur="if(this.value=='')this.value='00/00/0000'" /></label>
	<label class="colunas_3">Referência da data<br />
	<select name="tipo_data">
		<option value="data_vencimento">Vencimento</option>
		<option value="data_pagamento">Pagamento</option>
	</select>
	</label>
	</fieldset>

	<fieldset>
	<legend>Vencimentos</legend>
	<input type="image" src="images/button_ok.png" style="float:right; width:24px;" class="image" title="Gerar" />
	<label class="colunas_4"><input type="radio" name="venc" value="aberto" checked="checked" />Abertos</label>
	<label class="colunas_4"><input type="radio" name="venc" value="pagos" />Pagos</label> 
	<label class="colunas_4"><input type="radio" name="venc" value="*" />Todos</label>
	</fieldset>
	
	<fieldset>
	<legend>Carteira</legend>
	<input type="image" src="images/button_ok.png" style="float:right; width:24px;" class="image" title="Gerar" />
	
	<ul id="carteira_radio">
	<label><li><input type="radio" name="fat" value="*" checked>Todos</li></label>
	<? $qrc = mysql_query("SELECT codigo,titulo FROM carteira ORDER BY titulo");
	while($rc = mysql_fetch_array($qrc)){ ?>
	<label><li><input type="radio" name="fat" value="<?= $rc[codigo]?>"><?= $rc[codigo]?> - <?= $rc[titulo] ?></li></label>
	<? } ?>
	</ul>
	</fieldset>
	
	<!--	
	<fieldset>
	<legend>ComissÃµes</legend>
	<input type="image" src="images/button_ok.png" style="float:right; width:24px;" class="image" title="Gerar" />
	<label class="colunas_4"><input type="radio" name="bv" value="aberto" />Aberto</label>
	<label class="colunas_4"><input type="radio" name="bv" value="pagos" />Pagas</label>
	<label class="colunas_4"><input type="radio" name="bv" value="*" checked="checked" />Nenhum</label>
	</fieldset>
	-->
	
	<fieldset>
	<legend>Ordenação</legend>
	<label class="colunas_4"><input type="radio" name="ord" value="data_vencimento" checked="checked" />Data</label>
	<label class="colunas_4"><input type="radio" name="ord" value="codigo_cliente" />Cliente</label>
	<label class="colunas_4"><input type="radio" name="ord" value="valor" />Valor</label>
	<label class="colunas_4"><input type="radio" name="ord" value="bv_valor" />ComissÃµes</label>
	<input type="image" src="images/button_ok.png" style="float:right; width:24px;" class="image" title="Gerar" />
	</fieldset>
	
	</form>

</fieldset>



</div>

<div id="days_stats_top_<?= $data_prevista ?>" class="days_stats_top"></div>

<div id="days_stats_middle_<?= $data_prevista ?>" class="days_stats_middle"></div>

<div id="days_stats_bottom_<?= $data_prevista ?>" class="days_stats_bottom"></div>

<script language="javascript" src="../../scripts.js" type="text/javascript"></script>
<script language="javascript" src="../../jquery.js" type="text/javascript"></script>
<script language="javascript" src="../../jquery.pngFix.js" type="text/javascript"></script>
<script language="javascript" src="../../jquery.tooltip.js" type="text/javascript"></script>

<script type="text/javascript">
	
	//titulos
	$('[title]').tooltip({ 
		track: false, 
		delay: 0, 
		showURL: false, 
		showBody: true, 
		fade: 30 
	});
</script>
