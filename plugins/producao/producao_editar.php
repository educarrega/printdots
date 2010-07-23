<?
@session_start();
#require_once "../../conn.php";

if($atualiza)atualizar();

if($gravar){

	if($gravar == "incluir_os"){
		$table = "producao";
		$cols = "titulo_servico, autorizado_data, data_prevista, status, id";
		$values = "'Nova', '". date("Y-m-d") ."','". date("Y-m-d") ."','6','".date("dmYHis")."'";
		ins($table,$cols,$values);
		#obter o codigo da inclusao
		$SQLm = "SELECT MAX(codigo) as codigo FROM producao";
		$QRm = mysql_query($SQLm);
		$rsm = mysql_fetch_array($QRm);
		$codigo = $rsm[codigo]; #recolhido pelo pop editar_os para obter o codigo da ultima os
		if($retorno) echo $$retorno; //usado na inclusao
	}

	#duplicando a OS
	if($gravar == "duplicar_os"){
		$table = "producao";
		$cols = "titulo_servico";
		$values = "'$titulo_servico'";
		ins($table,$cols,$values);
		#echo("$table - $cols - $values");
		
		#obter o codigo da inclusao futura e incluida
		$SQLm = "SELECT MAX(codigo) as codigo FROM producao";
		$QRm = mysql_query($SQLm);
		$rsm = mysql_fetch_array($QRm);
		$codigo_futuro = $rsm[codigo];
		#echo "$rsm[codigo]***";

		#faturamento
		$SQLf = "SELECT * FROM contas_receber WHERE producao=$codigo AND agencia=$agencia";
		$QRf = mysql_query($SQLf);
		while($rsf = mysql_fetch_array($QRf)){
			$table = "contas_receber";
			$cols = "producao,agencia,data_venc,valor,bv_valor";
			$values = "'$codigo_futuro','$agencia','".date("Y-m-d")."','$rsf[valor]','$rsf[bv_valor]'";
			ins($table,$cols,$values);
			#echo("$table - $cols - $values");
			#die();
		}
		
		#producao_itens
		$SQLi = "SELECT * FROM producao_itens WHERE codigo_producao=$codigo";
		$QRi = mysql_query($SQLi);
		while($rsp = mysql_fetch_array($QRi)){
			$table = "producao_itens";
			$cols = "codigo_producao, codigo_produto, codigo_equipamento, quantidade, largura, altura, m2u, m2t, descricao, status";
			$values = "'$codigo_futuro', '$rsp[codigo_produto]', '$rsp[codigo_equipamento]', '$rsp[quantidade]', '$rsp[largura]', '$rsp[altura]', '$rsp[m2u]', '$rsp[m2t]', '$rsp[descricao]', '1'";
			ins($table,$cols,$values);
		}
		
		#copiando a imagem
		chdir("../");
		$diretorio = getcwd()."/producao/thumbs/";
		@copy("$diretorio$codigo.jpg","$diretorio$codigo_futuro.jpg");
		
		$codigo = $codigo_futuro;
		$gravar = "editar_os";
		$autorizado_data = mydate(date("Y-m-d"));
		$data_prevista = mydate(date("Y-m-d"));
		$historico = '';
		$historico_text = '';
		$historico_data = '';
		$historico_user = '';
		$historico_completo = '';
		$email_transporte = "";
		$data_entrega = '';
		$transporte_obs = '';
		$email = '';
		$email_transporte = '';
		$email_vencimento = '';
		$impressao = '';
		$status = 6;
		atualizar();
	}
	
	#alterar OS
	if($gravar == "editar_os"){
		$table = "producao";
		$cols = "
		titulo_servico='$titulo_servico', 
		agencia = '$agencia', 
		autorizado_data = '".mydate($autorizado_data)."', 
		data_prevista = '".mydate($data_prevista)."',  
		digital_obs = '$digital_obs',
		cod_transporte = '$cod_transporte', 
		frete_tipo = '$frete_tipo', 
		entrega_obs = '$entrega_obs', 
		orcamento_obs = '$orcamento_obs', 
		transporte_obs = '$transporte_obs', 
		email = '$email', 
		impressao = '$impressao', 
		email_transporte = '$email_transporte',
		";
		$data_entrega = mydate($data_entrega);
		
		if($transporte_obs) $status = '5';
		
		if(!strstr($data_entrega,'000') && !$transporte_obs) $status = '11';		
		
		if($status=='5' && !$transporte_obs) $status = '11';
		
		if($status=='11' || $status=='5' || $transporte_obs){	
			$data_entrega = strstr($data_entrega,'000') ? date("Y-m-d") : $data_entrega;
		}
		
		$cols .= "data_entrega='$data_entrega',
		status = '$status'";
		
		#controle de historico
		//atualizar_historico('status',$status,$codigo);

		//die($cols);
				
		$condition = " WHERE codigo=$codigo";
		upd($table,$cols,$condition);
		
		if(isset($agencia)){
			#contas_receber tem que usar a agencia atual
			$table = "contas_receber";
			$cols = "agencia='$agencia'";
			$condition = " WHERE producao=$codigo";
			upd($table,$cols,$condition);
		}
				
		$data_prevista = mydate($data_prevista);
		atualizar();
		die;
	}

	#atualizacoes independentes
	
	function atualizar_historico($campos,$valores,$codigo){
		$sta = mysql_fetch_array(sel("producao","status","WHERE codigo=$codigo"));
		if($sta[status]!=$status){
			$rst = mysql_fetch_array(sel("producao_status","titulo","WHERE codigo=$status"));
			$alteracao = "\nStatus: $sta[status] para $rst[titulo]";
		}
		$historico_completo = strip_tags($historico);
		$historico_data = date("Y-m-d");
		$historico_user = $_SESSION[user_detalhes];
		$historico_text = "Alteração de ordem de serviço".$alteracao;
		$historico = $historico_data." | ".$historico_user."\n".$historico_text."\n\n".$historico_completo;
		$cols .= "historico = '$historico'";
	}
	
	if($gravar == "incluir_itens"){
		$table = "producao_itens";
		$cols = "codigo_producao, codigo_produto, codigo_equipamento, quantidade, largura, altura, m2u, m2t, descricao, status";
		$values = "'$producao', '$codigo_produto', '$codigo_equipamento', '$quantidade', '$largura', '$altura', '$m2u', '$m2t', '$descricao', '1'";
		ins($table,$cols,$values);
		#obter o codigo da inclusao
		$SQLm = "SELECT MAX(codigo) as codigo FROM $table";
		$QRm = mysql_query($SQLm);
		$rsm = mysql_fetch_array($QRm);
		$codigo = $rsm[codigo]; #recolhido pelo pop editar_os para obter o codigo da ultima os
		if($retorno) echo $$retorno; //usado na inclusao
		atualizar();
		#die;
	}

	if($gravar == "editar_itens"){
		$table = "producao_itens";
		$cols = "
		codigo_producao='$producao',
		codigo_produto='$codigo_produto',
		codigo_equipamento='$codigo_equipamento',
		quantidade = '$quantidade', 
		altura = '$altura', 
		largura = '$largura', 
		m2u = '$m2u', 
		m2t = '$m2t',
		descricao='$descricao',
		status='1'";
		$condition = " WHERE codigo=$codigo";
		upd($table,$cols,$condition);
		if($retorno) echo $$retorno;
		$codigo = "";
		atualizar();
		#die;
	}
	if($gravar == "excluir_itens"){
		$table = "producao_itens";
		$condition = "WHERE codigo='$codigo'";
		del($table,$condition);
		atualizar();
	}

	if(strstr($gravar,"prioridade_os")){
		$table = "producao";
		if(strstr($gravar,"mais")){
			$cols = "digital_prioridade='". ($digital_prioridade+1) ."'";
			$condition = " WHERE codigo=$codigo";
			upd($table,$cols,$condition);
		}else{
			$cols = "digital_prioridade='". ($digital_prioridade-1) ."'";
			$condition = " WHERE codigo=$codigo";
			upd($table,$cols,$condition);
		}
		if($retorno) echo $$retorno;
		$codigo = "";
		#die;
		atualizar();
	}
	
	if($gravar == "status_os"){
		$table = "producao";
		#die("$table data_entrega, transporte_obs, status WHERE codigo=$codigo");
		$rsd = mysql_fetch_array(sel($table,"data_entrega, transporte_obs, status","WHERE codigo=$codigo"));
		$data_entrega = $rsd[data_entrega];
		$transporte_obs = $rsd[transporte_obs];
		if($rsd[status]=='11' || $rsd[status]=='5'){
			if($status=='11' || $status=='5'){
				$status = $status;
			}else{
				$status = $rsd[status];
			}
		}
		
		if($transporte_obs) $status = '5';
		
		if(!strstr($data_entrega,'000') && !$transporte_obs) $status = '11';		
		
		if($status=='5' && !$transporte_obs) $status = '11';
		
		if($status=='11' || $status=='5' || $transporte_obs){	
			$data_entrega = strstr($data_entrega,'000') ? date("Y-m-d") : $data_entrega;
		}
		
		$cols = "status='$status'";
		if($status=='11' || $status=='5') $cols .= ", data_entrega='$data_entrega'";
		$condition = " WHERE codigo=$codigo";
		//die($cols);
		upd($table,$cols,$condition);
		if($retorno) echo $$retorno;
		atualizar();
	}
	
	if($gravar == "historico_os"){
		$historico = $historico_completo;	
		if($historico_text) $historico = "<b>".$historico_data." | <i>".$historico_user."</i></b><br />".$historico_text."<hr />".$historico_completo;
		$table = "producao";
		$cols = "historico='".($historico)."'";
		$condition = " WHERE codigo=$codigo";
		upd($table,$cols,$condition);
		if($retorno) echo $$retorno;
		#die;
		atualizar();
	}

	if($gravar == "faturamento_os"){
		$table = "producao";
		$cols = "orcamento_obs='$orcamento_obs'";
		$condition = " WHERE codigo=$codigo";
		upd($table,$cols,$condition);
		if($retorno) echo $$retorno;
		#die;
		atualizar();
	}

}
$uid = date("His");
?>
