<? @session_start();
#require_once "../../conn.php";
?>

<style type="text/css">
@import url("plugins/producao/producao.css");
@import url("plugins/contas_receber/contas_receber.css");
</style>

<script type="text/javascript">

function lookup_pedido(inputString) {
$('#bt_limpar_pedido').show();
if(inputString.length == 0) {
	$('#bt_limpar_pedido').hide();
	$('#contas_receber_todas').load("utf2iso.php?file=plugins/contas_receber/contas_receber_lista.php");
	//$("input[type='text']:first", document.forms[0]).focus();
	busca_status('');
	document.location.hash = '#';
} else {
	$('#contas_receber_todas').show();
	$('#contas_receber_relatorio').hide();
	var status = $('input[type=radio][name=status]:checked').val();
	var todosmeses = $('input[type=radio][name=todosmeses]:checked').val();
	var pesquisa = setTimeout(function(){
		var pesquisado = $('#inputString').val();
		busca_status("<img src='images/loader.gif' class='loader'> Efetuando sua busca por: "+pesquisado);
		$('#contas_receber_todas').load("utf2iso.php?file=plugins/contas_receber/contas_receber_lista.php", {busca: ""+ encodeURI(pesquisado) +"",status:status,todosmeses:todosmeses});
	},2000);
}
} // lookup

$('#bt_limpar_pedido').click(function(){
	$('#inputString').val('');
	lookup_pedido('');
});

//oculta painel qdo scroll
$(document).ready(function(){
	//$("input[type='text']:first", document.forms[0]).focus();
	$('#bt_limpar_pedido').hide();
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
		<div class="producao_painel_busca_middle_interno click" title="Clique e alterne entre o modo relatório e listagem" onclick="contas_receber_escolha()"></div>		
	</div>
	<div class="producao_painel_busca_right">
		<div class="producao_painel_busca_right_interno">
		<input type="text" name="<?= $uid ?>" id="inputString" onkeyup="lookup_pedido(this.value);" />
		<input type="image" id="bt_limpar_pedido" src="images/bt_limpar.png?uid=<?= $uid ?>" title="Limpar a busca" />
		</div>
	</div>
</div>
