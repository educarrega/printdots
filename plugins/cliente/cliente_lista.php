<? 
session_start();
#require_once "../../conn.php";
?>

<? if(!$codigo){
	## RELATORIO INICIAL ##	
	$qr = mysql_query("SELECT Count(ativo) as ativos FROM cliente WHERE ativo=1");
	$at = mysql_fetch_object($qr);
	$ativos = $at->ativos;

	$qr = mysql_query("SELECT Count(ativo) as inativos FROM cliente WHERE ativo<>1");
	$at = mysql_fetch_object($qr);
	$inativos = $at->inativos;
	?>

	<table class="days_top">
	<tr>
	<td class="days_top_1"></td>
	<td class="days_top_2">Clientes</td>
	<td class="days_top_3"></td>
	<td class="days_top_4">&nbsp;</td>
	<td class="days_top_5"></td>
	</tr>
	</table>
	
	<div id="days_middle" class="days_middle suggestionList">
	
	<input id="bt_add_cliente" type="image" src="images/bt_mais.png" title="Incluir novo Cliente" />
	<div id="add_cliente">Adicionar cliente aqui</div>

	<?
	$indice = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,X,Z,W,Y,0,1,2,3,4,5,6,7,8,9,0,-,+,.,?,!,@,#,$,&,*,(,),[,],{,},|,/,<,>";
	$indice = explode(",",$indice);
	?>
	<fieldset class="fieldset_clientes">
	<legend>Índice de Clientes por Letras e Símbolos</legend>
	<div id="indices">
	<?
	foreach($indice as $k=>$v){
		$t = 0;
		$qr = mysql_query("SELECT Count(codigo) as contagem FROM cliente WHERE nome_fantasia LIKE '$v%'");
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
	$campos = 'nome_fantasia,codigo,razao_social,cidade,estado,email,telefone,cnpj,inscrest,inscrmun,cep,endereco,website,email,contato,messenger,obs';
	$campos = explode(',',$campos);
	$wr = "WHERE (";
	for($i=0;$i<count($campos);$i++){
		$wr .= "UPPER(". $campos[$i].") LIKE '%". $busca ."%' OR ";
	}
	$wr = substr($wr,0,strlen($wr)-4).") ORDER BY nome_fantasia";
	$filter = $busca;
}elseif(!$codigo){
	if(!$order) $order = "nome_fantasia";
	if(($ativos+$inativos) >20 && !isset($filter)) $filter = "A";
	if(isset($filter)) $filter_qr = "WHERE UPPER(nome_fantasia) LIKE '". $filter ."%'";
	if(!isset($filter)) $filter_qr = "";
	$wr = "$filter_qr ORDER BY ativo DESC,$order ASC";
}else{
	$wr = "WHERE codigo='$codigo'";
}

$SQL = "SELECT * FROM cliente $wr";
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
	<li<?= $bck ?> id="cliente_<?= $rs[codigo] ?>" onmouseover="controles(<?= $rs[codigo] ?>)">
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
	<img src="images/bt_ampliar.png" onclick="ampliar_cliente('<?= $rs[codigo] ?>')" alt="ampliar" title="Mais detalhes deste cliente" /> 
	<img src="images/bt_producao.png" onclick="os_cliente('<?= $rs[codigo] ?>')" alt="os" title="Ver as Ordens de Serviço deste cliente" /> 
	<img src="images/bt_editar.png" onclick="editar_cliente('<?= $rs[codigo] ?>')" alt="editar" title="Editar este cliente" /> 
	<img src="images/bt_excluir.png" onclick="excluir_cliente('<?= $rs[codigo] ?>')"  alt="excluir" title="Excluir este cliente" />
	</div>
	
	<div class="cliente_coluna_0">
		<div class="producao_busca_indice"><?= $resultados ?></div>
		<?
		echo "<span id='cliente_status_". $rs[codigo] ."'>";
		if($rs[ativo]=="1"){
		echo "<img src='images/bt_check.png' class='cliente_status' title='Cliente Ativo' onclick='cliente_status(". $rs[codigo] .",0)' >";
		}else{
		echo "<img src='images/bt_atencao.png' class='cliente_status' title='Cliente Inativo' onclick='cliente_status(". $rs[codigo] .",1)' >";
		} 
		echo "</span>"; ?>
	</div>
	
	<div class="cliente_coluna_1">
		<div class="cliente_nome_fantasia"><?= $rs[nome_fantasia] ?>&nbsp;</div>
		<div class="cliente_razao_social">
		<?= $rs[razao_social] ?>&nbsp;<br />
		<?= $rs[cnpj] ?>
		</div>
	</div>
	
	<div class="cliente_coluna_2">
	Cliente <img src="images/<? if($rs[tipo_cliente]){ ?>bt_check.png<? }else{ ?>bt_atencao.png<? } ?>" /> 
	| Fornece <img src="images/<? if($rs[tipo_fornecedor]){ ?>bt_check.png<? }else{ ?>bt_atencao.png<? } ?>" /> 
	| Repres. <img src="images/<? if($rs[tipo_representante]){ ?>bt_check.png<? }else{ ?>bt_atencao.png<? } ?>" />
	| Transporte <img src="images/<? if($rs[tipo_transporte]){ ?>bt_check.png<? }else{ ?>bt_atencao.png<? } ?>" />
	<br />

	<?= $rs[cidade] ?> (<?= $rs[estado] ?>) - <?= $rs[cep] ?><br />
	Telefone: <?= ($rs[telefone]) ?><br />
	Contato: <?= ($rs[contato]) ?><br />

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
	$tx_filter = "$filtrados clientes encontrados em \"$filter\"";
	if($filtrados=='1') $tx_filter= "$filtrados cliente encontrado em \"$filter\"";
	if(!$filtrados) $tx_filter = "<img src=\"images/bt_atencao.png\" align=\"absmiddle\" width=\"24\"> Nenhum cliente encontrado em \"$filter\"";
	?>

	<script type="text/javascript">
	busca_status('<?= $tx_filter ?>. Total de <?= $ativos ?> ativos e <?= $inativos ?> inativos.');

	function filter(filter){
		$('#cliente_todas').load('utf2iso.php?file=plugins/cliente/cliente_lista.php',{filter: filter});
	}

	function editar_cliente(codigo){
		$('#cliente_'+ codigo).load('utf2iso.php?file=plugins/cliente/cliente_editar.php',{codigo: codigo},function(){
			$('#cliente_'+ codigo).slideDown('slow');
			$('#cliente_'+ codigo).css({'overflow':'none', 'height': 'auto'});
		});
	}
	<? if($editar){ ?>
	editar_cliente(<?= $editar ?>);
	window.location.hash = '#cliente_<?= $editar ?>';
	$("input[name='nome_fantasial']:first", document.forms[0]).focus();
	<? } ?>
	
	function cliente_status(codigo,status){
		$.post('utf2iso.php?file=plugins/cliente/cliente_editar.php',{codigo: codigo, gravar: 'status', status: status});
		$('#cliente_'+ codigo).load('utf2iso.php?file=plugins/cliente/cliente_lista.php',{codigo: codigo});
	}
	
	function ampliar_cliente(codigo){
		$("#full_frame").load('utf2iso.php?file=plugins/cliente/cliente_os.php',{agencia: codigo});
		$("#full_frame").slideDown(function(){
			$("#full_background").fadeIn(500);
		});
	}
	
	function excluir_cliente(codigo){
		if(confirm('Deseja mesmo excluir este cliente?\nEsta ação é irreversível!')){
		$.post('utf2iso.php?file=plugins/cliente/cliente_editar.php',{codigo: codigo, gravar: 'excluir'},function(){
			$('#cliente_status_<?= $rs[codigo] ?>').hide();
			$('#cliente_'+ codigo).slideUp('slow');
			busca_status('O cliente foi excluído.');
		});
		}
	}
	
	function os_cliente(codigo){
		$("#content_right").load('utf2iso.php?file=plugins/producao/producao.php',{agencia: codigo});
	}

	$('#bt_add_cliente').click(function(){
		$('#add_cliente').load('utf2iso.php?file=plugins/cliente/cliente_editar.php',function(){
			$('#bt_add_cliente').fadeOut();
			$('#add_cliente').slideDown('slow');
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
