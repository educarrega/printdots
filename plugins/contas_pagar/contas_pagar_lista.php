<? 
session_start();
#require_once "../../conn.php";

	?>
	<table class="days_top">
	<tr>
	<td class="days_top_1"></td>
	<td class="days_top_2 days_top_click" title="Recarrega a Lista">Contas Ã  Pagar</td>
	<td class="days_top_3"></td>
	<td class="days_top_4">&nbsp;</td>
	<td class="days_top_5"></td>
	</tr>
	</table>

	
	
	<div id="days_middle" class="days_middle suggestionList">
		
	<fieldset class="fieldset_calendario">
	<legend>Índice por Anos e Meses</legend>
	<div id="order_<?= $codigo ?>" class="order_img"></div>
	
	<div id="indices">
	<div id="contas_pagar_years">
	<?
	
	if(!$year) $year = date("Y");
	if(!$month) $month = date("m");

	
	$SQLy = "SELECT YEAR( data_vencimento ) AS year, COUNT( codigo ) AS registros
	FROM contas_pagar
	GROUP BY YEAR( data_vencimento )
	";
	$QRy = mysql_query($SQLy);
	while($ry = mysql_fetch_array($QRy)){ ?>
		<a href="#<?= $ry[year]?>" onclick="filter('<?= $ry[year] ?>','<?= date(m) ?>')" class="<? if(trim($ry[year])==trim($year)) echo "selected" ?>">
		<?= $ry[year]?><br />
		<small>(<?= $ry[registros] ?>)</small>
		</a>
	<? } ?>
	</div>
	
	<div class="clear_left"></div>
	
	<?
	$SQLm = "SELECT month( data_vencimento ) as month , COUNT( codigo ) as registros 
	FROM contas_pagar
	WHERE YEAR( data_vencimento ) = $year 
	GROUP BY month( data_vencimento )";
	#echo $SQLm;
	$QRm = mysql_query($SQLm);
	while($rm = mysql_fetch_array($QRm)){?>
		<a class="<? if(trim($rm[month])==trim($month)) echo "selected" ?>" href="#<?= $rm[month] ?>" onclick="filter('<?= $year ?>','<?= $rm[month] ?>')">
		<?= mymonth($rm[month]) ?><br /> 
		<small>(<?= $rm[registros] ?>)</small>
		</a>
	<? } ?>	
	
	<input type="hidden" id="year" value="<?= $year ?>" />
	<input type="hidden" id="month" value="<?= $month ?>" />
		
	<div style="float:right">
	<input type="radio" name="status" value="pendentes" <? if($status=='pendentes') echo checked ?> onclick="filter('<?= $year ?>','<?= $month ?>')" />Pendentes<br />
	<input type="radio" name="status" value="quitadas" <? if($status=='quitadas') echo checked ?> onclick="filter('<?= $year ?>','<?= $month ?>')" /> Quitadas<br />
	<input type="radio" name="status" value="" <? if($status=='' || !$status) echo checked ?> onclick="filter('<?= $year ?>','<?= $month ?>')" /> Todas<br />
	</div>
	
	<? if($busca){ ?>
	<div class="clear_left">&nbsp;</div>
	<div align="left">
	<input type="radio" name="todosmeses" value="1" <? if($todosmeses) echo checked ?> onclick="filter('<?= $year ?>','<?= $month ?>')" /> Buscar em todos os meses<br />
	<input type="radio" name="todosmeses" value="0" <? if($todosmeses=='0' || !$todosmeses) echo checked ?> onclick="filter('<?= $year ?>','<?= $month ?>')" /> Somente selecionado
	</div>
	<? } ?>
	
	</div>
	<div class="clear_left">&nbsp;</div>
	
	<?
	if(!$busca){
	#para o resumo mensal
	#mes_total
	$QR_total = mysql_query("SELECT SUM(valor) as total FROM contas_pagar WHERE YEAR( data_vencimento ) = '$year' AND MONTH( data_vencimento ) = '$month'");
	$mes_total = mysql_fetch_array($QR_total);
	
	#mes_aberto
	$QR_aberto = mysql_query("SELECT SUM(valor) as total FROM contas_pagar WHERE YEAR( data_vencimento ) = '$year' AND MONTH( data_vencimento ) = '$month' AND data_pagamento='0000-00-00' AND data_vencimento >= '". date("Y-m-d") ."'");
	$mes_aberto = mysql_fetch_array($QR_aberto);
	
	#mes_atraso
	$QR_atraso = mysql_query("SELECT SUM(valor) as total FROM contas_pagar WHERE YEAR( data_vencimento ) = '$year' AND MONTH( data_vencimento ) = '$month' AND data_pagamento='0000-00-00' AND data_vencimento < '". date("Y-m-d") ."'");
	$mes_atraso = mysql_fetch_array($QR_atraso);
	
	#mes_pago
	$QR_pago = mysql_query("SELECT SUM(valor) as total FROM contas_pagar WHERE YEAR( data_vencimento ) = '$year' AND MONTH( data_vencimento ) = '$month' AND data_pagamento!='0000-00-00'");
	$mes_pago = mysql_fetch_array($QR_pago);
	
	?>        
        <fieldset class="fieldset">
	<legend>Resumo Mensal</legend>
	<?
	#calculos de porcentagem
	$total = $mes_total[total];
	$aberto = @number_format($mes_aberto[total]*100/$total,0);
	$atraso = @number_format($mes_atraso[total]*100/$total,0);
	$pago = @number_format($mes_pago[total]*100/$total,0);
        
        if($mes_aberto[total] && $aberto<1) $aberto = 1;
        if($mes_atraso[total] && $atraso<1) $atraso = 1;
        if($pago<1) $pago = 1;
        
        if(!$mes_aberto[total] && !$mes_atraso[total]){
            $diferenca = @number_format(($total-$mes_pago[total])*100/$total,0,'','');
        }
	?>
	
	<img style="float: right; padding: 0 5px 7px 0;" src="http://chart.apis.google.com/chart?cht=p3&chd=t:<?
        if($aberto) echo "$aberto,";
        if($atraso) echo "$atraso,";
        if($diferenca) echo "$diferenca,";
        echo "$pago";
        ?>&chs=250x100&chco=000000&chf=bg,s,CCCCCC00|c,s,00000000&chl=<?
        if($aberto) echo "Aberto|";
        if($atraso) echo "Atraso|";
        if($diferenca) echo "Diferença|";
        echo "Pago";
        ?>" />
	<br />
            
        <? if($aberto){ ?> 
	Aberto:         <b>R$ <?= number_format($mes_aberto[total],2,',','.') ?></b>            <?= $aberto ?>% <br />
        <? } ?>
        <? if($atraso){ ?>
	Atraso:       <b>R$ <?= number_format($mes_atraso[total],2,',','.') ?></b>            <?= $atraso ?>% <br />
        <? } ?>
        <? if($pago){ ?>
	Pago:           <b>R$ <?= number_format($mes_pago[total],2,',','.') ?></b>              <?= $pago ?>% <br />
        <? } ?>
        <? if($diferenca){ ?>
        Diferença de caixa: <b>R$ <?= number_format($total-$mes_pago[total],2,',','.') ?></b>   <?= $diferenca ?>%  <br />
        <? } ?>
	<legend>Total:  <b>R$ <?= number_format($mes_total[total],2,',','.') ?></b></legend>
	</fieldset><br>
	
	<hr />
	<? }#busca ?>
        
