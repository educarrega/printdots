<? 
session_start();
#require_once "../../conn.php";
include_once "carteira_painel_busca.php";
?>

<script type="text/javascript">
	busca_status('<img src=\"images/loader.gif\" class="loader"> Carregando as Carteiras de Pagamento');
	$(document).ready(function(){
		$('#carteira_todas').load("utf2iso.php?file=plugins/carteira/carteira_lista.php");
		$('#carteira_todas').show();
	});	
</script>
<br /><br /><br /><br />
<div id="carteira_todas"></div>
