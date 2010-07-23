<? 
session_start();
#require_once "../../conn.php";
include_once "material_painel_busca.php";
?>

<script type="text/javascript">
	busca_status('<img src=\"images/loader.gif\" class="loader"> Carregando os Materiais e Serviços');
	$(document).ready(function(){
		$('#material_todas').load("utf2iso.php?file=plugins/material/material_lista.php");
		$('#material_todas').show();
	});	
</script>
<br /><br /><br /><br />
<div id="material_todas"></div>
