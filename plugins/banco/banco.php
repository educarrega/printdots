<? 
session_start();
#require_once "../../conn.php";
include_once "banco_painel_busca.php";
?>

<script type="text/javascript">
	busca_status('<img src=\"images/loader.gif\" class="loader"> Carregando os Bancos e Contas  ');
	$(document).ready(function(){
		$('#banco_todas').load("utf2iso.php?file=plugins/banco/banco_lista.php");
		$('#banco_todas').show();
	});	
</script>
<br /><br /><br /><br />
<div id="banco_todas"></div>
