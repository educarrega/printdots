<? require "conn.php"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Estatísticas - Escolha</title>
	<style type="text/css">
	@import url("estilos.css");
	</style>
	<script language="javascript" src="library/scripts.js"></script>
	<script language="javascript">
	altura = screen.height;
	largura = screen.width;
	self.resizeTo(920,altura-120);
	self.moveTo(largura/2-460,20);
	</script>
</head>
<body style="margin:10px;" onLoad="self.focus()">
<h3>Estatísticas - Escolha</h3>

Informe os critérios da Estat&iacute;sticas.
<hr size="1" class="hr" />
<div align="center">
<form action="estatistica_relatorio.php" method="get" target="estatistica_relatorio" onSubmit="estatistica_relatorio();submit()">
<div align="center" id="bts">Clientes</div>
<?
echo "<select name='agencia' class='caixa'>";
echo "<option value='todos' selected>Todos</option>";
$qr = mysql_query("SELECT codigo,nome_fantasia FROM cliente ORDER BY nome_fantasia ASC");
while($rsc = mysql_fetch_array($qr)){
echo "<option value='$rsc[codigo]'>$rsc[nome_fantasia]</option>";
}
echo "</select>";
?>
</div><br>
<div align="center"><strong>Período</strong></div>
<table cellpadding="2" cellspacing="0" width="250" align="center"><tr>
<td>Data Inicial<br>
<input type="text" name="data_inicial" class="caixa" value="00/00/0000" onFocus="if(this.value=='00/00/0000')this.value=''" onBlur="if(this.value=='')this.value='00/00/0000'"></td>
<td>&nbsp;</td>
<td>Data Final<br>
<input type="text" name="data_final" class="caixa" value="00/00/0000" onFocus="if(this.value=='00/00/0000')this.value=''" onBlur="if(this.value=='')this.value='00/00/0000'"></td>
</tr></table>
</div>
<br>
<div align="center">
<hr size="1" class="hr" />
<input type="submit" value="Visualizar" class="bts">
</div>
</form>
</body>
</html>
