<? session_start(); 
require_once "conn.php";
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>PrintDots.com.br - Administração para Impressão Digital e Comunicação Visual</title>

	<style type="text/css">
	@import url("estilos.css");
	@import url("fieldset.css");
	@import url("jquery.click-calendario-1.0.css");
	</style>
	
	<script language="javascript" src="jquery.js" type="text/javascript"></script>
	<script language="javascript" src="jquery.tooltip.js" type="text/javascript"></script>
	<script language="javascript" src="jquery.click-calendario-1.0.js" type="text/javascript"></script>
	<script language="javascript" src="functions.js" type="text/javascript"></script>
    
    <script type="text/javascript">
	$("#carregando").hide();
        $(document).ready(function(){
		$('#content').load("utf2iso.php?file=login.php");
    });
	</script>

</head>
<body>

<div class="wallpaper"><img src="images/background/<?= background() ?>"/></div>

<div id="content"></div>

</body>
</html>
