<? 
session_start();
#require_once "../../conn.php";
include_once "configuracoes_painel_busca.php";
?>

<script type="text/javascript">
	busca_status('<img src=\"images/loader.gif\" class="loader"> Carregando as Configurações');
	$(document).ready(function(){
		$('#configuracoes_todas').load("utf2iso.php?file=plugins/configuracoes/configuracoes_lista.php");
		$('#configuracoes_todas').show();
	});	
</script>
<br /><br /><br /><br />
<div id="configuracoes_todas"></div>
