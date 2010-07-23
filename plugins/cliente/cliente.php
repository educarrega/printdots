<? 
session_start();
#require_once "../../conn.php";
include_once "cliente_painel_busca.php";
?>

<script type="text/javascript">
	busca_status('<img src=\"images/loader.gif\" class="loader"> Carregando os clientes');
	$(document).ready(function(){
		$('#cliente_todas').load("utf2iso.php?file=plugins/cliente/cliente_lista.php");
		$('#cliente_todas').show();
	});	
</script>
<br /><br /><br /><br />
<div id="cliente_todas"></div>
