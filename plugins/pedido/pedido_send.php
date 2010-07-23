<?php
session_start();
	#require_once "../../conn.php";
	
	if($destinatario){
	
		#dados faturamento
		$codigo_empresa = controle('codigo_empresa');
		$SQLf = "SELECT * FROM cliente WHERE codigo=$codigo_empresa";
		$QRf = mysql_query($SQLf);
		$ft = mysql_fetch_array($QRf);
		$faturamento = "
		Razão Social: $ft[razao_social]
		Nome Fantasia: $ft[nome_fantasia]
		CNPJ: $ft[cnpj]
		Insc. Estadual: $ft[inscrest]
		Cidade/UF: $ft[cidade] - $ft[estado]
		Endereço: $ft[endereco]
		CEP: $ft[cep]
		Telefone: $ft[telefone]
		Contato: $ft[contato]
		Email: $ft[email]
		";
		
		$empresa = $ft[nome_fantasia];
		$endereco = "$ft[endereco] - $ft[cidade] $ft[estado] - $ft[cep] - $ft[telefone]";
		if($site && !strstr($site,'http://')) $site = "http://$site";
		$site = "$ft[website]";
		$from = controle('email_from');
		$fromname = controle('email_from_name');
		$bcc = controle('email_bcc');
		$assunto = str_replace("\n","",$titulo);
		$assunto = str_replace("\t","",$assunto);
		$lines = file('pedido_send_modelo.php');
		$l_count = count($lines);
		for($x = 0; $x< $l_count; $x++){
			$mensagem.= $lines[$x];
		}
		
		#materiais servicos quantidades
		$SQLs = "SELECT * FROM pedido WHERE codigo_rel=$codigo";
		#echo $SQLe;
		$hr = "";
		$QRs = mysql_query($SQLs);
		while($rss = mysql_fetch_array($QRs)){
			$SQLp = "SELECT * FROM material WHERE codigo='$rss[material_codigo]'";
			$SQLpdt = mysql_query($SQLp);
			$pdt = mysql_fetch_array($SQLpdt);
			if($hr) $item .="<hr>";
			$hr = 1;
			$item .= "Quantidade: <b>$rss[material_quantidade]</b> un.
			Item: <b>$pdt[titulo]</b>
			Cod. Fornecedor: $pdt[codigo_fornecedor] | Cod. Local: $pdt[codigo]
			Valor unitario: R$ $rss[valor_unitario]	| Peso do Lote: $rss[peso] kg | Taxas do Lote: R$ $rss[valor_acrescimo] | Valor do Lote: R$ $rss[valor_total]";
		}#while
		
		#registro pai
		$SQL = "SELECT * FROM pedido WHERE codigo=$codigo";
		$QR = mysql_query($SQL);
		$rs = mysql_fetch_array($QR);
			
			$SQLt = mysql_query("SELECT * FROM cliente WHERE codigo=$rs[transporte_codigo]");
			$rst = mysql_fetch_array($SQLt);
			
			if($rs[entrega_obs]) $obs_transporte = "Observações sobre este serviços de transporte: 
			$rs[entrega_obs]";
			
			$item .= "
			
			<div class='titulo'>Serviço de Transporte</div> 
			<b>$rst[nome_fantasia]</b>
			<small>Contato: $rst[contato]
			Email: $rst[email]
			Telefone: $rst[telefone]</small>
			";
			$frete = $rs[transporte_lancar] ? 'À Pagar no destino' : 'Pago na origem';
			$item .= "
			Modalidade do frete: $frete | Peso total estimado: $rs[peso] kg | Valor estimado do frete: R$ $rs[transporte_valor] 
			$obs_transporte
			<div class='titulo'>Total do Pedido: R$ $rs[valor_total]</div>
			
			Data de emissão do pedido: ". mydate($rs[data_criacao]) ."
			Obs do pedido: $rs[descricao]";
			
			#dados fornecedor
			$SQLfn = "SELECT * FROM cliente WHERE codigo=$rs[cliente_codigo]";
			$SQLfn = mysql_query($SQLfn);
			$fn = mysql_fetch_array($SQLfn);
			$fornecedor = "
			Razão Social: $fn[razao_social]
			Nome Fantasia: $fn[nome_fantasia]
			CNPJ: $fn[cnpj]
			Insc. Estadual: $fn[inscrest]
			Cidade/UF: $fn[cidade] - $fn[estado]
			Endereço: $fn[endereco]
			CEP: $fn[cep]
			Telefone: $fn[telefone]
			Contato: $fn[contato]
			Email: $fn[email]
			";
			if(!$nome) $nome = $fn[contato];
			
				
		$email_dados = $rs[email];
				
		$mensagem = str_replace("Parceiro",$nome,$mensagem);
		$mensagem = str_replace("{titulo}",$titulo,$mensagem);
		$mensagem = str_replace("{item}",nl2br($item),$mensagem);
		$mensagem = str_replace("{chegada}",$chegada,$mensagem);
		$mensagem = str_replace("{fornecedor}",nl2br($fornecedor),$mensagem);
		$mensagem = str_replace("{faturamento}",nl2br($faturamento),$mensagem);
		$mensagem = str_replace("{empresa}",$empresa,$mensagem);
		$mensagem = str_replace("{from}",$from,$mensagem);
		$mensagem = str_replace("{site}",$site,$mensagem);
		$mensagem = str_replace("{endereco}",$endereco,$mensagem);
		$mensagem = utf8_decode($mensagem);
		
		#INICIO DO EMAIL
		$headers = "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= 'From:' .$from;
		#$headers .= 'Bcc:' .$email.'\r\n';

		include "../../functions_email.php";
		#$enviado = 1; //teste somente
		#echo "$from,$fromname,$bcc,$destinatario,$assunto,$mensagem,$headers";
		#$envio = email($from,$fromname,$bcc,$destinatario,$assunto,$mensagem,$headers);
		
		if($enviado){
			$email_dados = date("d/m/Y H:i:s")." | $nome | $destinatario | ". $_SESSION[user_detalhes] ."\n". $email_dados;
			$SQL = "UPDATE pedido SET email='$email_dados' WHERE codigo=$codigo";
			mysql_query($SQL);
			#atualizar();
		}
		#FIM DO EMAIL
		?>
		
		<style type="text/css">
		@import url("../../fieldset.css");
		.conteudo{
		width: 700px;
		margin: 0px auto;
		</style>
		
		<? if($enviado){ ?>
		<div class="conteudo">
		<fieldset class="fieldset_ok">
			<?= utf8_decode("
			<legend>Confirmação</legend>
			<div style='color:#FFF;'>
				<b>O e-mail foi enviado com sucesso!</b><br />
				<b>$titulo</b><br />
				De: $fromname | $from<br />
				Para: $nome | $destinatario<br />
				CÃ³pia Oculta: $bcc<br />
			</div><br />
			") ?>
		</fieldset>
		<? echo $mensagem;  ?>
		</div>
		<? die();
		}else{ ?>
		<body class="body">
		<div class="conteudo">
		<fieldset class="fieldset_atencao">
			<legend>Erro</legend>
			<div>
				<?= utf8_decode("
				<b>Houve um erro ao enviar a mensagem, verifique:</b>
				<ul>
				<li>se há conexão com a internet</li>
				<li>se o e-mail do destinatário foi informado corretamente</li>
				<li>se a conta informada como padrão no sistema realmente existe no servidor</li>
				</ul>
				<b>Dados Informados</b><br />
				$titulo <br />
				De: $fromname | $from <br />
				Para:  $nome |  $destinatario <br />
				<hr />
				") ?>
				<a href='javascript:history.back()' title="Voltar"><img class="image" src="../../images/bt_voltar_pq.png"></a>
			</div><br />
		</fieldset>
		</div>
		<? die();
		}#enviado
	}else{ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Enviando Email de Pedido ao Fornecedor</title>
<style type="text/css">
@import url("../../fieldset.css");
input[type="text"]{
	width: 98%;
}
.conteudo{
width: 700px;
margin: 0px auto;
}

</style>
</head>

<script language="javascript" src="../../jquery.js" type="text/javascript"></script>
<script language="javascript" src="../../jquery.pngFix.js" type="text/javascript"></script>
<script language="javascript" src="../../jquery.tooltip.js" type="text/javascript"></script>


<script language="javascript">

//altura = screen.height;
//largura = screen.width;
//self.resizeTo(760,altura-120);
//self.moveTo(largura/2-380,60);
self.focus();
	
//titulos
$('[title]').tooltip({ 
	track: true, 
	delay: 2, 
	showURL: false, 
	showBody: true, 
	fade: 300 
});
</script>
	
<body class="body">
<div class="conteudo">

<fieldset class="fieldset_atencao">
<legend>Atenção</legend>
	<b>Por Favor, informe e confira os dados abaixo antes de encaminhar este e-mail.</b><br />
	Este e-mail será enviado para o destinatário, com cÃ³pia oculta para a sua conta.<br /><br />
</fieldset>
<? } 

	#registro pai
	$SQL = "SELECT cliente_codigo,cliente_contato,cliente_email,email FROM pedido WHERE codigo=$codigo";
	$SQL = mysql_query($SQL);
	$rs = mysql_fetch_object($SQL);
	$titulo_servico = ($rs->titulo_servico);
	$cliente_codigo = $rs->cliente_codigo;
	$cliente_contato = $rs->cliente_contato;
	$cliente_email = $rs->cliente_email;
	$email = $rs->email;
	
	#cliente
	$SQL = "SELECT nome_fantasia FROM cliente WHERE codigo=$cliente_codigo";
	$SQL = mysql_query($SQL);
	$ag = mysql_fetch_object($SQL);
	$cliente_fantasia = $ag->nome_fantasia;
?>
<form name="producao" method="post" action="">
<fieldset class="fieldset_infos">
<legend>Dados para enviar o E-mail</legend>

	<label for="titulo">Título/assunto</label>
	<input type="text" name="titulo" value="Pedido de fornecimento de Materiais e Serviços: #<?= $codigo ?> - Newdoor">
	<label for="nome">Nome</label>
	<input type="text" name="nome" value="<?= $cliente_contato ?>" />
	<label for="destinatario">Destinatário</label>
	<input type="text" name="destinatario" value="<?= $cliente_email ?>" />
	<label for="agencia">Fornecedor</label>
	<input type="text" name="agencia" value="<?= $cliente_fantasia ?>" />
	<label for="chegada">Previsão de chegada</label>
	<input type="text" name="chegada" value="<?= date('d/m/Y',mktime(0,0,0,date(m),date(d)+3,date(Y))) ?>" />
	<input type="hidden" name="codigo" value="<?= $codigo ?>">
	<div>&nbsp;</div>
	<input type="image" src="../../images/bt_email_send.png" name="submit" title="Enviar o Email" />
</form>
</fieldset>

<fieldset class="fieldset_historico">
	<legend>HistÃ³rico</legend>
	<label>Notificações enviadas</label>
	<div class="div_textarea_scroll"><?= nl2br(str_replace(" . ","\n",$email)) ?></div>
</fieldset>
</div>
</body>
</html>	


