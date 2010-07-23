<? session_start();?>
<script>

$(document).ready(function(){
	$('#content_left').load("utf2iso.php?file=menu.php");		
});

$("#full_background").hide();
$("#full_frame").hide();

$("#full_background").click(function(){
	$("#full_background").fadeOut(50);	
	$("#full_frame").html('&nbsp;');
	$("#full_frame").hide();
});

</script>

<div id="full_background" title="Clique para fechar"><img src="images/bt_atualizar.png" alt=""></div>
<div id="full_frame">
	<div style="margin-top: 100px; text-align: center"><img src="images/ajax-loader.gif"><br />Consultando...</div>
</div>
<div id="content_left"></div>
<div id="content_right"></div>
