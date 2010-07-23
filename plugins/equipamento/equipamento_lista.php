<? 
session_start();
#require_once "../../conn.php";
?>

<? if(!$codigo){
	## RELATORIO INICIAL ##	
	$qr = mysql_query("SELECT Count(ativo) as ativos FROM equipamento WHERE ativo=1");
	$at = mysql_fetch_object($qr);
	$ativos = $at->ativos;

	$qr = mysql_query("SELECT Count(ativo) as inativos FROM equipamento WHERE ativo<>1");
	$at = mysql_fetch_object($qr);
	$inativos = $at->inativos;
	?>

	<table class="days_top">
	<tr>
	<td class="days_top_1"></td>
	<td class="days_top_2 days_top_click">Equipamentos</td>
	<td class="days_top_3"></td>
	<td class="days_top_4">&nbsp;</td>
	<td class="days_top_5"></td>
	</tr>
	</table>

	<div id="days_middle" class="days_middle suggestionList">
	
	<input id="bt_add_equipamento" type="image" src="images/bt_mais.png" title="Incluir novo Equipamento" />
	<div id="add_equipamento">Adicionar Equipamento aqui</div>

	<?
	$indice = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,X,Z,W,Y,0,1,2,3,4,5,6,7,8,9,0,-,+,.,?,!,@,#,$,&,*,(,),[,],{,},|,/,<,>";
	$indice = explode(",",$indice);
	?>
	<fieldset class="fieldset_infos">
	<legend>Índice de Equipamentos por Letras e Símbolos</legend>
	<div id="indices">
	<?
	foreach($indice as $k=>$v){
		$t = 0;
		$qr = mysql_query("SELECT Count(codigo) as contagem FROM equipamento WHERE titulo LIKE '$v%'");
		$tt = mysql_fetch_object($qr);
		$t = $tt->contagem;
		if($t>0){
		?><a href="#<?= $v ?>" onclick="filter('<?= $v ?>')" onmouseover="busca_status('<?= $t ?> itens em <?= $v ?>')"><?= $v ?></a><?
		}
	}
	?>
	</div>
	<div class="clear_left">&nbsp;</div>
	</fieldset>

	<hr />
<? } ?>

<? 

if($busca){
	$busca = strtoupper(acento(urldecode($busca)));
	$campos = 'titulo,codigo,descricao,mph,ppm,ativo';
	$campos = explode(',',$campos);
	$wr = "WHERE (";
	for($i=0;$i<count($campos);$i++){
		$wr .= "UPPER(". $campos[$i].") LIKE '%". $busca ."%' OR ";
	}
	$wr = substr($wr,0,strlen($wr)-4).") ORDER BY titulo";
	$filter = $busca;
}elseif(!$codigo){
	if(!$order) $order = "titulo";
	if(($ativos+$inativos) >20 && !isset($filter)) $filter = "A";
	if(isset($filter)) $filter_qr = "WHERE UPPER(titulo) LIKE '". $filter ."%'";
	if(!isset($filter)) $filter_qr = "";
	$wr = "$filter_qr ORDER BY ativo DESC,$order ASC";
}else{
	$wr = "WHERE codigo='$codigo'";
}

$SQL = "SELECT * FROM equipamento $wr";
$QR = mysql_query($SQL);
if(!$codigo) $filtrados = mysql_num_rows($QR);
while($rs = mysql_fetch_array($QR)){ 

	if($busca){
		$resultados++;
	}else{
		$resultados = $rs[codigo];
	}
?>
	
<? if(!$codigo){
	echo "<div class='clear_left'></div>";
	
	if($alter==0){
		$alter = 1;
		$bck = " class='alter'";
	}else{
		$alter = 0;
		$bck = "";
	} 	
	?>
	<li<?= $bck ?> id="equipamento_<?= $rs[codigo] ?>" onmouseover="controles(<?= $rs[codigo] ?>)">
<? } 

	?>
	<script language="javascript">
	$('[title]').tooltip({ 
	track: false, 
	delay: 0, 
	showURL: false, 
	showBody: true, 
	fade: 30 
	});
	</script>
	
	
	<div id="bt_controles_<?= $rs[codigo] ?>" class="bt_controles">
	<img src="images/bt_ampliar.png" onclick="ampliar_equipamento('<?= $rs[codigo] ?>')" alt="ampliar" title="Mais detalhes deste Equipamento" /> 
	<img src="images/bt_producao.png" onclick="os_equipamento('<?= $rs[codigo] ?>')" alt="os" title="Ver as Ordens de Serviço deste Equipamento" /> 
	<img src="images/bt_editar.png" onclick="editar_equipamento('<?= $rs[codigo] ?>')" alt="editar" title="Editar este Equipamento" /> 
	<img src="images/bt_excluir.png" onclick="excluir_equipamento('<?= $rs[codigo] ?>')"  alt="excluir" title="Excluir este Equipamento" />
	</div>
	
	<div class="equipamento_coluna_0">
		<div class="producao_busca_indice"><?= $resultados ?></div>
		<?
		echo "<span id='equipamento_status_". $rs[codigo] ."'>";
		if($rs[ativo]=="1"){
		echo "<img src='images/bt_check.png' class='equipamento_status' title='Equipamento Ativo' onclick='equipamento_status(". $rs[codigo] .",0)' >";
		}else{
		echo "<img src='images/bt_atencao.png' class='equipamento_status' title='Equipamento Inativo' onclick='equipamento_status(". $rs[codigo] .",1)' >";
		} 
		echo "</span>"; ?>
	</div>
	
	<div class="equipamento_coluna_1">
		<div class="equipamento_titulo"><?= $rs[titulo] ?>&nbsp;</div>
		<div class="equipamento_descricao">
		<?= $rs[descricao] ?>&nbsp;<br />
		</div>
	</div>
	
	<div class="equipamento_coluna_2">
	MÂ²/h: <?= $rs[mph] ?><br />
	</div>
			
	<div class="clear_left"></div>
	
	
	
	
	
<? if(!$codigo){ ?>
	</li>
	<hr />
<? } ?>
	
<? } # while ?>

