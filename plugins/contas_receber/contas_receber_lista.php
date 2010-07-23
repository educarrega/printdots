<? 
session_start();
#require_once "../../conn.php";

	?>
	<table class="days_top">
	<tr>
	<td class="days_top_1"></td>
	<td class="days_top_2 days_top_click" title="Recarrega a Lista">Contas Ã  Receber</td>
	<td class="days_top_3"></td>
	<td class="days_top_4">&nbsp;</td>
	<td class="days_top_5"></td>
	</tr>
	</table>

	
	
	<div id="days_middle" class="days_middle suggestionList">
		
	<fieldset>
	<legend><span id="order_<?= $codigo ?>" class="order_img"></span> Índice por Anos e Meses</legend>
	
	
	<div id="indices">
	<div id="contas_receber_years">
	<?
	
	if(!$year) $year = date("Y");
	if(!$month) $month = date("m");

	
	$SQLy = "SELECT YEAR( data_vencimento ) AS year, COUNT( codigo ) AS registros
	FROM contas_receber
	GROUP BY YEAR( data_vencimento )
	";
	$QRy = mysql_query($SQLy);
	while($ry = mysql_fetch_array($QRy)){
            if(strlen($ry[year])==4) { ?>
		<a href="#<?= $ry[year]?>" onclick="filter('<?= $ry[year] ?>','<?= date(m) ?>')" class="<? if(trim($ry[year])==trim($year)) echo "selected" ?>">
		<?= $ry[year]?><br />
		<small>(<?= $ry[registros] ?>)</small>
		</a>
	<?  } #count
        }# while ?>
	</div>
	
	<div class="clear_left"></div>
	
	<?
	$SQLm = "SELECT month( data_vencimento ) as month , COUNT( codigo ) as registros 
	FROM contas_receber
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
        
        <input onclick="contas_receber_escolha()" style="position: absolute; top: 127px; right: 50px; width: 32px;" type="image" src="images/bt_relatorio_financeiro.png" value="OK" title="Alternar Modo de Lista / RelatÃ³rio">
            
	<input onclick="print_relatorio()" style="position: absolute; top: 127px; right: 100px; width: 32px;" type="image" src="images/bt_impressoras.png" value="OK" title="Imprimir um relatÃ³rio com os resultados obtidos">
            
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
	$QR_total = mysql_query("SELECT SUM(valor) as total FROM contas_receber WHERE YEAR( data_vencimento ) = '$year' AND MONTH( data_vencimento ) = '$month'");
	$mes_total = mysql_fetch_array($QR_total);
	
	#mes_aberto
	$QR_aberto = mysql_query("SELECT SUM(valor) as total FROM contas_receber WHERE YEAR( data_vencimento ) = '$year' AND MONTH( data_vencimento ) = '$month' AND data_pagamento='0000-00-00' AND data_vencimento >= '". date("Y-m-d") ."'");
	$mes_aberto = mysql_fetch_array($QR_aberto);
	
	#mes_atraso
	$QR_atraso = mysql_query("SELECT SUM(valor) as total FROM contas_receber WHERE YEAR( data_vencimento ) = '$year' AND MONTH( data_vencimento ) = '$month' AND data_pagamento='0000-00-00' AND data_vencimento < '". date("Y-m-d") ."'");
	$mes_atraso = mysql_fetch_array($QR_atraso);
	
	#mes_pago
	$QR_pago = mysql_query("SELECT SUM(valor) as total FROM contas_receber WHERE YEAR( data_vencimento ) = '$year' AND MONTH( data_vencimento ) = '$month' AND data_pagamento!='0000-00-00'");
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
	</fieldset>
	<? }#busca ?>
	
        
        
	<div class="clear_left">&nbsp;</div>
	</fieldset>
	<hr />
	
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
	
	#cliente
	$SQL_cliente = "SELECT codigo FROM cliente WHERE
	nome_fantasia LIKE '%$busca%'
	OR cidade LIKE '%$busca%'
	AND tipo_cliente='1'
        ORDER BY codigo DESC
        LIMIT 150";
	$QR_cliente = mysql_query($SQL_cliente);
	while($rsf = mysql_fetch_array($QR_cliente)){
		if($codigo_cliente) $codigo_cliente .= ",";
		$codigo_cliente .= "'$rsf[codigo]'";
	}#echo $codigo_cliente.$SQL_cliente;
	
	#transporte
	$SQL_transporte = "SELECT codigo FROM cliente WHERE
	nome_fantasia LIKE '%$busca%'
	OR cidade LIKE '%$busca%'
	AND tipo_transporte='1'
        ORDER BY codigo DESC";
	$QR_transporte = mysql_query($SQL_transporte);
	while($rst = mysql_fetch_array($QR_transporte)){
		if($codigo_transporte) $codigo_transporte .= ",";
		$codigo_transporte .= "'$rst[codigo]'";
	}#echo $codigo_transporte.$SQL_transporte;
	
	#banco
	$SQL_banco = "SELECT codigo FROM banco WHERE
	banco LIKE '%$busca%'
	OR conta_numero LIKE '%$busca%'
	AND ativo='1'
        ORDER BY codigo DESC";
	$QR_banco = mysql_query($SQL_banco);
	while($rsb = mysql_fetch_array($QR_banco)){
		if($codigo_banco) $codigo_banco .= ",";
		$codigo_banco .= "'$rsb[codigo]'";
	}#echo $codigo_banco.$SQL_banco;
	
	#carteira
	$SQL_carteira = "SELECT codigo FROM carteira WHERE
	titulo LIKE '%$busca%'
	AND ativo='1'
        ORDER BY codigo DESC";
	$QR_carteira = mysql_query($SQL_carteira);
	while($rsc = mysql_fetch_array($QR_carteira)){
		if($codigo_carteira) $codigo_carteira .= ",";
		$codigo_carteira .= "'$rsc[codigo]'";
	}#echo $codigo_carteira.$SQL_carteira;
	

	#produto
	$SQL_produto = "SELECT codigo FROM produto WHERE
	titulo LIKE '%$busca%'
	OR descricao LIKE '%$busca%'
	AND ativo='1'
        ORDER BY codigo DESC";
	$QR_produto = mysql_query($SQL_produto);
	while($rsd = mysql_fetch_array($QR_produto)){
		if($codigo) $codigo_produto .= ",";
		$codigo_produto .= "'$rsd[codigo]'";
	}#echo $codigo_produto.$SQL_produto;
	
        
        #producao
	$SQL_producao = "SELECT codigo FROM producao WHERE
        titulo_servico LIKE '%$busca%'
        OR codigo LIKE '%$busca%'
		OR orcamento_obs LIKE '%$busca%'
		OR digital_obs LIKE '%$busca%'
        OR entrega_obs LIKE '%$busca%'
        OR email_vencimento LIKE '%$busca%'
        OR data_prevista LIKE '%". mydate($busca) ."%'
        OR data_entrega LIKE '%". mydate($busca) ."%' ";
        if($codigo_transporte) $SQL_producao .= " OR cod_transporte IN ($codigo_transporte) ";
	$SQL_producao .= "ORDER BY codigo DESC
        LIMIT 5000";
	$QR_producao = mysql_query($SQL_producao);
	while($rsp = mysql_fetch_array($QR_producao)){
		if($codigo_producao) $codigo_producao .= ",";
		$codigo_producao .= "'$rsp[codigo]'";
	}#echo $codigo_producao.$SQL_producao;
	
        
        #producao_itens
	$SQL_producao_itens = "SELECT codigo_producao FROM producao_itens WHERE
        descricao LIKE '%$busca%'";
	if($codigo_equipamento) $SQL_producao_itens .= "OR codigo_equipamento IN ($codigo_equipamento) ";
        if($codigo_produto) $SQL_producao_itens .= "OR codigo_produto IN ($codigo_produto) ";
        $SQL_producao_itens .= " ORDER BY codigo DESC
        LIMIT 5000";
	$QR_producao_itens = mysql_query($SQL_producao_itens);
	while($rsi = mysql_fetch_array($QR_producao_itens)){
		if($codigo_producao) $codigo_producao .= ",";
		$codigo_producao .= "'$rsi[codigo_producao]'";
	}#echo $SQL_producao_itens.$codigo_producao;
	
	
	#contas_receber
	$wr = "WHERE (";
	if($codigo_cliente) $wr .= "codigo_cliente IN ($codigo_cliente) OR ";
	if($codigo_producao) $wr .= "codigo_producao IN ($codigo_producao) OR ";
	if($codigo_banco) $wr .= "codigo_banco IN ($codigo_banco)  OR ";
	if($codigo_carteira) $wr .= "codigo_carteira IN ($codigo_carteira) OR ";
	$wr .= "documento LIKE '%$busca%'
	OR valor LIKE '%$busca%' 
	OR data_pagamento LIKE '%$busca%' 
	OR data_vencimento LIKE '%$busca%' 
	OR obs LIKE '%$busca%') 
	$wr_data"; 
	if(!$todosmeses) $wr .=" AND YEAR( data_vencimento ) = '$year' ";
	if(!$todosmeses) $wr .=" AND MONTH( data_vencimento ) = '$month' ";
	$wr .= " ORDER BY data_vencimento $_SESSION[ASCDESC]";
	
	$filter = $busca;
	
}elseif(!$codigo){
	
	#botoes de ordem
	$order = "data_vencimento";
	$order_by = "ORDER BY $order $_SESSION[ASCDESC]";
	
	$wr = "WHERE YEAR( data_vencimento ) = '$year' AND MONTH( data_vencimento ) = '$month' $wr_data $order_by";
}else{
	$wr = "WHERE codigo='$codigo'";
}

