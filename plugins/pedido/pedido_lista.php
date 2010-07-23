<? 
session_start();
#require_once "../../conn.php";
?>

<? if(!$codigo){
	## RELATORIO INICIAL ##	
	$qr = mysql_query("SELECT Count(ativo) as ativos FROM pedido WHERE ativo=1 AND (codigo_rel='' OR ISNULL(codigo_rel))");
	$at = mysql_fetch_object($qr);
	$ativos = $at->ativos;

	$qr = mysql_query("SELECT Count(ativo) as inativos FROM pedido WHERE ativo<>1 AND (codigo_rel='' OR ISNULL(codigo_rel))");
	$at = mysql_fetch_object($qr);
	$inativos = $at->inativos;
	?>

	<table class="days_top">
	<tr>
	<td class="days_top_1"></td>
	<td class="days_top_2 days_top_click" title="Recarrega a Lista">Pedidos</td>
	<td class="days_top_3"></td>
	<td class="days_top_4">&nbsp;</td>
	<td class="days_top_5"></td>
	</tr>
	</table>

	<div id="days_middle" class="days_middle suggestionList">
	
	<div id="order_<?= $codigo ?>" class="order_img"></div>
	<input id="bt_add_pedido" type="image" src="images/bt_mais.png" title="Incluir novo pedido" />
	
	<fieldset class="fieldset_pedido">
	<legend>Organizar Pedidos</legend>
	<div id="indices">
	<a href="#abertura" onclick="order('data_criacao')" class="<? if($order == "data_criacao") echo order_visited ?>">Data abertura</a> 
	<a href="#lancado" onclick="order('data_lancamento')" class="<? if($order == "data_lancamento") echo order_visited ?>">Data lançamento</a> 
	<a href="#fornecedor" onclick="order('cliente_codigo')" class="<? if($order == "cliente_codigo") echo order_visited ?>">Fornecedor</a> 
	<a href="#transporte" onclick="order('transporte_codigo')" class="<? if($order == "transporte_codigo") echo order_visited ?>">Transporte</a>
	<a href="#valor" onclick="order('valor_total')" class="<? if($order == "valor_total") echo order_visited ?>">Valor</a>
	</div>
	<input type="radio" name="filter" value="0" <? if($filter=='0' || !$filter) echo checked ?> onclick="order('<?= $order ?>')" /> Pedidos pendentes
	<input type="radio" name="filter" value="1" <? if($filter=='1') echo checked ?> onclick="order('<?= $order ?>')" /> Pedidos lançados
	</fieldset>

	<hr />

<? } ?>

<? 

if($busca){
	$busca = strtoupper(acento(urldecode($busca)));
	if(strstr($busca,'/')) $busca = mydate($busca);
	
	$SQLb = "
	SELECT pedido.codigo, pedido.codigo_rel
	FROM pedido, material
	WHERE 
	UPPER( transporte_valor ) LIKE  '%$busca%' 
	OR UPPER( cliente_contato ) LIKE  '%$busca%' 
	OR UPPER( cliente_email ) LIKE  '%$busca%' 
	OR UPPER( data_criacao ) LIKE  '%$busca%' 
	OR UPPER( data_lancamento ) LIKE  '%$busca%' 
	OR UPPER( material_quantidade ) LIKE  '%$busca%' 
	OR UPPER( valor_total ) LIKE  '%$busca%' 
	OR UPPER( valor_acrescimo ) LIKE  '%$busca%' 
	OR UPPER( pedido.peso ) LIKE  '%$busca%' 
	OR UPPER( pedido.descricao ) LIKE  '%$busca%' 
	OR (material.codigo LIKE  '%$busca%' AND material_codigo = material.codigo ) 
	OR (UPPER(material.titulo) LIKE  '%$busca%' AND material_codigo = material.codigo ) 
	OR (UPPER(material.descricao) LIKE  '%$busca%' AND material_codigo = material.codigo ) 
	GROUP BY pedido.codigo
	ORDER BY pedido.codigo_rel
	";
	$QRb = mysql_query($SQLb);
	while($rb = mysql_fetch_array($QRb)){
		if($rb[codigo_rel]=='') $codigos[] .= $rb[codigo];
		$codigos[] .= $rb[codigo_rel];
	}
	for($i=0;$i<count($codigos);$i++){
		$wr .= "codigo = '$codigos[$i]' OR ";
	}
	if(count($codigos)>0){
		$wr = "WHERE ". substr($wr,0,strlen($wr)-4)." ORDER BY codigo DESC";
	}else{
		$wr = "WHERE codigo='0'";
	}
	$filter = $busca;
}elseif(!$codigo){

	if($_SESSION[ASCDESC]=="DESC") {
		$_SESSION[ASCDESC] = "ASC";
		$_ascdesc = '<img src="images/bt_acima.png" alt="" title="crescente">';
	}else{
		$_SESSION[ASCDESC] = "DESC";
		$_ascdesc = '<img src="images/bt_abaixo.png" alt="" title="decrescente">';
	}
	
	#botoes de ordem
	if(!$order) {
		$_SESSION[ASCDESC] = "DESC";
		$order = "codigo";
	}
	$order_by = "ORDER BY $order $_SESSION[ASCDESC]";
	
	#radio de lancados ou pendentes
	$ativo = '0';
	if($filter) $ativo = "1";
	$wr = "WHERE (codigo_rel='' OR ISNULL(codigo_rel)) AND ativo='$ativo' $order_by";
}else{
	$wr = "WHERE codigo='$codigo'";
}