<? if(!$codigo){ ?>
	</div>

	<div id="days_stats_top_<?= $data_prevista ?>" class="days_stats_top"></div>

	<div id="days_stats_middle_<?= $data_prevista ?>" class="days_stats_middle"></div>

	<div id="days_stats_bottom_<?= $data_prevista ?>" class="days_stats_bottom"></div>


	<?
	$tx_filter = "$filtrados Equipamentos encontrados em \"$filter\"";
	if($filtrados=='1') $tx_filter= "$filtrados Equipamento encontrado em \"$filter\"";
	if(!$filtrados) $tx_filter = "<img src=\"images/bt_atencao.png\" align=\"absmiddle\" width=\"24\"> Nenhum Equipamento encontrado em \"$filter\"";
	?>

	<script type="text/javascript">
	busca_status('<?= $tx_filter ?>. Total de <?= $ativos ?> ativos e <?= $inativos ?> inativos.');

	function filter(filter){
		$('#equipamento_todas').load('utf2iso.php?file=plugins/equipamento/equipamento_lista.php',{filter: filter});
	}

	function editar_equipamento(codigo){
		$('#equipamento_'+ codigo).load('utf2iso.php?file=plugins/equipamento/equipamento_editar.php',{codigo: codigo},function(){
			$('#equipamento_'+ codigo).slideDown('slow');
			$('#equipamento_'+ codigo).css({'overflow':'none', 'height': 'auto'});
		});
	}
	<? if($editar){ ?>
	editar_equipamento(<?= $editar ?>);
	window.location.hash = '#equipamento_<?= $editar ?>';
	$("input[name='titulo']:first", document.forms[0]).focus();
	<? } ?>
	
	function equipamento_status(codigo,status){
		$.post('utf2iso.php?file=plugins/equipamento/equipamento_editar.php',{codigo: codigo, gravar: 'status', status: status});
		$('#equipamento_'+ codigo).load('utf2iso.php?file=plugins/equipamento/equipamento_lista.php',{codigo: codigo});
	}
	
	function ampliar_equipamento(codigo){
		$("#full_frame").load('utf2iso.php?file=plugins/equipamento/equipamento_os.php',{equipamento: codigo});
		$("#full_frame").slideDown(function(){
			$("#full_background").fadeIn(500);
		});
	}
	
	function excluir_equipamento(codigo){
		if(confirm('Deseja mesmo excluir este Equipamento?\nEsta ação é irreversível!')){
		$.post('utf2iso.php?file=plugins/equipamento/equipamento_editar.php',{codigo: codigo, gravar: 'excluir'},function(){
			$('#equipamento_status_<?= $rs[codigo] ?>').hide();
			$('#equipamento_'+ codigo).slideUp('slow');
			busca_status('O Equipamento foi excluído.');
		});
		}
	}
	
	function os_equipamento(codigo){
		$("#content_right").load('utf2iso.php?file=plugins/producao/producao.php',{equipamento: codigo, filtro:'Equipamento', order:'digital_plotter', tabela: 'equipamento', campo: 'titulo', data_prevista: '<?= $data_prevista ?>', info_aberto: 'impressora'});
	}

	$('#bt_add_equipamento').click(function(){
		$('#add_equipamento').load('utf2iso.php?file=plugins/equipamento/equipamento_editar.php',function(){
			$('#bt_add_equipamento').fadeOut();
			$('#add_equipamento').slideDown('slow');
		});	
	});
	
	var last_codigo = 0;
	function controles(codigo){
		$('#bt_controles_'+last_codigo).hide();
		last_codigo = codigo;
		$('#bt_controles_'+codigo).show();
	}

	//titulos
	$('[title]').tooltip({ 
		track: false, 
		delay: 0, 
		showURL: false, 
		showBody: true, 
		fade: 30 
	});
	</script>
<? } ?>