$SQL = "SELECT * FROM contas_receber $wr";
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
	
	<li<?= $bck ?> id="contas_receber_<?= $rs[codigo] ?>" onmouseover="controles(<?= $rs[codigo] ?>)">
		
	<div id="bt_controles_<?= $rs[codigo] ?>" class="bt_controles">
	<img src="images/bt_producao.png" onclick="producao_os('<?= $rs[codigo_producao] ?>')" alt="" title="Ver esta produção" /> 
	<img src="images/bt_contas_receber.png" onclick="contas_receber_editar_os('<?= $rs[codigo_producao] ?>','<?= $rs[codigo_cliente] ?>','<?= $rs[codigo]?>')" alt="" title="Quitar vencimento" />
        <img src="images/bt_impressoras.png" onclick="print_os('<?= $rs[codigo_producao] ?>')" alt="" title="Tela de Impressão desta Produção" />
	</div>
	
	<? $bck = "";
	if($rs[data_pagamento]=='0000-00-00' && $rs[data_vencimento] < date("Y-m-d")) $bck = "background: #FF0000; color:#FFF";
	if($rs[data_pagamento]!='0000-00-00') $bck = "background: #004455;";
	?>
        	<?
		$SQLcr = "SELECT titulo FROM carteira WHERE codigo='$rs[codigo_carteira]'";
		$QRcr = mysql_query($SQLcr);
		$rcr = mysql_fetch_array($QRcr);
		
		$SQLb = "SELECT banco FROM banco WHERE codigo='$rs[codigo_banco]'";
		$QRb = mysql_query($SQLb);
		$rb = mysql_fetch_array($QRb);
                
                $SQLp = "SELECT codigo,titulo_servico,autorizado_data FROM producao WHERE codigo='$rs[codigo_producao]'";
		$QRp = mysql_query($SQLp);
		$rp = mysql_fetch_array($QRp);
		?>
	<div class="contas_receber_coluna_0">
		<div class="producao_busca_indice" style="<?= $bck ?>"><?= mydate($rs[data_vencimento]) ?></div>
		<small>Lanc <?= mydate($rp[autorizado_data]) ?></small>
	</div>
	<?
	$SQLc = "SELECT codigo,nome_fantasia,cidade,estado,telefone FROM cliente WHERE codigo='$rs[codigo_cliente]'";
	$QRc = mysql_query($SQLc);
	$rc = mysql_fetch_array($QRc);
	?>
	<div class="contas_receber_coluna_1">
		<div class="contas_receber_titulo">R$ <?= $rs[valor] ?></div>
		<div class="contas_receber_descricao">
		<?= $rc[nome_fantasia] ?><br />
		<small><?= $rc[cidade] ?> <?= $rc[estado] ?> <?= $rc[telefone] ?>
		</div></small>
	</div>
	
	<div class="contas_receber_coluna_2">
                <b><?= $rp[codigo] ?> - <?= $rp[titulo_servico] ?></b><br />
		Carteira: <?= $rs[codigo_carteira] ?> - <?= $rcr[titulo] ?> | <?= $rs[codigo_banco] ?> - <?= $rb[banco] ?><br />
		Documento: <?= $rs[documento] ?>
	</div>
			
	<div class="clear_left"></div>
	
	</li>

	
