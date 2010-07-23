<? 
session_start();
#require_once "../../conn.php";
?>

<? if(!$codigo){
	## RELATORIO INICIAL ##	
	$qr = mysql_query("SELECT Count(ativo) as ativos FROM produto WHERE ativo=1");
	$at = mysql_fetch_object($qr);
	$ativos = $at->ativos;

	$qr = mysql_query("SELECT Count(ativo) as inativos FROM produto WHERE ativo<>1");
	$at = mysql_fetch_object($qr);
	$inativos = $at->inativos;
	?>

	<table class="days_top">
	<tr>
	<td class="days_top_1"></td>
	<td class="days_top_2 days_top_click">Produtos</td>
	<td class="days_top_3"></td>
	<td class="days_top_4">&nbsp;</td>
	<td class="days_top_5"></td>
	</tr>
	</table>

	<div id="days_middle" class="days_middle suggestionList">
	
	<input id="bt_add_produto" type="image" src="images/bt_mais.png" title="Incluir novo Produto" />
	<div id="add_produto">Adicionar produto aqui</div>

	<?
	$indice = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,X,Z,W,Y,0,1,2,3,4,5,6,7,8,9,0,-,+,.,?,!,@,#,$,&,*,(,),[,],{,},|,/,<,>";
	$indice = explode(",",$indice);
	?>
	<fieldset class="fieldset_infos">
	<legend>Índice de Produtos por Letras e Símbolos</legend>
	<div id="indices">
	<?
	foreach($indice as $k=>$v){
		$t = 0;
		$qr = mysql_query("SELECT Count(codigo) as contagem FROM produto WHERE titulo LIKE '$v%'");
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
	$campos = 'codigo,titulo,descricao,equipamento,ppm,ativo';
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

$SQL = "SELECT * FROM produto $wr";
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
	<li<?= $bck ?> id="produto_<?= $rs[codigo] ?>" onmouseover="controles(<?= $rs[codigo] ?>)">
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
	<img src="images/bt_ampliar.png" onclick="ampliar_produto('<?= $rs[codigo] ?>')" alt="ampliar" title="Mais detalhes deste produto" /> 
	<img src="images/bt_producao.png" onclick="os_produto('<?= $rs[codigo] ?>')" alt="os" title="Ver as Ordens de Serviço que utilizam este produto" /> 
	<img src="images/bt_editar.png" onclick="editar_produto('<?= $rs[codigo] ?>')" alt="editar" title="Editar este produto" /> 
	<img src="images/bt_excluir.png" onclick="excluir_produto('<?= $rs[codigo] ?>')"  alt="excluir" title="Excluir este produto" />
	</div>
	
	<div class="produto_coluna_0">
		<div class="producao_busca_indice"><?= $resultados ?></div>
		<?
		echo "<span id='produto_status_". $rs[codigo] ."'>";
		if($rs[ativo]=="1"){
		echo "<img src='images/bt_check.png' class='produto_status' title='produto Ativo' onclick='produto_status(". $rs[codigo] .",0)' >";
		}else{
		echo "<img src='images/bt_atencao.png' class='produto_status' title='produto Inativo' onclick='produto_status(". $rs[codigo] .",1)' >";
		} 
		echo "</span>"; ?>
	</div>
	
	<div class="produto_coluna_1">
		<div class="produto_titulo"><?= $rs[titulo] ?>&nbsp;</div>
		<div class="produto_descricao">
		<?= $rs[descricao] ?>&nbsp;<br />		
		</div>
	</div>
	
	<div class="produto_coluna_2">
	Peso por MÂ²: <?= $rs[ppm] ?> mg<br />
	<?
	$equipamentos = explode(',',$rs[equipamento]);
	$SQLe = "SELECT codigo,titulo FROM equipamento ORDER BY ativo DESC, titulo";
	$QRe = mysql_query($SQLe);
	while($eq = mysql_fetch_array($QRe)){
	if(in_array("$eq[codigo]",$equipamentos)){
		?><?= $eq[titulo] ?>, <?
	} 
	}?><br />
	Composição: 
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
	$tx_filter = "$filtrados produto encontrados em \"$filter\"";
	if($filtrados=='1') $tx_filter= "$filtrados produto encontrado em \"$filter\"";
	if(!$filtrados) $tx_filter = "<img src=\"images/bt_atencao.png\" align=\"absmiddle\" width=\"24\"> Nenhum produto encontrado em \"$filter\"";
	?>

	<script type="text/javascript">
	busca_status('<?= $tx_filter ?>. <?= $ativos ?> ativos e <?= $inativos ?> inativos.');

	function filter(filter){
		$('#produto_todas').load('utf2iso.php?file=plugins/produto/produto_lista.php',{filter: filter});
	}

	function editar_produto(codigo){
		$('#produto_'+ codigo).load('utf2iso.php?file=plugins/produto/produto_editar.php',{codigo: codigo},function(){
			$('#produto_'+ codigo).slideDown('slow');
			$('#produto_'+ codigo).css({'overflow':'none', 'height': 'auto'});
		});
	}
	<? if($editar){ ?>
	editar_produto(<?= $editar ?>);
	window.location.hash = '#produto_<?= $editar ?>';
	$("input[name='titulo']:first", document.forms[0]).focus();
	<? } ?>
	
	function produto_status(codigo,status){
		$.post('utf2iso.php?file=plugins/produto/produto_editar.php',{codigo: codigo, gravar: 'status', status: status});
		$('#produto_'+ codigo).load('utf2iso.php?file=plugins/produto/produto_lista.php',{codigo: codigo});
	}
	
	function ampliar_produto(codigo){
		$("#full_frame").load('utf2iso.php?file=plugins/produto/produto_os.php',{produto: codigo});
		$("#full_frame").slideDown(function(){
			$("#full_background").fadeIn(500);
		});
	}
	
	function excluir_produto(codigo){
		if(confirm('Deseja mesmo excluir este produto?\nEsta ação é irreversível!')){
		$.post('utf2iso.php?file=plugins/produto/produto_editar.php',{codigo: codigo, gravar: 'excluir'},function(){
			$('#produto_status_<?= $rs[codigo] ?>').hide();
			$('#produto_'+ codigo).slideUp('slow');
			busca_status('O produto foi excluído.');
		});
		}
	}
	
	function os_produto(codigo){
		$("#content_right").load('utf2iso.php?file=plugins/producao/producao.php',{produto: codigo, filtro:'produto', order:'cod_produto', tabela: 'produto', campo: 'titulo', data_prevista: '<?= $data_prevista ?>', info_aberto: 'equipamento'});
	}

	$('#bt_add_produto').click(function(){
		$('#add_produto').load('utf2iso.php?file=plugins/produto/produto_editar.php',function(){
			$('#bt_add_produto').fadeOut();
			$('#add_produto').slideDown('slow');
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
