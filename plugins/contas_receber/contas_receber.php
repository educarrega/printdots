<? 
session_start();
#require_once "../../conn.php";
include_once "contas_receber_painel_busca.php";
?>

<script language="javascript" src="plugins/ordem_de_servico/ordem_de_servico.js" type="text/javascript"></script>
<script type="text/javascript">
	busca_status('<img src=\"images/loader.gif\" class="loader"> Carregando Contas à Receber...');
	$(document).ready(function(){
		$('#contas_receber_todas').load("utf2iso.php?file=plugins/contas_receber/contas_receber_lista.php");
		$('#contas_receber_relatorio').load("utf2iso.php?file=plugins/contas_receber/contas_receber_relatorio.php");
		$('#contas_receber_todas').show();
		$('#contas_receber_relatorio').show();
	});	
</script>
<br /><br /><br /><br />
<div id="contas_receber_todas"></div>
<div id="contas_receber_relatorio"></div>
