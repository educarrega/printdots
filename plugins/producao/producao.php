<? @session_start();
#require_once "../../conn.php";
if(!$busca){
include_once "producao_painel_busca.php";
}
login();
?>

	<?
	if($codigo){
		$SQLd = "SELECT autorizado_data,data_prevista FROM producao WHERE codigo='$codigo'";
		$dt = "autorizado_data";
	}elseif($agencia){
		$SQLd = "SELECT autorizado_data,data_prevista FROM producao WHERE (status!='' AND status!='1' AND agencia='$agencia') GROUP BY data_prevista ORDER BY data_prevista DESC, digital_prioridade DESC, codigo DESC LIMIT 10";
		$dt = "data_prevista";
	}else{
		#consulta principal
		#quantidade de dias listados antes de hoje
		$producao_dias_passados = $_SESSION["producao_dias_passados"];
		if(!$producao_dias_passados) $producao_dias_passados = 7;
		$SQLd = "SELECT data_prevista FROM producao WHERE (status!='' AND status!='1') AND TO_DAYS(NOW()) - TO_DAYS(data_prevista) <= ". $producao_dias_passados ." GROUP BY data_prevista ORDER BY data_prevista DESC, digital_prioridade DESC, codigo DESC";
		$dt = "data_prevista";
		$timestamp = strtotime('+3 days');
		$data_limite = date('Y-m-d', $timestamp);
	}
	
	?>
	<script language="javascript" src="plugins/ordem_de_servico/ordem_de_servico.js" type="text/javascript"></script>
	<script language="javascript" src="plugins/producao/producao.js" type="text/javascript"></script>
        <script type="text/javascript">
        	$('.producao_painel_busca_middle_interno').html('<img src=\"images/loader.gif\" class="loader"> Carregando as Ordens de Serviço');
		$(document).ready(function(){
			$('#producao_resultados').hide();
			<?
			$_SESSION[contador] = 0;
			$time_out = 200;
			$QRd = mysql_query($SQLd);
			while($rd = mysql_fetch_array($QRd)){ 
				$data_prevista = $rd[$dt]; ?>
				$('#dia_<?= $data_prevista ?>').load("utf2iso.php?file=plugins/producao/producao_dias.php",{data_prevista:'<?= $data_prevista ?>', codigo: '<?= $codigo ?>', agencia: '<?= $agencia ?>', digital_plotter: '<?= $equipamento ?>', filtro:'<?= $filtro ?>', order:'<?= $order ?>', tabela:'<?= $tabela ?>', campo:'<?= $campo ?>', info_aberto:'<?= $info_aberto ?>', incluir_os: '<?= $incluir_os ?>', time_out: '<?= $time_out ?>'});
				<? $divs .= "<div id=\"dia_". $data_prevista ."\"></div>"; 
				$time_out = ($time_out+1000); ?>
			<? } ?>
			var pesquisa = setTimeout( function() { 
				<? if($codigo){?>
				busca_status('Selecionando apenas uma Ordem de Serviço');
				<? } ?>
			}, <?= $time_out ?> );
		});
		stop_Int();
		start_Int('<?= controle('mensagem_interval') ?>');
		
		function editar_producao(target,codigo,hash){
			stop_Int();
			$('#atualizacao').hide();
			$("#full_background").fadeOut(50);
			$('#producao_os_'+target).load("utf2iso.php?file=plugins/ordem_de_servico/editar_os2.php",{codigo:codigo, target: target});
			$('#producao_os_'+target).css({'overflow':'none', 'height': 'auto'});
			$('#producao_os_'+target).slideDown();
			if(hash) window.location.hash = hash;
		}
		
		function alert_status(mensagem){
			if(mensagem) alert(mensagem);
		}
		
	</script>

<? if(!$busca){ ?>
	<br /><br /><br /><br />
	<div id="producao_todas">
	<? echo $divs; ?>
	</div>
	<div id="producao_resultados">
	<? include "producao_busca.php" ?>
	</div>
<? }else{
	echo $divs;
} ?>
