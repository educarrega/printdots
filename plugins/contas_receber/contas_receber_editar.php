<?
#require_once "../../conn.php";
if($gravar){

	#vencimentos
	$table = "contas_receber";
	if(strstr($gravar,"excluir")){
		$condition = " WHERE codigo=$codigo";
		del($table,$condition);
		$codigo = "";
	}
	if(strstr($gravar,"incluir")){
		$cols = "codigo_producao,codigo_cliente";
		$values = "'$codigo_producao','$codigo_cliente'";
		//echo $parcelas;
		//echo $codigo_producao;
		//echo $codigo_cliente;
		//if($codigo) $loop=1;
		for($p=0;$p<$parcelas;$p++){
			ins($table,$cols,$values);
			//resgata o ultimo registro incluido na linha ins
			$max = "MAX(codigo) as codigo";
			$condition = " WHERE codigo_producao='$codigo_producao'";
			$SQLc = sel($table,$max,$condition);
			$rsc = mysql_fetch_array($SQLc);
			//seta o novo codigo e modo gravar
			$codigos[] = $rsc[codigo];
			//os dados ja preenchidos seguem para o proximo passo normalmente como uma alteracao
		}
		$gravar = "alterar";
	}
	if(strstr($gravar,"alterar")){
		//print_r($codigos);
		if(count($codigos)==0) $codigos[] = $codigo;
		if($data_vencimento=='00/00/0000') $data_vencimento = date("d/m/Y");
		$date = mydate($data_vencimento);
		foreach($codigos as $ch=>$vl){
			$data_vencimento = $date;
			$condition = " WHERE codigo=$vl";
			$cols = "
			codigo_cliente='$codigo_cliente',
			data_vencimento='$data_vencimento',
			codigo_banco='$codigo_banco',
			codigo_carteira='$codigo_carteira',
			documento='$documento',
			obs='$obs',
			bv_fornecedor='$bv_fornecedor',
			bv_documento='$bv_documento',
			email='$email',
			";
			$_valor = $valor ? "'".str_replace(",",".",$valor)."'" : "NULL";
			$_bv_valor = $bv_valor ? "'".str_replace(",",".",$bv_valor)."'" : "NULL";
			$_data_pagamento = mydate($data_pagamento) ? "'".mydate($data_pagamento)."'" : "NULL";
			$_bv_data_pagamento = mydate($bv_data_pagamento) ? "'".mydate($bv_data_pagamento)."'" : "NULL";
			$cols .= "
			valor=$_valor,
			bv_valor=$_bv_valor,
			data_pagamento=$_data_pagamento,
			bv_data_pagamento=$_bv_data_pagamento
			";
			//echo $cols.$condition; die; 
			//echo $vl;
			upd($table,$cols,$condition);
			$codigo = $v;
			if($periodo>0){
			$data_vencimento = date("Y-m-d", strtotime( "+$periodo day", strtotime( $date ) ) );
			$date = $data_vencimento;
			}
		}
		atualizar();
	}

}
$uid = date("His");
?>
