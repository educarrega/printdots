<? @session_start();
#require_once "../../conn.php";
include_once "producao_editar.php";

if($codigo){
	$wr_data = "codigo=$codigo";
}else{
	$wr_data = "(status!='' AND status!='1'";
	$campo_data = $dt;
	if(!$campo_data) $campo_data = "data_prevista";
	if($agencia) $wr_data .= " AND agencia='$agencia'";
	if($digital_plotter){
		$wr_data .= " AND digital_plotter != '' AND NOT ISNULL(digital_plotter) AND digital_plotter='$digital_plotter'";
		#echo "$digital_plotter";
	} 
	$wr_data .= " AND $campo_data='$data_prevista')";
	$ordenador = 100;
	#$limit = "LIMIT 10";
	$order_by = "ORDER BY digital_prioridade DESC, codigo DESC";
	
	if($_SESSION[ASCDESC]=="DESC") {
		$_SESSION[ASCDESC] = "ASC";
		$_ascdesc = '<img src="images/bt_acima.png" alt="" title="crescente">';
	}else{
		$_SESSION[ASCDESC] = "DESC";
		$_ascdesc = '<img src="images/bt_abaixo.png" alt="" title="decrescente">';
	}
	
	if($filtro) $order_by = "ORDER BY ".$order." ".$_SESSION[ASCDESC];
	if($filtro=="Equipamento") {
		$wr_data .= " AND digital_plotter!='' AND NOT ISNULL(digital_plotter)";
	}
}
	
