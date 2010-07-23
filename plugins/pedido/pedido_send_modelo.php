<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<style type="text/css">
body {
	background-color:#333;
	font-family: Verdana,Arial,Tahoma;
	font-size: 11px;
	padding: 10px;
	font-family: "Liberation sans", "Segoe ui", Helvetica, Arial, Tahoma, Verdana;
	font-size: 12px;
	margin: 0 auto;
}
#conteudo{
	background-color:#333;
	padding: 10px;
	width: 100%;
	height: 100%;
}
a:link {
	color: #000000;
	text-decoration: none;
}
a:hover {
	text-decoration: none;
	color: #666;
}
.titulo{
	text-align: center;
	font-size: 17px;
	padding: 5px;
	border: 1px solid #000;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
}
#topo{
	background: url('http://www.newdoor.com.br/wp-content/themes/glossyblue-1-4/images/nwd_base.jpg') no-repeat 87% 50%;
	height: 150px;
	padding: 5px;
	margin: 5px;
	border: 2px solid #999;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
}
#base{
	color: #CCC;
	background: #333;
	border: 2px solid #999;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
}
#base a{
	color: #CCC;
}
table {
	border-width: 0px;
	border-spacing: 0px;
	border-style: none;
	border-collapse: collapse;
	background-color: white;
	width: 600px;
}
table td {
	border-width: 0 0 0 0;
	padding: 10px;
	font-size:12px;
	vertical-align: top;
}
table td table td table td{
	padding: 0;
}
hr{
	border:0;
	border-bottom: 1px solid #000;
}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<table id="conteudo"><tr><td>
<table align="center">
<tr>
<td id="topo"></td>
</tr>

<tr>
<td>
	<br />
      	<div class="titulo">{titulo}</div>
      	<br />
      	
	Ol&aacute; <b>Parceiro</b>.<br />
	<br />
	<b>Esta é uma solicitação de compra de Materiais e Serviços.</b><br />
	Em caso de dÃºvidas, favor entrar em contato com nosso departamento financeiro pelos dados impressos ao final deste email. <br />
	Se possível, aguardamos o retorno via email ou telefone no prazo de até 24hs, comprovando o recebimento deste.
	<br /><br />
	
	<div class="titulo">Itens do Pedido</div><br />
	
	<div>{item}</div>
	Chegada Prevista: {chegada}
	<br /><br />
	
	
	<table><tr>
	<td>
	<b>Dados para Faturamento:</b><br />
	{faturamento}
	</td>
	<td>
	<b>Cadastro do Fornecedor:</b><br />
	{fornecedor}
	</td>
	</tr></table>
	<br /><br /><br />
	Agradecemos a atenção.
	<br /><br />
</td>
</tr>

<tr>
<td id="base">
<b>{empresa}</b><br />
{endereco}<br />
<a href="mailto:{from}">{from}</a><br />
<a href="{site}" target="_blank">{site}</a>
</td>
</tr>

</table>
</td></tr></table>
</body>
</html>
