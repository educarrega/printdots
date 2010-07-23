<? @session_start();
#require_once "../../conn.php";
?>

<style type="text/css">
@import url("plugins/producao/producao.css");
</style>

<script type="text/javascript">

function lookup(inputString) {
$('#bt_limpar').hide();
clearTimeout(pesquisa);
$('#atualizacao').hide();
stop_Int();
if(inputString.length == 0) {
	$('#bt_limpar').hide();
	//$('#producao_todas').show();
	$('#content_right').load("utf2iso.php?file=plugins/producao/producao.php");
	$('#producao_resultados').hide();
	//$("input[type='text']:first", document.forms[0]).focus();
	busca_status('');
	document.location.hash = '#';
	$('#atualizacao').show();
	start_Int('<?= controle('mensagem_interval') ?>');
} else {
	$('#bt_limpar').show();
	$('#producao_todas').hide();
	$('#producao_resultados').show();
	var pesquisa = setTimeout(function(){
		var pesquisado = $('#inputString').val();
		busca_status("<img src='images/loader.gif' class='loader'> Efetuando sua busca por: "+pesquisado);
    		$('#days_middle_completo').load("utf2iso.php?file=plugins/producao/producao_busca.php", {busca: ""+ encodeURI(pesquisado) +""}, function(){
			$('#days_middle_completo').show();    		
    		});
	},2000);	
}
} // lookup

function fill(thisValue) {
	window.location.hash = '#';
	$('#producao_todas').load("utf2iso.php?file=plugins/producao/producao.php", {codigo: thisValue, busca: '1'});
	$('#producao_todas').show();
	$exibir = 0;
}

$('#bt_limpar').click(function(){
	$('#inputString').val('');
	lookup('');
});


//oculta painel qdo scroll
$(document).ready(function(){
	//$("input[type='text']:first", document.forms[0]).focus();
	$('#bt_limpar').hide();
	$('#days_middle_carregando_busca').hide();
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

var exibir = 1;
$('.producao_painel_busca_middle').click(function(){
	if(exibir==1){
		exibir = 0;
		$('#producao_todas').hide();
		$('#producao_resultados').show();
	}else{
		exibir = 1;
		$('#producao_todas').show();
		$('#producao_resultados').hide();
	}
});

</script>

<div id="producao_painel_busca">
	<div class="producao_painel_busca_left">&nbsp;</div>
	<div class="producao_painel_busca_middle">
		<div class="producao_painel_busca_middle_interno" title="Clique para alternar entre os resultados da busca e a lista de produção">
		</div>
	</div>
	<div class="producao_painel_busca_right">
		<div class="producao_painel_busca_right_interno">
		<input type="text" name="<?= $uid ?>" id="inputString" onkeyup="lookup(this.value);" />
		<input type="image" id="bt_limpar" src="images/bt_limpar.png" title="Limpar a busca e recarregar a tela principal" />
		</div>
	</div>
</div>
