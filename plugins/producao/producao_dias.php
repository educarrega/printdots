<? @session_start();
#require_once "../../conn.php";
?>
<script language="JavaScript" type="text/javascript">
$('[title]').tooltip({ 
	track: false, 
	delay: 0, 
	showURL: false, 
	showBody: true, 
	fade: 30 
});

$("#days_date_<?= $data_prevista ?>").click(function() {
	$('#days_middle_<?= $data_prevista ?>').slideToggle(200);
	$('#days_middle_carregando_<?= $data_prevista ?>').hide();
	$('#days_stats_top_<?= $data_prevista ?>').slideToggle(400);
	$('#days_stats_middle_<?= $data_prevista ?>').slideToggle(600);
//	$('#days_group_by_<?= $data_prevista ?>').slideToggle(800);
	if($('#days_group_by_<?= $data_prevista ?>').is(':visible')){
		$('#days_group_by_<?= $data_prevista ?>').fadeOut('normal');
	}else{
		$('#days_group_by_<?= $data_prevista ?>').fadeIn('normal');
	}
});

//filtros
$("#group_nenhum_<?= $data_prevista ?>").click(function() {
	$('#days_middle_<?= $data_prevista ?>').load("utf2iso.php?file=plugins/producao/producao_fila.php",{data_prevista: '<?= $data_prevista ?>', info_aberto: 'cliente'});
});
$("#group_equipamento_<?= $data_prevista ?>").click(function() {
	$('#days_middle_<?= $data_prevista ?>').load("utf2iso.php?file=plugins/producao/producao_fila.php",{filtro:'Equipamento', order:'digital_plotter', tabela: 'equipamento', campo: 'titulo', data_prevista: '<?= $data_prevista ?>', info_aberto: 'impressora'});
});
$("#group_entrega_<?= $data_prevista ?>").click(function() {
	$('#days_middle_<?= $data_prevista ?>').load("utf2iso.php?file=plugins/producao/producao_fila.php",{filtro:'Entrega', order:'cod_transporte', tabela: 'cliente', campo: 'nome_fantasia', data_prevista: '<?= $data_prevista ?>', info_aberto: 'transporte'});
});
$("#group_status_<?= $data_prevista ?>").click(function() {
	$('#days_middle_<?= $data_prevista ?>').load("utf2iso.php?file=plugins/producao/producao_fila.php",{filtro:'Status', order:'status', tabela: 'producao_status', campo: 'titulo', data_prevista: '<?= $data_prevista ?>', info_aberto: 'arquivos'});
});
$("#group_produto_<?= $data_prevista ?>").click(function() {
	$('#days_middle_<?= $data_prevista ?>').load("utf2iso.php?file=plugins/producao/producao_fila.php",{filtro:'Produto', order:'codigo_produto', tabela: 'produto', campo: 'titulo', data_prevista: '<?= $data_prevista ?>', info_aberto: 'impressora'});
});

$(document).ready(function(){
	//$('#days_middle_<?= $data_prevista ?>').hide();
	// carregamento das ordens de servico nos dias dentro de cada div days_middle_data
	var t = setTimeout( function() { 
		$('#days_middle_<?= $data_prevista ?>').load("utf2iso.php?file=plugins/producao/producao_fila.php",{data_prevista: '<?= $data_prevista ?>', codigo: '<?= $codigo ?>', agencia: '<?= $agencia ?>', digital_plotter: '<?= $digital_plotter ?>', filtro:'<?= $filtro ?>', order:'<?= $order ?>', tabela:'<?= $tabela ?>', campo:'<?= $campo ?>', info_aberto:'<?= $info_aberto ?>', incluir_os: '<?= $incluir_os ?>', days:'1'}, function(){
		$('#days_middle_<?= $data_prevista ?>').slideDown();
		});
	}, <?= $time_out ?> );
});

</script>

<?
$d = explode("-",$data_prevista);
$timestamp = mktime(0,0,0,$d[1],$d[2],$d[0]);
$date = getdate($timestamp);
$dayofweek = $date[wday];
?>
<table class="days_top" id="days_top_<?= $data_prevista ?>">
	<tr>
	<td class="days_top_1"></td>
	<td class="days_top_2 days_top_click" id="days_date_<?= $data_prevista ?>" title="Exibir/Ocultar dia">
	<? if(date("Y-m-d")=="$data_prevista"){ ?><a name="hoje"></a>Hoje<? }else{ ?><?= ($semana[$dayofweek]) ?><? } ?> | <?= substr(mydate($data_prevista),0,5) ?>
	</td>
	<td class="days_top_3"></td>
	<td class="days_top_4">
	<? if(!$codigo){ ?>
	<span id="days_group_by_<?= $data_prevista ?>" class="days_group_by">
	Organizar: 
	<a id="group_nenhum_<?= $data_prevista ?>" href="#Nenhum">Nenhum</a> | 
	<a id="group_equipamento_<?= $data_prevista ?>" href="#Equipamento">Equipamento</a> | 
	<a id="group_entrega_<?= $data_prevista ?>" href="#Entrega">Sistema de entrega</a> | 
	<a id="group_status_<?= $data_prevista ?>" href="#Status">Status</a> | 
	<!--<a id="group_produto_<?= $data_prevista ?>" href="#Produto">Produto</a> -->
	</span>
	<?= $_ascdesc ?>
	<? } ?>
	</td>
	<td class="days_top_5"></td>
	</tr>
</table>

<div id="days_middle_<?= $data_prevista ?>" class="days_middle">
<div align="center"><img src='images/loader.gif' class='loader'></div>
</div>

<div id="days_stats_top_<?= $data_prevista ?>" class="days_stats_top"></div>

<div id="days_stats_middle_<?= $data_prevista ?>" class="days_stats_middle"></div>

<div id="days_stats_bottom_<?= $data_prevista ?>" class="days_stats_bottom"></div>
