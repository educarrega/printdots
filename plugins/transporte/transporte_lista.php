<? 
session_start();
#require_once "../../conn.php";
?>

<? if(!$codigo){
	## RELATORIO INICIAL ##	
	$qr = mysql_query("SELECT Count(ativo) as ativos FROM transporte WHERE ativo=1");
	$at = mysql_fetch_object($qr);
	$ativos = $at->ativos;

	$qr = mysql_query("SELECT Count(ativo) as inativos FROM transporte WHERE ativo<>1");
	$at = mysql_fetch_object($qr);
	$inativos = $at->inativos;
	?>

	<table class="days_top">
	<tr>
	<td class="days_top_1"></td>
	<td class="days_top_2 days_top_click">Transportes</td>
	<td class="days_top_3"></td>
	<td class="days_top_4">&nbsp;</td>
	<td class="days_top_5"></td>
	</tr>
	</table>

	<div id="days_middle" class="days_middle suggestionList">
	
	<input id="bt_add_transporte" type="image" src="images/bt_mais.png" title="Incluir novo Sistema de Transporte" />
	<div id="add_transporte">Adicionar transporte aqui</div>

	<?
	$indice = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,X,Z,W,Y,0,1,2,3,4,5,6,7,8,9,0,-,+,.,?,!,@,#,$,&,*,(,),[,],{,},|,/,<,>";
	$indice = explode(",",$indice);
	?>
	<fieldset class="fieldset_infos">
	<legend>Índice de Sistemas de Transporte por Letras e Símbolos</legend>
	<div id="indices">
	<?
	foreach($indice as $k=>$v){
		$t = 0;
		$qr = mysql_query("SELECT Count(codigo) as contagem FROM transporte WHERE servico LIKE '$v%'");
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
	$campos = 'codigo,servico,descricao,endereco,telefone,email,site,contato,prazo_entrega,ativo';
	$campos = explode(',',$campos);
	$wr = "WHERE (";
	for($i=0;$i<count($campos);$i++){
		$wr .= "UPPER(". $campos[$i].") LIKE '%". $busca ."%' OR ";
	}
	$wr = substr($wr,0,strlen($wr)-4).") ORDER BY servico";
	$filter = $busca;
}elseif(!$codigo){
	if(!$order) $order = "servico";
	if(($ativos+$inativos) >20 && !isset($filter)) $filter = "A";
	if(isset($filter)) $filter_qr = "WHERE UPPER(servico) LIKE '". $filter ."%'";
	if(!isset($filter)) $filter_qr = "";
	$wr = "$filter_qr ORDER BY ativo DESC,$order ASC";
}else{
	$wr = "WHERE codigo='$codigo'";
}

$SQL = "SELECT * FROM transporte $wr";
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
	<li<?= $bck ?> id="transporte_<?= $rs[codigo] ?>" onmouseover="controles(<?= $rs[codigo] ?>)">
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
	<img src="images/bt_ampliar.png" onclick="ampliar_transporte('<?= $rs[codigo] ?>')" alt="ampliar" title="Mais detalhes deste transporte" /> 
	<img src="images/bt_producao.png" onclick="os_transporte('<?= $rs[codigo] ?>')" alt="os" title="Ver as Ordens de Serviço que utilizam este transporte" /> 
	<img src="images/bt_editar.png" onclick="editar_transporte('<?= $rs[codigo] ?>')" alt="editar" title="Editar este sistema de transporte" /> 
	<img src="images/bt_excluir.png" onclick="excluir_transporte('<?= $rs[codigo] ?>')"  alt="excluir" title="Excluir este sistema de transporte" />
	</div>
	
	<div class="transporte_coluna_0">
		<div class="producao_busca_indice"><?= $resultados ?></div>
		<?
		echo "<span id='transporte_status_". $rs[codigo] ."'>";
		if($rs[ativo]=="1"){
		echo "<img src='images/bt_check.png' class='transporte_status' title='Transporte Ativo' onclick='transporte_status(". $rs[codigo] .",0)' >";
		}else{
		echo "<img src='images/bt_atencao.png' class='transporte_status' title='Transporte Inativo' onclick='transporte_status(". $rs[codigo] .",1)' >";
		} 
		echo "</span>"; ?>
	</div>
	
	<div class="transporte_coluna_1">
		<div class="transporte_titulo"><?= $rs[servico] ?>&nbsp;</div>
		<div class="transporte_descricao">
		Prazo de Entrega: <?= $rs[prazo_entrega] ?><br />
		<?= $rs[descricao] ?>&nbsp;<br />		
		</div>
	</div>
	
	<div class="equipamento_coluna_2">
	Telefone: <?= $rs[telefone] ?> | 
	Contato: <?= $rs[contato] ?><br />
	Email: <?= $rs[email] ?><br />
	Descricao: <?= $rs[descricao] ?><br />
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
	$tx_filter = "$filtrados s. transporte encontrados em \"$filter\"";
	if($filtrados=='1') $tx_filter= "$filtrados s. transporte encontrado em \"$filter\"";
	if(!$filtrados) $tx_filter = "<img src=\"images/bt_atencao.png\" align=\"absmiddle\" width=\"24\"> Nenhum s. transporte encontrado em \"$filter\"";
	?>

	<script type="text/javascript">
	busca_status('<?= $tx_filter ?>. <?= $ativos ?> ativos e <?= $inativos ?> inativos.');

	function filter(filter){
		$('#transporte_todas').load('utf2iso.php?file=plugins/transporte/transporte_lista.php',{filter: filter});
	}

	function editar_transporte(codigo){
		$('#transporte_'+ codigo).load('utf2iso.php?file=plugins/transporte/transporte_editar.php',{codigo: codigo},function(){
			$('#transporte_'+ codigo).slideDown('slow');
			$('#transporte_'+ codigo).css({'overflow':'none', 'height': 'auto'});
		});
	}
	<? if($editar){ ?>
	editar_transporte(<?= $editar ?>);
	window.location.hash = '#transporte_<?= $editar ?>';
	$("input[name='servico']:first", document.forms[0]).focus();
	<? } ?>
	
	function transporte_status(codigo,status){
		$.post('utf2iso.php?file=plugins/transporte/transporte_editar.php',{codigo: codigo, gravar: 'status', status: status});
		$('#transporte_'+ codigo).load('utf2iso.php?file=plugins/transporte/transporte_lista.php',{codigo: codigo});
	}
	
	function ampliar_transporte(codigo){
		$("#full_frame").load('utf2iso.php?file=plugins/transporte/transporte_os.php',{transporte: codigo});
		$("#full_frame").slideDown(function(){
			$("#full_background").fadeIn(500);
		});
	}
	
	function excluir_transporte(codigo){
		if(confirm('Deseja mesmo excluir este Sistema de Transporte?\nEsta ação é irreversível!')){
		$.post('utf2iso.php?file=plugins/transporte/transporte_editar.php',{codigo: codigo, gravar: 'excluir'},function(){
			$('#transporte_status_<?= $rs[codigo] ?>').hide();
			$('#transporte_'+ codigo).slideUp('slow');
			busca_status('O Sistema de Transporte foi excluído.');
		});
		}
	}
	
	function os_transporte(codigo){
		$("#content_right").load('utf2iso.php?file=plugins/producao/producao.php',{transporte: codigo, filtro:'Transporte', order:'cod_transporte', tabela: 'transporte', campo: 'servico', data_prevista: '<?= $data_prevista ?>', info_aberto: 'transporte'});
	}

	$('#bt_add_transporte').click(function(){
		$('#add_transporte').load('utf2iso.php?file=plugins/transporte/transporte_editar.php',function(){
			$('#bt_add_transporte').fadeOut();
			$('#add_transporte').slideDown('slow');
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
