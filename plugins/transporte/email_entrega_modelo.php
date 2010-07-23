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
}
a:hover {
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
	background: url('{site}/wp-content/themes/glossyblue-1-4/images/nwd_base.jpg') no-repeat 87% 50%;
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

.imagem{
	width: 125px;
	border: 2px solid #000;
	float: right;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
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
table td table td table{
	width: auto;
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

	<br>
	Olá <b>Parceiro</b>, seguem os dados para reconhecimento da mercadoria, permitindo retirada ou aguardo para entrega:<br>
	<br>
	

	<div class="titulo">Detalhes da Entrega</div><br />
	
	<img src="{site}/gestor/plugins/producao/thumbs/{codigo}.jpg" border="0" class="imagem" />
	
	<table>
	
	<tr>
	<td><b>Ordem de Serviço:</b></td>
	<td>{codigo}</td>
	</tr>
	
	<tr>
	<td><b>Título:</b></td>
	<td>{titulo_servico}</td>
	</tr>
	
	<tr>
	<td><b>Quantidade:</b></td>
	<td>{quantidade}</td>
	</tr>
	

	<tr>
	<td><b>Cliente:</b></td>
	<td>{agencia}</td>
	</tr>

	<tr>
	<td><b>Autorizado:</b></td>
	<td>{autorizado_data}</td>
	</tr>

	<tr>
	<td><b>Chegada:</b></td>
	<td>{chegada}</td>
	</tr>

	<tr>
	<td><b>Conhecimento:</b></td>
	<td>{transporte_obs}</td>
	</tr>
	
	<tr>
	<td><b>Tipo de frete:</b></td>
	<td>{frete_tipo}</td>
	</tr>
	
	<tr>
	<td><b>Instruções:</b></td>
	<td>{entrega_obs}</td>
	</tr>

	<tr>
	<td><b>Rastrear/Infos:</b></td>
	<td>{transporte_servico}<br />{transporte_site}</td>
	</tr>

	</table>
		
	<br /><br />
	Agradecemos a atenção.<br />
    
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
