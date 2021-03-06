<? @session_start();
#require_once "../../conn.php";
?>

<style type="text/css">
@import url("plugins/producao/producao.css");
@import url("plugins/equipamento/equipamento.css?<?= $uid ?>");
</style>

<script type="text/javascript">

function lookupequipamento(inputString) {
$('#bt_limpar_equipamento').show();
if(inputString.length == 0) {
	$('#bt_limpar_equipamento').hide();
	$('#equipamento_todas').load("utf2iso.php?file=plugins/equipamento/equipamento_lista.php");
	//$("input[type='text']:first", document.forms[0]).focus();
	busca_status('');
	document.location.hash = '#';
} else {	
	busca_status("<img src='images/loader.gif' class='loader'> Efetuando sua busca por: "+inputString);
    	$('#equipamento_todas').load("utf2iso.php?file=plugins/equipamento/equipamento_lista.php", {busca: ""+ encodeURI(inputString) +""});
}
} // lookup

$('#bt_limpar_equipamento').click(function(){
	$('#inputString').val('');
	lookupequipamento('');
});

//oculta painel qdo scroll
$(document).ready(function(){
	//$("input[type='text']:first", document.forms[0]).focus();
	$('#bt_limpar_equipamento').hide();
	var animActive = false;
	$(window).scroll(function(){
		if (animActive == false){
			animActive = true;
			$('#producao_painel_busca').fadeOut(100, function () {
				var scrl = setTimeout( function(){
				animActive = false;
				$('#producao_painel_busca').fadeIn(500);
				}, 1500);
			});
		}
	});
});

</script>

<div id="producao_painel_busca">
	<div class="producao_painel_busca_left">&nbsp;</div>
	<div class="producao_painel_busca_middle">
		<div class="producao_painel_busca_middle_interno">
		</div>
	</div>
	<div class="producao_painel_busca_right">
		<div class="producao_painel_busca_right_interno">
		<input type="text" name="<?= $uid ?>" id="inputString" onkeyup="lookupequipamento(this.value);" />
		<input type="image" id="bt_limpar_equipamento" src="images/bt_limpar.png" title="Limpar a busca" />
		</div>
	</div>
</div>
