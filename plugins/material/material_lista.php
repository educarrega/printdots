<? 
session_start();
#require_once "../../conn.php";
?>

<? if(!$codigo){
	## RELATORIO INICIAL ##	
	$qr = mysql_query("SELECT Count(ativo) as ativos FROM material WHERE ativo=1");
	$at = mysql_fetch_object($qr);
	$ativos = $at->ativos;

	$qr = mysql_query("SELECT Count(ativo) as inativos FROM material WHERE ativo<>1");
	$at = mysql_fetch_object($qr);
	$inativos = $at->inativos;
	?>

	<table class="days_top">
	<tr>
	<td class="days_top_1"></td>
	<td class="days_top_2 days_top_click" title="Recarrega a Lista">Material e Serviço</td>
	<td class="days_top_3"></td>
	<td class="days_top_4">&nbsp;</td>
	<td class="days_top_5"></td>
	</tr>
	</table>

	<div id="days_middle" class="days_middle suggestionList">
	
	<input id="bt_add_material" type="image" src="images/bt_mais.png" title="Incluir novo material" />
	<div id="add_material">Adicionar material aqui</div>

	<?
	$indice = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,X,Z,W,Y,0,1,2,3,4,5,6,7,8,9,0,-,+,.,?,!,@,#,$,&,*,(,),[,],{,},|,/,<,>";
	$indice = explode(",",$indice);
	?>
	<fieldset class="fieldset_infos">
	<legend>Índice por Letras e Símbolos</legend>
	<div id="indices">
	<?
	foreach($indice as $k=>$v){
		$t = 0;
		$qr = mysql_query("SELECT Count(codigo) as contagem FROM material WHERE titulo LIKE '$v%'");
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
	//$SQL = "SELECT * FROM material $wr";
	//$QR = mysql_query($SQL);
	$campos = 'codigo,titulo,descricao,base_calculo,peso,largura,altura,profundidade,litro,hora,valor,codigo_fornecedor,ativo';
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

$SQL = "SELECT * FROM material $wr";
#echo $SQL;
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
	<li<?= $bck ?> id="material_<?= $rs[codigo] ?>" onmouseover="controles(<?= $rs[codigo] ?>)">
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
	<img src="images/bt_ampliar.png" onclick="ampliar_material('<?= $rs[codigo] ?>')" alt="ampliar" title="Mais detalhes deste material" /> 
	<img src="images/bt_producao.png" onclick="onde_material('<?= $rs[codigo] ?>')" alt="os" title="Ver as entradas e saídas que utilizam este material" /> 
	<img src="images/bt_editar.png" onclick="editar_material('<?= $rs[codigo] ?>')" alt="editar" title="Editar este material" /> 
	<img src="images/bt_excluir.png" onclick="excluir_material('<?= $rs[codigo] ?>')"  alt="excluir" title="Excluir este material" />
	</div>
	
	<div class="material_coluna_0">
		<div class="producao_busca_indice"><?= $resultados ?></div>
		<?
		echo "<span id='material_status_". $rs[codigo] ."'>";
		if($rs[ativo]=="1"){
		echo "<img src='images/bt_check.png' class='material_status' title='material Ativo' onclick='material_status(". $rs[codigo] .",0)' >";
		}else{
		echo "<img src='images/bt_atencao.png' class='material_status' title='material Inativo' onclick='material_status(". $rs[codigo] .",1)' >";
		} 
		echo "</span>"; ?>
	</div>
	
	<div class="material_coluna_1">
		<div class="material_titulo"><?= $rs[titulo] ?>&nbsp;</div>
		<div class="material_descricao"><?= $rs[descricao] ?></div>
		<div class="material_coluna_2">
		Base de calculo: <?= $rs[base_calculo] ?> | 
		Codigo no fornecedor: <?= $rs[codigo_fornecedor] ?>
		</div>
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
	$tx_filter = "$filtrados Materiais e Serviços encontrados em \"$filter\"";
	if($filtrados=='1') $tx_filter= "$filtrados Materiais e Serviços encontrado em \"$filter\"";
	if(!$filtrados) $tx_filter = "<img src=\"images/bt_atencao.png\" align=\"absmiddle\" width=\"24\"> Nenhum material encontrado em \"$filter\"";
	?>

	<script type="text/javascript">
	busca_status('<?= $tx_filter ?>. <?= $ativos ?> ativos e <?= $inativos ?> inativos.');
	
	$('.days_top_2').click(function(){
		$('#material_todas').load('utf2iso.php?file=plugins/material/material_lista.php');
	})
	
	function filter(filter){
		$('#material_todas').load('utf2iso.php?file=plugins/material/material_lista.php',{filter: filter});
	}

	function editar_material(codigo){
		$('#material_'+ codigo).load('utf2iso.php?file=plugins/material/material_editar.php',{codigo: codigo},function(){
			$('#material_'+ codigo).slideDown('slow');
			$('#material_'+ codigo).css({'overflow':'none', 'height': 'auto'});
		});
	}
	<? if($editar){ ?>
	editar_material(<?= $editar ?>);
	window.location.hash = '#material_<?= $editar ?>';
	$("input[name='titulo']:first", document.forms[0]).focus();
	<? } ?>
	
	function material_status(codigo,status){
		$.post('utf2iso.php?file=plugins/material/material_editar.php',{codigo: codigo, gravar: 'status', status: status});
		$('#material_'+ codigo).load('utf2iso.php?file=plugins/material/material_lista.php',{codigo: codigo});
	}
	
	function ampliar_material(codigo){
		$("#full_frame").load('utf2iso.php?file=plugins/material/material_ampliar.php',{codigo: codigo});
		$("#full_frame").slideDown(function(){
			$("#full_background").fadeIn(500);
		});
	}
	
	function excluir_material(codigo){
		if(confirm('Deseja mesmo excluir este material?\nEsta ação é irreversível!')){
		$.post('utf2iso.php?file=plugins/material/material_editar.php',{codigo: codigo, gravar: 'excluir'},function(){
			$('#material_status_<?= $rs[codigo] ?>').hide();
			$('#material_'+ codigo).slideUp('slow');
			busca_status('O material foi excluído.');
		});
		}
	}
	
	function onde_material(codigo){
		$("#content_right").load('utf2iso.php?file=plugins/contas_pagar/contas_pagar_relatorio.php',{titulo: codigo, filtro:'material', order:'cod_material', tabela: 'material', campo: 'material'});
	}

	$('#bt_add_material').click(function(){
		$('#add_material').load('utf2iso.php?file=plugins/material/material_editar.php',function(){
			$('#bt_add_material').fadeOut();
			$('#add_material').slideDown('slow');
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