<? 
if($_SESSION[ASCDESC]=="DESC") {
	$_SESSION[ASCDESC] = "ASC";
	$_ascdesc = '<img src="images/bt_acima.png" alt="" title="crescente">';
}else{
	$_SESSION[ASCDESC] = "DESC";
	$_ascdesc = '<img src="images/bt_abaixo.png" alt="" title="decrescente">';
}

if($status=='pendentes'){
	$wr_data = " AND data_pagamento='0000-00-00'";
}elseif($status=="quitadas"){
	$wr_data = " AND data_pagamento!='0000-00-00'";
}
	
if($busca){
	$busca = strtoupper(acento(urldecode($busca)));
	if(strstr($busca,'/')) $busca = mydate($busca);
	
	#fornecedor
	$SQL_fornecedor = "SELECT codigo FROM cliente WHERE
	nome_fantasia LIKE '%$busca%'
	OR cidade LIKE '%$busca%'
	AND tipo_fornecedor='1'";
	$QR_fornecedor = mysql_query($SQL_fornecedor);
	while($rsf = mysql_fetch_array($QR_fornecedor)){
		if($codigo_fornecedor) $codigo_fornecedor .= ",";
		$codigo_fornecedor .= "'$rsf[codigo]'";
	}#echo $codigo_fornecedor.$SQL_fornecedor;
	
	#transporte
	$SQL_transporte = "SELECT codigo FROM cliente WHERE
	nome_fantasia LIKE '%$busca%'
	OR cidade LIKE '%$busca%'
	AND tipo_transporte='1'";
	$QR_transporte = mysql_query($SQL_transporte);
	while($rst = mysql_fetch_array($QR_transporte)){
		if($codigo_transporte) $codigo_transporte .= ",";
		$codigo_transporte .= "'$rst[codigo]'";
	}#echo $codigo_transporte;
	
	#banco
	$SQL_banco = "SELECT codigo FROM banco WHERE
	banco LIKE '%$busca%'
	OR conta_numero LIKE '%$busca%'
	OR agencia_numero LIKE '%$busca%'
	AND ativo='1'";
	$QR_banco = mysql_query($SQL_banco);
	while($rsb = mysql_fetch_array($QR_banco)){
		if($codigo_banco) $codigo_banco .= ",";
		$codigo_banco .= "'$rsb[codigo]'";
	}#echo $codigo_banco;
	
	#carteira
	$SQL_carteira = "SELECT codigo FROM carteira WHERE
	titulo LIKE '%$busca%'
	AND ativo='1'";
	$QR_carteira = mysql_query($SQL_carteira);
	while($rsc = mysql_fetch_array($QR_carteira)){
		if($codigo_carteira) $codigo_carteira .= ",";
		$codigo_carteira .= "'$rsc[codigo]'";
	}#echo $codigo_carteira;
	
	#material
	$SQL_material = "SELECT codigo FROM material WHERE
	titulo LIKE '%$busca%'
	OR descricao LIKE '%$busca%'
	AND ativo='1'";
	$QR_material = mysql_query($SQL_material);
	while($rsm = mysql_fetch_array($QR_material)){
		if($codigo_material) $codigo_material .= ",";
		$codigo_material .= "'$rsm[codigo]'";
	}#echo $codigo_material;
	
	#pedido
	$SQL_pedido = "SELECT codigo FROM pedido WHERE ";
	if($codigo_fornecedor) $SQL_pedido .= "cliente_codigo IN ($codigo_fornecedor) OR ";
	if($codigo_transporte) $SQL_pedido .= "transporte_codigo IN ($codigo_transporte) OR ";
	if($codigo_material) $SQL_pedido .= "material_codigo IN ($codigo_material) OR ";
	if(strstr($SQL_pedido," OR ")){
		$SQL_pedido = substr($SQL_pedido,0,strlen($SQL_pedido)-4)." AND ativo='1'";
		#echo $SQL_pedido;
		$QR_pedido = mysql_query($SQL_pedido);
		while($rsp = mysql_fetch_array($QR_pedido)){
			if($codigo_pedido) $codigo_pedido .= ",";
			$codigo_pedido .= "'$rsp[codigo]'";
		}
	}#echo $codigo_pedido;
	
	#contas_receber
	$wr = "WHERE (";
	if($codigo_fornecedor) $wr .= "codigo_cliente IN ($codigo_fornecedor) OR ";
	if($codigo_transporte) $wr .= "codigo_cliente IN ($codigo_transporte) OR ";
	if($codigo_pedido) $wr .= "codigo_pedido IN ($codigo_pedido) OR ";
	if($codigo_banco) $wr .= "codigo_banco IN ($codigo_banco)  OR ";
	if($codigo_carteira) $wr .= "codigo_carteira IN ($codigo_carteira) OR ";
	$wr .= "documento LIKE '%$busca%'
	OR valor_acrescimo LIKE '%$busca%' 
	OR valor LIKE '%$busca%' 
	OR data_pagamento LIKE '%$busca%' 
	OR data_vencimento LIKE '%$busca%' 
	OR data_lancamento LIKE '%$busca%' 
	OR descricao LIKE '%$busca%') 
	$wr_data
	AND YEAR( data_vencimento ) = '$year' ";
	if(!$todosmeses) $wr .=" AND MONTH( data_vencimento ) = '$month'";
	$wr .= "ORDER BY data_vencimento $_SESSION[ASCDESC]";
	
	$filter = $busca;
	
}elseif(!$codigo){
	
	#botoes de ordem
	$order = "data_vencimento";
	$order_by = "ORDER BY $order $_SESSION[ASCDESC]";
	
	$wr = "WHERE YEAR( data_vencimento ) = '$year' AND MONTH( data_vencimento ) = '$month' $wr_data $order_by";
}else{
	$wr = "WHERE codigo='$codigo'";
}

