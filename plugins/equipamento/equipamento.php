<? 
session_start();
#require_once "../../conn.php";
include_once "equipamento_painel_busca.php";
?>

<script type="text/javascript">
	busca_status('<img src=\"images/loader.gif\" class="loader"> Carregando os Equipamentos');
	$(document).ready(function(){
		$('#equipamento_todas').load("utf2iso.php?file=plugins/equipamento/equipamento_lista.php");
		$('#equipamento_todas').show();
	});	
</script>
<br /><br /><br /><br />
<div id="equipamento_todas"></div>
