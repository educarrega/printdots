<? 
session_start();
#require_once "../../conn.php";
include_once "contas_pagar_painel_busca.php";
?>

<script type="text/javascript">
	busca_status('<img src=\"images/loader.gif\" class="loader"> Carregando Contas à Pagar...');
	$(document).ready(function(){
		$('#contas_pagar_todas').load("utf2iso.php?file=plugins/contas_pagar/contas_pagar_lista.php");
		$('#contas_pagar_todas').show();
	});	
</script>
<br /><br /><br /><br />
<div id="contas_pagar_todas"></div>