if($filtro=="Equipamento"){
	$SQLr = "SELECT producao.* , producao_itens.codigo_producao, producao_itens.codigo_equipamento
		FROM producao, producao_itens, equipamento
		WHERE producao.codigo = producao_itens.codigo_producao
		AND producao_itens.codigo_equipamento = equipamento.codigo
		AND producao.data_prevista='$data_prevista' 
		AND producao.status != ''
		AND producao.status != '1'
		ORDER BY producao_itens.codigo_equipamento $_SESSION[ASCDESC]";	
	#echo $SQLr;
	$QRr = mysql_query($SQLr);
	if($days){
		$_SESSION[contador] =  $_SESSION[contador] + mysql_num_rows($QRr);
		$_SESSION[texto_contagem] = "$_SESSION[contador] Ordens de Serviço, edite-as ou pesquise outras no campo ao lado.";
	}
	#$ordenador = 0;
	while($rs = mysql_fetch_array($QRr)){ 

		#$ordenador++;
		if($alter){$alter=0;}else{$alter=1;}
		#mysql_query("UPDATE producao SET digital_prioridade=$ordenador WHERE codigo=$rs[codigo]");
		$codigo = $rs[codigo]; #daqui em diante nao precisa mais informar o codigo para os proximos includes
		?>

		<? #agrupamento por filtros
		if($filtro){
			if(!$ultimo_filtro || $ultimo_filtro!=$rs["codigo_equipamento"]){
				$ultimo_filtro = $rs["codigo_equipamento"];
				$SQLf = "SELECT $campo FROM $tabela WHERE codigo='$ultimo_filtro'";
				$QRf = mysql_query($SQLf);
				#echo $SQLf;
				$rsf = mysql_fetch_object($QRf);
				#echo $rsf->$campo;
				?>
				<div class='days_group_by_title'>
				<?= $filtro ?> : <?= ($rsf->$campo) ?>
				</div><hr class="hr_days_middle" />
				<? 
			}
			$codigos .= "|$rs[codigo]|";
			if(strstr($codigos,"|$rs[codigo]|")){
			$repetido++;
			$target = "_$repetido";
			}
		}
		$target = $rs[codigo]."_".$repetido; ?>
		<div class="producao_os_<?= $alter ?> producao_os" id="producao_os_<?= $target ?>">
		<? include "producao_os.php"; ?>
		</div>
		<hr class="hr_days_middle" />
		<?
		//estatisticas do dia
	}
}
	$SQLr = "SELECT * FROM producao WHERE $wr_data $order_by $limit";
	#echo $SQLr;
	$QRr = mysql_query($SQLr);
	if($days){
		$_SESSION[contador] =  $_SESSION[contador] + mysql_num_rows($QRr);
		$_SESSION[texto_contagem] = "$_SESSION[contador] Ordens de Serviço, edite-as ou pesquise outras no campo ao lado.";
	}
	#$ordenador = 0;
	while($rs = mysql_fetch_array($QRr)){ 

		#$ordenador++;
		if($alter){$alter=0;}else{$alter=1;}
		#mysql_query("UPDATE producao SET digital_prioridade=$ordenador WHERE codigo=$rs[codigo]");
		$codigo = $rs[codigo]; #daqui em diante nao precisa mais informar o codigo para os proximos includes
		?>

		<? #agrupamento por filtros
		
		if($filtro){
			if(!$ultimo_filtro || $ultimo_filtro!=$rs[$order]){
				$ultimo_filtro = $rs[$order];
				if($filtro == 'Entrega') $wr_filtro = "AND tipo_transporte='1'";		
				$SQLf = "SELECT $campo FROM $tabela WHERE codigo='$ultimo_filtro' $wr_filtro";
				$QRf = mysql_query($SQLf);
				#echo $SQLf;
				$rsf = mysql_fetch_object($QRf);
				#echo $rsf->$campo;
				?>
				<div class='days_group_by_title'>
				<?= $filtro ?> : <?= str_replace('TRANSP','',strtoupper(acento($rsf->$campo))) ?>
				</div><hr class="hr_days_middle" />
				<? 
			}
		} 
		$target = "$rs[codigo]"; ?>
		<div class="producao_os_<?= $alter ?> producao_os" id="producao_os_<?= $target ?>">
		<? include "producao_os.php"; ?>
		</div>
		<hr class="hr_days_middle" />
		<?
		//estatisticas do dia
	} 


	#resumo do dia
	$stats = "";
	$qrp = mysql_query("SELECT * FROM equipamento ORDER BY titulo ASC");
	while($rsp = mysql_fetch_array($qrp)){
		
		//criando a lista de equipamentos do dia
		if($m2[$rsp[codigo]]){
			// grafico
			$max_dia = ($rsp[mph]*24);
			$rel_dia = number_format(($m2[$rsp[codigo]]*100)/$max_dia,0,"","");
			$bck = "";
			if($rel_dia>33) $bck = 'background: #FFFF00; color: #333;';
			if($rel_dia>66) $bck = 'background: #FF0000; color: #FFFF00;';
			// descricao de cada equipamento
			$stats .= "<div class=\"stats_linha\"><span><b>$rsp[titulo]</b>:</span> <span>". m2h($min[$rsp[codigo]]) ."</span> <span>". $m2[$rsp[codigo]] ."m&#178;</span> <span>". $ps[$rsp[codigo]] ." kg</span> <span class=\"stats_grafico\"><div class=\"stats_tt\" title=\"". $max_dia ."mÂ² suportados por dia\"><div class=\"stats_prc\" style=\"width:". $rel_dia ."%;$bck\">". $rel_dia ."%</div></div></span><br /></div>";
		}
		
		// para as totalizacoes
		$t_dia = ($t_dia+$min[$rsp[codigo]]); //tempo em minutos do dia do equipamento
		$m_dia = ($m_dia+$m2[$rsp[codigo]]); // metros por dia do equipamento
		$p_dia = ($p_dia+$ps[$rsp[codigo]]); // peso por dia do equipamento
		$min[$rsp[codigo]] = "";
		$m2[$rsp[codigo]] = "";
		$ps[$rsp[codigo]] = "";
	}
	
	//imprimindo os valores no status
	$totais = "<div class=\"stats_linha\"><span>Equipamentos:</span> <span>Tempo</span> <span>Metros</span> <span>Peso</span> <span class=\"stats_grafico\">24h do equipamento</span><br /></div>";
	$totais .= $stats;
	$totais .= "<span><b>Totais:</b></span> <span>". m2h($t_dia) ."</span> <span>". $m_dia ."m&#178;</span> <span>". $p_dia ." Kg</span>";
	
	//limpando para o proximo loop de dia
	$t_dia = 0;
	$m_dia = 0;
	$max_dia = 0;
	$rel_dia = 0;
	$data_prevista = "";
?>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	<? if($incluir_os){ ?>
	busca_status("Incluindo Ordem de Serviço");
	<? }else{ ?>
	$('#days_stats_middle_<?= $_POST[data_prevista] ?>').html('<?= $totais ?>');
	busca_status("<?= $_SESSION[texto_contagem] ?>");
	<? } ?>
});

$('[title]').tooltip({ 
	track: true, 
	delay: 0, 
	showURL: false, 
	showBody: true, 
	fade: 10 
});
</script>
<?
	$t_dia = 0;
	$m_dia = 0;
	$data_prevista = "";
	$stats = "";
	$totais = "";
?>
