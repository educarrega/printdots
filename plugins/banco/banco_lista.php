<? 
session_start();
#require_once "../../conn.php";
?>

<? if(!$codigo){
	## RELATORIO INICIAL ##	
	$qr = mysql_query("SELECT Count(ativo) as ativos FROM banco WHERE ativo=1");
	$at = mysql_fetch_object($qr);
	$ativos = $at->ativos;

	$qr = mysql_query("SELECT Count(ativo) as inativos FROM banco WHERE ativo<>1");
	$at = mysql_fetch_object($qr);
	$inativos = $at->inativos;
	?>

	<table class="days_top">
	<tr>
	<td class="days_top_1"></td>
	<td class="days_top_2 days_top_click" title="Recarrega a Lista">Bancos e Contas    </td>
	<td class="days_top_3"></td>
	<td class="days_top_4">&nbsp;</td>
	<td class="days_top_5"></td>
	</tr>
	</table>

	<div id="days_middle" class="days_middle suggestionList">
	
	<input id="bt_add_banco" type="image" src="images/bt_mais.png" title="Incluir novo banco" />
	<div id="add_banco">Adicionar banco aqui</div>

	<?
	$indice = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,X,Z,W,Y,0,1,2,3,4,5,6,7,8,9,0,-,+,.,?,!,@,#,$,&,*,(,),[,],{,},|,/,<,>";
	$indice = explode(",",$indice);
	?>
	<fieldset class="fieldset_infos">
	<legend>Índice de Bancos e Contas   por Letras e Símbolos</legend>
	<div id="indices">
	<?
	foreach($indice as $k=>$v){
		$t = 0;
		$qr = mysql_query("SELECT Count(codigo) as contagem FROM banco WHERE banco LIKE '$v%'");
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
	$campos = 'codigo,banco,banco_numero,agencia_numero,agencia_digito,conta_numero,descricao,endereco,telefone,contato,site,email';
	$campos = explode(',',$campos);
	$wr = "WHERE (";
	for($i=0;$i<count($campos);$i++){
		$wr .= "UPPER(". $campos[$i].") LIKE '%". $busca ."%' OR ";
	}
	$wr = substr($wr,0,strlen($wr)-4).") ORDER BY banco";
	$filter = $busca;
}elseif(!$codigo){
	if(!$order) $order = "banco";
	if(($ativos+$inativos) >20 && !isset($filter)) $filter = "A";
	if(isset($filter)) $filter_qr = "WHERE UPPER(banco) LIKE '". $filter ."%'";
	if(!isset($filter)) $filter_qr = "";
	$wr = "$filter_qr ORDER BY ativo DESC,$order ASC";
}else{
	$wr = "WHERE codigo='$codigo'";
}

$SQL = "SELECT * FROM banco $wr";
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
	<li<?= $bck ?> id="banco_<?= $rs[codigo] ?>" onmouseover="controles(<?= $rs[codigo] ?>)">
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
	<img src="images/bt_ampliar.png" onclick="ampliar_banco('<?= $rs[codigo] ?>')" alt="ampliar" title="Mais detalhes deste banco" /> 
	<img src="images/bt_producao.png" onclick="onde_banco('<?= $rs[codigo] ?>')" alt="os" title="Ver as entradas e saídas que utilizam este banco" /> 
	<img src="images/bt_editar.png" onclick="editar_banco('<?= $rs[codigo] ?>')" alt="editar" title="Editar este banco" /> 
	<img src="images/bt_excluir.png" onclick="excluir_banco('<?= $rs[codigo] ?>')"  alt="excluir" title="Excluir este banco" />
	</div>
	
	<div class="banco_coluna_0">
		<div class="producao_busca_indice"><?= $resultados ?></div>
		<?
		echo "<span id='banco_status_". $rs[codigo] ."'>";
		if($rs[ativo]=="1"){
		echo "<img src='images/bt_check.png' class='banco_status' title='banco Ativo' onclick='banco_status(". $rs[codigo] .",0)' >";
		}else{
		echo "<img src='images/bt_atencao.png' class='banco_status' title='banco Inativo' onclick='banco_status(". $rs[codigo] .",1)' >";
		} 
		echo "</span>"; ?>
	</div>
	
	<div class="banco_coluna_1">
		<div class="banco_titulo"><?= $rs[banco] ?>&nbsp;</div>
		<div class="banco_descricao">
		<?= $rs[descricao] ?>&nbsp;<br />		
		</div>
	</div>
	
	<div class="banco_coluna_2">
	Saída   <img src="images/<? if($rs[aceita_saida]){ ?>bt_check.png<? }else{ ?>bt_atencao.png<? } ?>" /> 
	| Entrada <img src="images/<? if($rs[aceita_entrada]){ ?>bt_check.png<? }else{ ?>bt_atencao.png<? } ?>" /> 
	| C.Corrente <img src="images/<? if($rs[conta_corrente]){ ?>bt_check.png<? }else{ ?>bt_atencao.png<? } ?>" /> 
	| Poupança <img src="images/<? if($rs[conta_poupanca]){ ?>bt_check.png<? }else{ ?>bt_atencao.png<? } ?>" /> <br />
	<?
	$carteiras = explode(',',$rs[codigo_carteira]);
	$SQLe = "SELECT codigo,titulo FROM carteira ORDER BY ativo DESC, titulo";
	$QRe = mysql_query($SQLe);
	while($eq = mysql_fetch_array($QRe)){
	if(in_array("$eq[codigo]",$carteiras)){
		?><?= $eq[titulo] ?>, <?
	} 
	}?><br />
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
	$tx_filter = "$filtrados Bancos e Contas   encontrados em \"$filter\"";
	if($filtrados=='1') $tx_filter= "$filtrados Bancos e Contas   encontrado em \"$filter\"";
	if(!$filtrados) $tx_filter = "<img src=\"images/bt_atencao.png\" align=\"absmiddle\" width=\"24\"> Nenhum banco encontrado em \"$filter\"";
	?>

	<script type="text/javascript">
	busca_status('<?= $tx_filter ?>. <?= $ativos ?> ativos e <?= $inativos ?> inativos.');
	
	$('.days_top_2').click(function(){
		$('#banco_todas').load('utf2iso.php?file=plugins/banco/banco_lista.php');
	})
	
	function filter(filter){
		$('#banco_todas').load('utf2iso.php?file=plugins/banco/banco_lista.php',{filter: filter});
	}

	function editar_banco(codigo){
		$('#banco_'+ codigo).load('utf2iso.php?file=plugins/banco/banco_editar.php',{codigo: codigo},function(){
			$('#banco_'+ codigo).slideDown('slow');
			$('#banco_'+ codigo).css({'overflow':'none', 'height': 'auto'});
		});
	}
	<? if($editar){ ?>
	editar_banco(<?= $editar ?>);
	window.location.hash = '#banco_<?= $editar ?>';
	$("input[name='banco']:first", document.forms[0]).focus();
	<? } ?>
	
	function banco_status(codigo,status){
		$.post('utf2iso.php?file=plugins/banco/banco_editar.php',{codigo: codigo, gravar: 'status', status: status});
		$('#banco_'+ codigo).load('utf2iso.php?file=plugins/banco/banco_lista.php',{codigo: codigo});
	}
	
	function ampliar_banco(codigo){
		$("#full_frame").load('utf2iso.php?file=plugins/banco/banco_ampliar.php',{codigo: codigo});
		$("#full_frame").slideDown(function(){
			$("#full_background").fadeIn(500);
		});
	}
	
	function excluir_banco(codigo){
		if(confirm('Deseja mesmo excluir este banco?\nEsta ação é irreversível!')){
		$.post('utf2iso.php?file=plugins/banco/banco_editar.php',{codigo: codigo, gravar: 'excluir'},function(){
			$('#banco_status_<?= $rs[codigo] ?>').hide();
			$('#banco_'+ codigo).slideUp('slow');
			busca_status('O banco foi excluído.');
		});
		}
	}
	
	function onde_banco(codigo){
		$("#content_right").load('utf2iso.php?file=plugins/contas_pagar/contas_pagar_relatorio.php',{banco: codigo, filtro:'banco', order:'cod_banco', tabela: 'banco', campo: 'banco'});
	}

	$('#bt_add_banco').click(function(){
		$('#add_banco').load('utf2iso.php?file=plugins/banco/banco_editar.php',function(){
			$('#bt_add_banco').fadeOut();
			$('#add_banco').slideDown('slow');
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
