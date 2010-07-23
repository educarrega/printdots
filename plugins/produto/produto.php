<? 
session_start();
#require_once "../../conn.php";
include_once "produto_painel_busca.php";
?>

<script type="text/javascript">
	busca_status('<img src=\"images/loader.gif\" class="loader"> Carregando os Produtos');
	$(document).ready(function(){
		$('#produto_todas').load("utf2iso.php?file=plugins/produto/produto_lista.php");
		$('#produto_todas').show();
	});	
</script>
<br /><br /><br /><br />
<div id="produto_todas"></div>
