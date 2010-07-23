<?@session_start();
#require_once "../../conn.php";
login();

if(!isset($_POST[busca])) { ?>

<table class="days_top"  id="days_top_<?= $data_prevista ?>">
<tr>
<td class="days_top_1"></td>
<td class="days_top_2" id="days_date_<?= $data_prevista ?>">Busca</td>
<td class="days_top_3"></td>
<td class="days_top_4">&nbsp;</td>
<td class="days_top_5"></td>
</tr>
</table>

<div id="days_middle_carregando_busca" class="days_middle">
<img src="images/loader.gif" class="loader" />
</div>

<div id="days_middle_completo" class="days_middle suggestionList">

<? }

if(isset($_POST[busca])) {
	if(strlen($busca)>0) {
		
		#setagens iniciais
		$busca = (acento(str_replace('%C3','',$busca))); #trata codificacao do ajax
		#echo $busca;
		$wr = ""; #inicia o where vazio
		$order = "data_prevista DESC, codigo DESC"; #ordenacao padrao
		#if(strstr($busca,"/")) $busca = mydate($busca); #se a busca for em formato data
		#fim setagens
		
		#if(strstr($busca,"/")) $busca = mydate($busca);

		#buscar na tabela agencia
		$SQLag = "SELECT codigo FROM cliente WHERE nome_fantasia LIKE '%$busca%' OR desde LIKE '%$busca%' OR cidade LIKE '%$busca%' OR telefone LIKE '%$busca%' OR email LIKE '%$busca%'";
		$QRag = mysql_query($SQLag);
		while($rg = mysql_fetch_array($QRag)){
			if($rg[codigo]){
				$wr .= "agencia='$rg[codigo]' OR ";
			}
		}
		
		
		#buscar na tabela produto
		$SQLpd = "SELECT codigo FROM produto WHERE titulo LIKE '%$busca%' OR descricao LIKE '%$busca%'";
		$QRpd = mysql_query($SQLpd);
		while($pd = mysql_fetch_array($QRpd)){
			if($pd[codigo]){
				$wr .= "codigo_produto='$pd[codigo]' OR ";
			}
		}
		
		
		#buscar na tebela de contas receber
		#--09/03/2010--#remoçao da pesquisa em bvs--#$SQLdc = "SELECT codigo,codigo_producao FROM contas_receber WHERE codigo_producao LIKE '%$busca%' OR valor LIKE '%". str_replace(",",".",$busca) ."%' OR documento LIKE '%$busca%' OR data_vencimento LIKE '%$busca%' OR data_pagamento LIKE '%$busca%' OR obs LIKE '%$busca%' OR bv_valor LIKE '%". str_replace(",",".",$busca) ."%' OR bv_data_pagamento LIKE '%$busca%' OR bv_documento LIKE '%$busca%' OR email LIKE '%$busca%'";
		$SQLdc = "SELECT codigo,codigo_producao FROM contas_receber WHERE codigo_producao LIKE '%$busca%' OR valor LIKE '%". str_replace(",",".",$busca) ."%' OR documento LIKE '%$busca%' OR data_vencimento LIKE '%$busca%' OR data_pagamento LIKE '%$busca%' OR obs LIKE '%$busca%' OR email LIKE '%$busca%'";
		$QRdc = mysql_query($SQLdc);
		while($dc = mysql_fetch_array($QRdc)){
			if($dc[producao]){
				$wr .= "codigo='$dc[producao]' OR ";
			}
		}

		
#		#buscar na tabela equipamento
#		$SQLpt = "SELECT * FROM equipamento WHERE titulo LIKE '%$busca%'";
#		$QRpt = mysql_query($SQLpt);
#		while($pt = mysql_fetch_array($QRpt)){
#			$wr .= "digital_plotter=$pt[codigo] OR ";
#		}

		
		#buscar na tabela producao
		$SQLp = "SELECT * FROM producao LIMIT 1";
		$QRp = mysql_query($SQLp);
		for($i=0;$i<mysql_num_fields($QRp);$i++){
			$wr .= "UPPER(". (mysql_field_name($QRp,$i)) .") LIKE '%". strtoupper($busca) ."%' OR ";
		}
		
		$wr = substr($wr,0,strlen($wr)-4);
		
		#resultados da pesquisa
		$select = "SELECT * FROM producao WHERE $wr GROUP BY codigo ORDER BY $order LIMIT 100";
		$query = mysql_query($select);
		#echo $select;
		    $resultados = 0;
		    while ($rs = mysql_fetch_array($query)) {
			for($i=0;$i<mysql_num_fields($query);$i++){
				$rs_query = acento(strtoupper($rs[mysql_field_name($query,$i)]));
				#if(strstr($rs_query,strtoupper($busca))){
					if($resultados<100){
					if($rs[codigo]!=$cod_achado){
					if(!$ult_data || $ult_data!=$rs[autorizado_data]){ ?>
					<div class='days_group_by_title'><?= mydate($rs[autorizado_data]) ?></div><hr class="hr_days_middle" />
					<? }
					$resultados++;
					$ult_data = $rs[autorizado_data];
					$cod_achado = $rs[codigo];
					$achado = (substr(strip_tags($rs[mysql_field_name($query,$i)]),0,80));
					echo '<li onclick="fill(\''.$rs[codigo].'\');">';
					echo '<div class="producao_busca_indice">'. $resultados .'</div>';
					echo '<img class="producao_os_thumbs_busca" src="'. existe('../../plugins/producao/thumbs/'.$rs[codigo].'.jpg',1).'">';
					echo '<b>'.$rs[codigo].'-'.($rs[titulo_servico]).'</b><br />';
					echo 'Quantidade; '.($rs[quantidade]).'<br />';
					echo $achado.'</li><hr class="hr_days_middle" />';
					}#cod_achado
					}#if resultados
				#}#if str
			}#for i
		    }# WHILE
		    ?>
			<div class='clear_left'>&nbsp;</div>
			
			<script type="text/javascript">
			if(<?= $resultados ?>>0){
			busca_status('<?= $resultados ?> ocorrências para "<?= $busca ?>"');
			}else{
			busca_status('<img src="images/bt_atencao.png" align="absmiddle" width="24"> Nenhuma ocorrência para "<?= $busca ?>"');
			}
			</script>
			<?
	}
}
?>

<?
if(!isset($_POST[busca])) { ?>

</div>

<div id="days_stats_top_<?= $data_prevista ?>" class="days_stats_top"></div>

<div id="days_stats_middle_<?= $data_prevista ?>" class="days_stats_middle"></div>

<div id="days_stats_bottom_<?= $data_prevista ?>" class="days_stats_bottom"></div>

<? } ?>
