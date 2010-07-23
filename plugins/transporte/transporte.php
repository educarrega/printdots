<? 
session_start();
#require_once "../../conn.php";
include_once "transporte_painel_busca.php";
?>

<script type="text/javascript">
	busca_status('<img src=\"images/loader.gif\" class="loader"> Carregando os Tipos de Transporte');
	$(document).ready(function(){
		$('#transporte_todas').load("utf2iso.php?file=plugins/transporte/transporte_lista.php");
		$('#transporte_todas').show();
	});	
</script>
<br /><br /><br /><br />
<div id="transporte_todas"></div>