$SQL = "SELECT * FROM pedido $wr";
#echo $SQL;
$QR = mysql_query($SQL);
if(!$codigo) $filtrados = mysql_num_rows($QR);
while($rs = mysql_fetch_array($QR)){ 
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
	<li<?= $bck ?> id="pedido_<?= $rs[codigo] ?>" onmouseover="controles(<?= $rs[codigo] ?>)">
<? } 
	?>
	
	<div id="bt_controles_<?= $rs[codigo] ?>" class="bt_controles">
	<img src="images/bt_ampliar.png" onclick="print_pedido('<?= $rs[codigo] ?>')" alt="ampliar" title="Mais detalhes deste pedido" /> 
	<img src="images/bt_editar.png" onclick="editar_pedido('<?= $rs[codigo] ?>')" alt="editar" title="Editar este pedido" />
	<?
	$qrc = mysql_query("SELECT codigo FROM contas_pagar WHERE codigo_pedido='$rs[codigo]'");
	$rsc = mysql_fetch_array($qrc);
	?>
	<img src="images/bt_contas_pagar.png" onclick="lancar_pedido('<?= $rs[codigo] ?>')" alt="editar" title="<? if(!$rsc[codigo]){ ?>Lançar este pedido<? }else{ ?>Quitar vencimentos<? } ?>" />
	<? if(!$rsc[codigo]){ ?>
	<img src="images/bt_excluir.png" onclick="excluir_pedido_<?= $codigo ?>('<?= $rs[codigo] ?>')"  alt="excluir" title="Excluir este pedido" />
	<? } ?>
	</div>
	
	<div class="pedido_coluna_0">
		<div class="producao_busca_indice"><?= $rs[codigo] ?></div>
		<?
		echo "<span id='pedido_status_". $rs[codigo] ."'>";
		if($rs[ativo]=="1"){
		echo "<img src='images/bt_check.png' class='pedido_status' title='Pedido efetivado' />";
		}else{
		echo "<img src='images/bt_atencao.png' class='pedido_status' title='Pedido em andamento, deseja lançar?' onclick='lancar_pedido(\"$rs[codigo]\")' />";
		} 
		echo "</span>"; ?>
	</div>
	<?
	$SQLc = "SELECT codigo,nome_fantasia,cidade,estado,telefone FROM cliente WHERE codigo='$rs[cliente_codigo]'";
	$QRc = mysql_query($SQLc);
	$rc = mysql_fetch_array($QRc);
	
	$SQLt = "SELECT nome_fantasia FROM cliente WHERE codigo='$rs[transporte_codigo]'";
	$QRt = mysql_query($SQLt);
	$rt = mysql_fetch_array($QRt);
	?>
	<div class="pedido_coluna_1">
		<div class="pedido_titulo"><?= $rc[nome_fantasia] ?>&nbsp;</div>
		<div class="pedido_descricao">
		<small><?= $rc[cidade] ?> <?= $rc[estado] ?> <?= $rc[telefone] ?><br />
		<?= $rt[nome_fantasia] ?>&nbsp;<br />
		Abertura <?= mydate($rs[data_criacao]) ?> | Lançado: <?= mydate($rs[data_lancamento]) ?>
		</div></small>
	</div>
	
	<div class="pedido_coluna_2">
	<?
	$SQLs = "SELECT * FROM pedido WHERE codigo_rel='$rs[codigo]'";
	#echo $SQL;
	$SQLs = mysql_query($SQLs);
	while($rss = mysql_fetch_array($SQLs)){

		$SQLp = "SELECT titulo FROM material WHERE codigo='$rss[material_codigo]'";
		#echo $SQLp;
		$SQLpdt = mysql_query($SQLp);
		$pdt = mysql_fetch_array($SQLpdt);
		?>
		<?= $rss[material_quantidade] ?> | 
		<?= $pdt[titulo] ?> | 
		R$ <?= $rss[valor_total] ?> |
		<?= $rss[peso] ?> Kg
		<br />
			
	<? }#while
	$total_com_acrescimo = number_format($total_com_acrescimo,2,'.',''); ?>
	<hr />
	Totais: R$ <?= $rs[valor_total] ?> | R$ <?= $rs[transporte_valor] ?> <?= $rs[peso] ?> kg
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
	$tx_filter = "$filtrados Pedidos"; #em \"$filter\"";
	if($filtrados=='1') $tx_filter= "$filtrados Pedidos encontrado em \"$filter\"";
	if(!$filtrados) $tx_filter = "<img src=\"images/bt_atencao.png\" align=\"absmiddle\" width=\"24\"> Nenhum pedido encontrado em \"$filter\"";
	?>

	<script type="text/javascript">
	busca_status('<?= $tx_filter ?>. <?= $ativos ?> lançados e <?= $inativos ?> pendentes.');
	
	$('#order_<?= $codigo ?>').html('<?= $_ascdesc ?>');
	
	$('[title]').tooltip({ 
	track: false, 
	delay: 0, 
	showURL: false, 
	showBody: true, 
	fade: 30 
	});
	
	$('.days_top_2').click(function(){
		$('#pedido_todas').load('utf2iso.php?file=plugins/pedido/pedido_lista.php');
	})
	
	function order(order){
		var filter = $('input[type=radio][name=filter]:checked').val();
		$('#pedido_todas').load('utf2iso.php?file=plugins/pedido/pedido_lista.php',{order: order, filter: filter});
	}

	function editar_pedido(codigo){
		$('#pedido_'+ codigo).load('utf2iso.php?file=plugins/pedido/pedido_editar.php',{codigo: codigo},function(){
			$('#pedido_'+ codigo).slideDown('slow');
			$('#pedido_'+ codigo).css({'overflow':'none', 'height': 'auto'});
		});
	}
	<? if($editar){ ?>
	editar_pedido(<?= $editar ?>);
	window.location.hash = '#pedido_<?= $editar ?>';
	$("input[name='pedido']:first", document.forms[0]).focus();
	<? } ?>
	
	function lancar_pedido(codigo){
		$("#full_frame").load('utf2iso.php?file=plugins/pedido/pedido_lancar.php',{codigo: codigo});
		$("#full_frame").slideDown(function(){
			$("#full_background").fadeIn(500);
		});
	}
	
	function ampliar_pedido(codigo){
		$("#full_frame").load('utf2iso.php?file=plugins/pedido/pedido_print.php',{codigo: codigo});
		$("#full_frame").slideDown(function(){
			$("#full_background").fadeIn(500);
		});
	}
	
	function excluir_pedido_<?= $codigo ?>(codigo){
		if(confirm('Deseja mesmo excluir este pedido?\nEsta ação é irreversível!')){
		$.post('utf2iso.php?file=plugins/pedido/pedido_editar.php',{codigo: codigo, gravar: 'excluir'},function(){
			$('#pedido_status_<?= $rs[codigo] ?>').hide();
			$('#pedido_'+ codigo).slideUp('slow');
			busca_status('O pedido foi excluído.');
		});
		}
	}
	
	$('#bt_add_pedido').click(function(){
		$('#pedido_todas').load('utf2iso.php?file=plugins/pedido/pedido_editar.php',{gravar: 'incluir'},function(){
			//$('#bt_add_pedido').fadeOut();
			//$('#add_pedido').slideDown('slow');
		});	
	});
	
	function print_pedido(codigo){
		win = open("plugins/pedido/pedido_print.php?codigo="+codigo,"pedido_print_"+codigo);
	}
	
	function pedido_send(codigo){
		win = open("plugins/pedido/pedido_send.php?codigo="+codigo,"pedido_send_"+codigo);
	}
	
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
