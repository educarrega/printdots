<? 
session_start();
#require_once "../../conn.php";
include_once "pedido_painel_busca.php";
?>

<script type="text/javascript">
	busca_status('<img src=\"images/loader.gif\" class="loader"> Carregando Pedidos...');
	$(document).ready(function(){
		$('#pedido_todas').load("utf2iso.php?file=plugins/pedido/pedido_lista.php");
		$('#pedido_todas').show();
	});	
</script>
<br /><br /><br /><br />
<div id="pedido_todas"></div>