<? } # while ?>


	</div>

	<div id="days_stats_top_<?= $data_prevista ?>" class="days_stats_top"></div>

	<div id="days_stats_middle_<?= $data_prevista ?>" class="days_stats_middle"></div>

	<div id="days_stats_bottom_<?= $data_prevista ?>" class="days_stats_bottom"></div>
        
        <form name="print">
            <input type="hidden" name="consulta" value="<?= $SQL ?>" />
        </form>


	<?
	if($contagem>'1') $tx_filter = "$contagem Contas Ã  Receber encontradas";
	if($contagem=='1') $tx_filter= "$contagem Conta Ã  Receber encontrada";
	if(!$contagem) $tx_filter = "<img src=\"images/bt_atencao.png\" align=\"absmiddle\" width=\"24\"> Nenhuma Conta Ã  Receber encontrada";
	if($busca) $tx_filter = "$tx_filter em \"". $busca ."\"";
	?>

	<script type="text/javascript">
	busca_status('<?= $tx_filter ?>');
	
	$('#order_<?= $codigo ?>').html('<?= $_ascdesc ?>');
	
	$('.days_top_2').click(function(){
		$('#contas_receber_todas').load('utf2iso.php?file=plugins/contas_receber/contas_receber_lista.php');
	})
	
	function filter(year,month){
		var busca = encodeURI($('#inputString').val());
		var status = $('input[type=radio][name=status]:checked').val();
		var todosmeses = $('input[type=radio][name=todosmeses]:checked').val();
		$('#contas_receber_todas').load('utf2iso.php?file=plugins/contas_receber/contas_receber_lista.php',{year: year, month: month, status: status, busca: busca, todosmeses: todosmeses});
	}
	
	function contas_receber_editar_os(codigo_producao,codigo_cliente,codigo_parcela){
		$("#full_frame").load('utf2iso.php?file=plugins/contas_receber/contas_receber_editar_os.php', {codigo_producao: codigo_producao, codigo: codigo_parcela, codigo_cliente: codigo_cliente, view: '1'});
		$("#full_frame").slideDown(function(){
			$("#full_background").fadeIn(500,function(){
				$("#full_background").click(function(){
					filter('<?= $year ?>','<?= $month ?>');	
				})
			});
		});
	}
	
        function producao_os(codigo_producao){
		$("#full_frame").load("utf2iso.php?file=plugins/ordem_de_servico/editar_os2.php",{codigo:codigo_producao, target: ''});
		$("#full_frame").slideDown(function(){
			$("#full_background").fadeIn(500,function(){
				$("#full_background").click(function(){
					filter('<?= $year ?>','<?= $month ?>');	
				})
			});
		});
	}
        
	
        function print_relatorio(){
            open('',"receber_print_<?= $uid ?>");
            with(document.print)
            {
            method = "POST";
            action = "plugins/contas_receber/contas_receber_relatorio_print.php";
            target = "receber_print_<?= $uid ?>";
            submit();
            }
        }
        
        alter = 1;
        function contas_receber_escolha(){
            if(alter == 1){
                alter = 0;
                $("#contas_receber_relatorio").show();
                $("#contas_receber_todas").hide();
            }else{
                alter = 1;
                $("#contas_receber_relatorio").hide();
                $("#contas_receber_todas").show();
            }
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