$SQL = "SELECT * FROM contas_pagar $wr";
#echo $SQL;
$QR = mysql_query($SQL);
$contagem = mysql_num_rows($QR);
while($rs = mysql_fetch_array($QR)){ 
	
	if($alter==0){
		$alter = 1;
		$bck = " class='alter'";
	}else{
		$alter = 0;
		$bck = "";
	} ?>
	
	<li<?= $bck ?> id="contas_pagar_<?= $rs[codigo] ?>" onmouseover="controles(<?= $rs[codigo] ?>)">
		
	<div id="bt_controles_<?= $rs[codigo] ?>" class="bt_controles">
	<img src="images/bt_ampliar.png" onclick="pedido_print('<?= $rs[codigo_pedido] ?>')" alt="" title="Mais detalhes do pedido" /> 
	<img src="images/bt_contas_pagar.png" onclick="pedido_lancar('<?= $rs[codigo_pedido] ?>','<?= $rs[codigo]?>')" alt="" title="Quitar vencimento" />
	</div>
	
	<? $bck = "";
	if($rs[data_pagamento]=='0000-00-00' && $rs[data_vencimento] < date("Y-m-d")) $bck = "background: #FF0000; color:#FFF";
	if($rs[data_pagamento]!='0000-00-00') $bck = "background: #004455;";
	?>
	<div class="contas_pagar_coluna_0">
		<div class="producao_busca_indice" style="<?= $bck ?>"><?= mydate($rs[data_vencimento]) ?></div>
		<small>Lanc <?= mydate($rs[data_lancamento]) ?></small>
		
	</div>
	<?
	$SQLc = "SELECT codigo,nome_fantasia,cidade,estado,telefone FROM cliente WHERE codigo='$rs[codigo_cliente]'";
	$QRc = mysql_query($SQLc);
	$rc = mysql_fetch_array($QRc);
	?>
	<div class="contas_pagar_coluna_1">
		<div class="contas_pagar_titulo">R$ <?= $rs[valor] ?></div>
		<div class="contas_pagar_descricao">
		<?= $rc[nome_fantasia] ?><br />
		<small><?= $rc[cidade] ?> <?= $rc[estado] ?> <?= $rc[telefone] ?>
		</div></small>
	</div>
	
	<div class="contas_pagar_coluna_2">
		<?
		$SQLc = "SELECT titulo FROM carteira WHERE codigo='$rs[codigo_carteira]'";
		$QRc = mysql_query($SQLc);
		$rc = mysql_fetch_array($QRc);
		
		$SQLb = "SELECT banco FROM banco WHERE codigo='$rs[codigo_banco]'";
		$QRb = mysql_query($SQLb);
		$rb = mysql_fetch_array($QRb);
		?>
		Sequência: <?= $rs[sequencia_carteira] ?><br />
		Carteira: <?= $rs[codigo_carteira] ?> - <?= $rc[titulo] ?> | <?= $rs[codigo_banco] ?> - <?= $rb[banco] ?><br />
		Documento: <?= $rs[documento] ?>
	</div>
			
	<div class="clear_left"></div>
	
	</li>

	
<? } # while ?>


	</div>

	<div id="days_stats_top_<?= $data_prevista ?>" class="days_stats_top"></div>

	<div id="days_stats_middle_<?= $data_prevista ?>" class="days_stats_middle"></div>

	<div id="days_stats_bottom_<?= $data_prevista ?>" class="days_stats_bottom"></div>


	<?
	if($contagem>'1') $tx_filter = "$contagem Contas Ã  Pagar encontradas";
	if($contagem=='1') $tx_filter= "$contagem Conta Ã  Pagar encontrada";
	if(!$contagem) $tx_filter = "<img src=\"images/bt_atencao.png\" align=\"absmiddle\" width=\"24\"> Nenhuma Conta Ã  Pagar encontrada";
	if($busca) $tx_filter = "$tx_filter em \"$busca\"";
	?>

	<script type="text/javascript">
	busca_status('<?= $tx_filter ?>');
	
	$('#order_<?= $codigo ?>').html('<?= $_ascdesc ?>');
	
	$('.days_top_2').click(function(){
		$('#contas_pagar_todas').load('utf2iso.php?file=plugins/contas_pagar/contas_pagar_lista.php');
	})
	
	function filter(year,month){
		var busca = encodeURI($('#inputString').val());
		var status = $('input[type=radio][name=status]:checked').val();
		var todosmeses = $('input[type=radio][name=todosmeses]:checked').val();
		$('#contas_pagar_todas').load('utf2iso.php?file=plugins/contas_pagar/contas_pagar_lista.php',{year: year, month: month, status: status, busca: busca, todosmeses: todosmeses});
	}
	
	function pedido_lancar(codigo_pedido,codigo_parcela){
		$("#full_frame").load('utf2iso.php?file=plugins/pedido/pedido_lancar.php',{codigo: codigo_pedido,codigo_parcela:codigo_parcela});
		$("#full_frame").slideDown(function(){
			$("#full_background").fadeIn(500,function(){
				$("#full_background").click(function(){
					filter('<?= $year ?>','<?= $month ?>');	
				})
			});
		});
	}
	
	function pedido_print(codigo){
		win = open("plugins/pedido/pedido_print.php?codigo="+codigo,"pedido_print_"+codigo);
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
