<? 
session_start();
#require_once "../../conn.php";
?>

<table class="days_top">
<tr>
<td class="days_top_1"></td>
<td class="days_top_2">Transportes</td>
<td class="days_top_3"></td>
<td class="days_top_4">&nbsp;</td>
<td class="days_top_5"></td>
</tr>
</table>

<div id="days_middle" class="days_middle">


<fieldset class="fieldset_transporte">
<legend>Relatório de Entregas</legend>
<form action="" method="post" id="form_entrega">
	<label>Data</label>
	<input type="text" id="data_inicial" name="data_inicial" class="caixa" value="<?= date("d/m/Y") ?>" onBlur="if(this.value=='') this.value='<?= date("d/m/Y") ?>'">
	<div class="clear_left"></div>
	<hr />
	<input type="image" src="images/button_ok.png" style="float:right" title="Gerar" />
</form>
</fieldset>


</div>

<div id="days_stats_top_<?= $data_prevista ?>" class="days_stats_top"></div>

<div id="days_stats_middle_<?= $data_prevista ?>" class="days_stats_middle"></div>

<div id="days_stats_bottom_<?= $data_prevista ?>" class="days_stats_bottom"></div>


<script type="text/javascript">
$('#form_entrega').submit(function(){
	var data_inicial = $('#data_inicial').val();
	win = open('plugins/transporte/entrega_relatorio.php?data_inicial='+data_inicial,"entrega");
	return false;
});

//titulos
$('[title]').tooltip({ 
	track: false, 
	delay: 0, 
	showURL: false, 
	showBody: true, 
	fade: 30 
});
</script>
