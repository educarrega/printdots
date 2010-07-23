<? session_start();
#require_once "conn.php";
?>

<script>
$("#logout").click(function() {
	$('#content_right').html('');
	$('.panel_container').slideUp(function(){
	$('#content').load("login.php?logout=1");
	});
});

$(document).ready(function(){
	$('[title]').tooltip({ 
		track: true, 
		delay: 0, 
		showURL: false, 
		showBody: true, 
		fade: 50 
	});

	$('#content_right').load("utf2iso.php?file=<?= controle('plugin_inicial') ?>"); 
	//ex: plugins/producao/producao.php
});
function mensagem(){
	$('#mensagem').load('utf2iso.php?file=plugins/mensagens/mensagens.php');
	//alert('<?= controle('mensagem_interval') ?>');
}
var intervalo = setInterval("mensagem()",<?= controle('mensagem_interval') ?>);

$("#mensagem").mouseover(function(){
	$("#mensagem").css({overflow:'auto'});
});
$("#mensagem").mouseout(function(){
	$("#mensagem").css({overflow:'hidden'});
});

</script>

<div class="panel_container">
<div class="panel_container_top"></div>
<div class="panel_container_middle">
	
	<div id="logout">
	<img src="images/logout.png" title="Logout do Sistema"/>
	</div>

	<div id="user_picture_medium">
	<img id="prefs" src="<?= $_SESSION[user_image] ?>" />
	</div>

	<div id="user_login">
	Olá<br />
	<?= $_SESSION[user_detalhes] ?><br/>
	<small><?= $_SESSION[settings_empresa] ?></small>
	<?php #echo utf8_encode('áéíóúç') ?>
	</div>

	<div class="clear_left"></div>
	
</div>
<div class="panel_container_bottom"></div>
</div>

<div class="panel_container">
<div class="panel_container_top"></div>
<div class="panel_container_middle">

<?
#categorias de menus

$SQL3 = "SELECT * FROM controle WHERE rel='". controle("menu") ."' AND ativo='1' ORDER BY ordem, descricao ASC, titulo ASC";
#echo $SQL3;
$cn3 = mysql_query($SQL3);
while($rs3 = mysql_fetch_array($cn3)){
	
	$usr = explode(',',$rs3[usuario]);
	if(in_array($_SESSION[user_codigo],$usr)){

	#sessions de opcoes de plugins
	$SQL4 = "SELECT * FROM controle WHERE rel='".$rs3[codigo]."' AND ativo='1'";
	#echo $SQL4;
	$cn4 = mysql_query($SQL4);
	while($rs4 = mysql_fetch_array($cn4)){
		$_SESSION[$rs4[titulo]] = $rs4[valor];
		$_SESSION[$rs4[titulo]."_codigo"] = $rs4[codigo];
	}
	#criar a sessao de plugins ativos para utilizacao na function plugins()
	$_SESSION[$rs3[titulo]] = $rs3[valor];
		
	# montagem de cada botao do menu?>
	<script>
	$('#menu_<?= $rs3[codigo] ?>').click(function(){
	$('#content_right').load("utf2iso.php?file=<?= $rs3[valor] ?>");
	stop_Int();
	});
	</script>
	<a id="menu_<?= $rs3[codigo] ?>" href="#" title="<?= ($rs3[descricao]) ?>"><img id="menu_<?= $rs3[codigo] ?>" src="<?= $rs3[titulo] ?>" alt="" /></a>
	
	<? }#usuario
} ?>

<div class="clear_left"></div>
</div>
<div class="panel_container_bottom"></div>
</div>

<div class="panel_container">
<div class="panel_container_top"></div>
<div class="panel_container_middle">
	<div id="mensagem">nada</div>
</div>
<div class="panel_container_bottom"></div>
</div>

<div class="panel_container">
<div class="panel_container_top"></div>
<div class="panel_container_middle">
	<div id="menu_printdots">
	<img src="images/printdots_h.png" />
	</div>
</div>
<div class="panel_container_bottom"></div>
</div>

<script>mensagem()</script>
