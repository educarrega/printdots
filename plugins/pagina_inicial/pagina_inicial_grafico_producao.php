<?php
@session_start();
#require_once "../../conn.php";
?>
    <label>No Dia</label>
    <ul class="recursos">
	<?
	#montar os dias passados
	$SQL_data = "SELECT data_prevista FROM producao WHERE (status!='' AND status!='1') AND TO_DAYS(NOW()) - TO_DAYS(data_prevista) <= 30 GROUP BY data_prevista ORDER BY data_prevista DESC LIMIT 9";
	$QR_data = mysql_query($SQL_data);
	while($rs_data = mysql_fetch_array($QR_data)){
	    $data[] = $rs_data[data_prevista];
	}
	asort($data);
	foreach($data as $k=>$v){
	    $dia = explode("-",$v);
            ?>
	    <a class="<? if(trim($selecionado) == trim($v)) echo "dia_selecionado" ?>" href="javascript:grafico_producao('<?= $v ?>')"><?= $dia[2] ?></a>
	<? }
	
	#consulta primaria
	$SQL_grafico = "SELECT COUNT( codigo ) AS qt, status 
			FROM  `producao` 
			WHERE data_prevista =  '$selecionado'
			GROUP BY  `status`
			";
                        #echo $SQL_grafico;
	$QR_grafico = mysql_query($SQL_grafico);
	while($rsg = mysql_fetch_array($QR_grafico)){
	    #$grafico_status[] = $rsg[status];
	    $grafico_qt[] = $rsg[qt];
	    $grafico_total = ($rsg[qt]+$grafico_total);
	    $QR_titulo = mysql_query("SELECT titulo FROM producao_status WHERE codigo=$rsg[status]");
	    $rst = mysql_fetch_array($QR_titulo);
	    $grafico_titulo[] = $rst[titulo];
            $grafico_status[] = $rsg[status];
	}
	
	?>
        <hr style="border:0;" />
        <?
	#calculos de porcentagem
	for($k=0;$k<count($grafico_titulo);$k++){
	    $grafico_prc[$k] = @number_format($grafico_qt[$k]*100/$grafico_total,0);
	    if($grafico_qt[$k]<1 && $grafico_qt[$k]>0) $grafico_prc[$k] = 1;
	    #echo "$grafico_titulo[$k]: $grafico_qt[$k] - $grafico_prc[$k]%<br />"; ?>
            <div>
                <img src="plugins/producao/images/status_<?= $grafico_status[$k] ?>.png">
                <?= $grafico_titulo[$k] ?>: <b><?= $grafico_qt[$k] ?></b> (<?= $grafico_prc[$k] ?>%)
            </div>	
        <? }
	echo "Total: <b>$grafico_total</b> Ordens de Serviço";
	?>
	
	<?
        for($k=0;$k<count($grafico_titulo);$k++){
	    $chart_titulo .= $grafico_titulo[$k];
	    if($k+1<count($grafico_titulo)) $chart_titulo .= "|";
	}
        for($k=0;$k<count($grafico_titulo);$k++){
	    $chart_valor .= $grafico_prc[$k];
	    if($k+1<count($grafico_titulo)) $chart_valor .= ",";
	}
        ?>
	
	<br /><br />
	<img style="padding: 0" src="http://chart.apis.google.com/chart?cht=p3&chd=t:<?= $chart_valor ?>&chs=288x100&chco=000000&chf=bg,s,CCCCCC00|c,s,00000050&chl=<?= $chart_titulo ?>" />
	<br />
                    
	<div class="clear_left">&nbsp;</div>
    </ul>