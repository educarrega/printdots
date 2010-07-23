<? 
session_start();
#require_once "../../conn.php";
?>

<? if(!$codigo){
	## RELATORIO INICIAL ##	
	$qr = mysql_query("SELECT Count(ativo) as ativos FROM carteira WHERE ativo=1");
	$at = mysql_fetch_object($qr);
	$ativos = $at->ativos;

	$qr = mysql_query("SELECT Count(ativo) as inativos FROM carteira WHERE ativo<>1");
	$at = mysql_fetch_object($qr);
	$inativos = $at->inativos;
	?>

	<table class="days_top">
	<tr>
	<td class="days_top_1"></td>
	<td class="days_top_2 days_top_click">Carteiras</td>
	<td class="days_top_3"></td>
	<td class="days_top_4">&nbsp;</td>
	<td class="days_top_5"></td>
	</tr>
	</table>

	<div id="days_middle" class="days_middle suggestionList">
	
	<input id="bt_add_carteira" type="image" src="images/bt_mais.png" title="Incluir nova Carteira de Pagamento" />
	<div id="add_carteira">Adicionar carteira aqui</div>

	<?
	$indice = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,X,Z,W,Y,0,1,2,3,4,5,6,7,8,9,0,-,+,.,?,!,@,#,$,&,*,(,),[,],{,},|,/,<,>";
	$indice = explode(",",$indice);
	?>
	<fieldset class="fieldset_infos">
	<legend>Índice de Carteiras de Pagamento por Letras e Símbolos</legend>
	<div id="indices">
	<?
	foreach($indice as $k=>$v){
		$t = 0;
		$qr = mysql_query("SELECT Count(codigo) as contagem FROM carteira WHERE titulo LIKE '$v%'");
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
	$campos = 'titulo,descricao,ativo';
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

$SQL = "SELECT * FROM carteira $wr";
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
	<li<?= $bck ?> id="carteira_<?= $rs[codigo] ?>" onmouseover="controles(<?= $rs[codigo] ?>)">
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
	<img src="images/bt_ampliar.png" onclick="ampliar_carteira('<?= $rs[codigo] ?>')" alt="ampliar" title="Mais detalhes desta carteira" /> 
	<img src="images/bt_carteiras.png" onclick="onde_carteira('<?= $rs[codigo] ?>')" alt="os" title="Ver as entradas e saídas que utilizam esta carteira" /> 
	<img src="images/bt_editar.png" onclick="editar_carteira('<?= $rs[codigo] ?>')" alt="editar" title="Editar esta Carteira de Pagamento" /> 
	<img src="images/bt_excluir.png" onclick="excluir_carteira('<?= $rs[codigo] ?>')"  alt="excluir" title="Excluir esta Carteira de Pagamento" />
	</div>
	
	<div class="carteira_coluna_0">
		<div class="producao_busca_indice"><?= $resultados ?></div>
		<?
		echo "<span id='carteira_status_". $rs[codigo] ."'>";
		if($rs[ativo]=="1"){
		echo "<img src='images/bt_check.png' class='carteira_status' title='Carteira Ativa' onclick='carteira_status(". $rs[codigo] .",0)' >";
		}else{
		echo "<img src='images/bt_atencao.png' class='carteira_status' title='Carteira Inativa' onclick='carteira_status(". $rs[codigo] .",1)' >";
		} 
		echo "</span>"; ?>
	</div>
	
	<div class="carteira_coluna_1">
		<div class="carteira_titulo"><?= $rs[titulo] ?>&nbsp;</div>
		<div class="carteira_descricao">
		<?= $rs[descricao] ?><br /><br />
		</div>
	</div>
	
	<div class="carteira_coluna_2">
		<br /><br /><br />
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
	$tx_filter = "$filtrados Carteiras encontradas em \"$filter\"";
	if($filtrados=='1') $tx_filter= "$filtrados Carteira encontradas em \"$filter\"";
	if(!$filtrados) $tx_filter = "<img src=\"images/bt_atencao.png\" align=\"absmiddle\" width=\"24\"> Nenhuma  Carteira encontrada em \"$filter\"";
	?>

	<script type="text/javascript">
	busca_status('<?= $tx_filter ?>. <?= $ativos ?> ativas e <?= $inativos ?> inativas.');

	function filter(filter){
		$('#carteira_todas').load('utf2iso.php?file=plugins/carteira/carteira_lista.php',{filter: filter});
	}

	function editar_carteira(codigo){
		$('#carteira_'+ codigo).load('utf2iso.php?file=plugins/carteira/carteira_editar.php',{codigo: codigo},function(){
			$('#carteira_'+ codigo).slideDown('slow');
			$('#carteira_'+ codigo).css({'overflow':'none', 'height': 'auto'});
		});
	}
	<? if($editar){ ?>
	editar_carteira(<?= $editar ?>);
	window.location.hash = '#carteira_<?= $editar ?>';
	$("input[name='titulo']:first", document.forms[0]).focus();
	<? } ?>
	
	function carteira_status(codigo,status){
		$.post('utf2iso.php?file=plugins/carteira/carteira_editar.php',{codigo: codigo, gravar: 'status', status: status});
		$('#carteira_'+ codigo).load('utf2iso.php?file=plugins/carteira/carteira_lista.php',{codigo: codigo});
	}
	
	function ampliar_carteira(codigo){
		$("#full_frame").load('utf2iso.php?file=plugins/carteira/carteira_ampliar.php',{codigo: codigo});
		$("#full_frame").slideDown(function(){
			$("#full_background").fadeIn(500);
		});
	}
	
	function excluir_carteira(codigo){
		if(confirm('Deseja mesmo excluir esta Carteira de Pagamento?\nEsta ação é irreversível!')){
		$.post('utf2iso.php?file=plugins/carteira/carteira_editar.php',{codigo: codigo, gravar: 'excluir'},function(){
			$('#carteira_status_<?= $rs[codigo] ?>').hide();
			$('#carteira_'+ codigo).slideUp('slow');
			busca_status('A Carteira de Pagamento foi excluída.');
		});
		}
	}
	
	function onde_carteira(codigo){
		$("#content_right").load('utf2iso.php?file=plugins/bancos/bancos.php',{carteira: codigo});
	}

	$('#bt_add_carteira').click(function(){
		$('#add_carteira').load('utf2iso.php?file=plugins/carteira/carteira_editar.php',function(){
			$('#bt_add_carteira').fadeOut();
			$('#add_carteira').slideDown('slow');
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
