<? 
session_start();
#require_once "../../conn.php";
$table = "contas_pagar";
if($gravar=="incluir"){
	
	if(!$parcela){
		$parcela = 1;
	}else{
		$valor_acrescimo = ($valor_acrescimo/$parcela);
		$valor = ($valor/$parcela);
	}
	
	$data_vencimento = mydate($data_vencimento);
	for($i=0;$i<$parcela;$i++){
		$cols = "codigo_cliente,
		codigo_pedido,
		codigo_carteira,
		codigo_banco,
		sequencia_carteira,
		documento,
		valor_acrescimo,
		valor,
		data_lancamento,
		data_vencimento,
		data_pagamento,
		descricao";
		$values = "'$codigo_cliente',
		'$codigo_pedido',
		'$codigo_carteira',
		'$codigo_banco',
		'$sequencia_carteira',
		'$documento',
		'$valor_acrescimo',
		'$valor',
		'". date(Y."-".m."-".d) ."',
		'". $data_vencimento ."',
		'". mydate($data_pagamento) ."',
		'$descricao'";
		ins($table,$cols,$values);
		//echo $values;
		
		$date = $data_vencimento;
		$data_vencimento = date("Y-m-d", strtotime( "+$periodo day", strtotime( $date ) ) );
	}

}

if($gravar=="excluir"){
	del($table,"WHERE codigo=$codigo_parcela");
}

if($gravar=="editar"){
	$condition = " WHERE codigo=$codigo_parcela";
	$cols = "codigo_cliente='$codigo_cliente',
	codigo_pedido='$codigo_pedido',
	codigo_carteira='$codigo_carteira',
	codigo_banco='$codigo_banco',
	sequencia_carteira='$sequencia_carteira',
	documento='$documento',
	valor_acrescimo='$valor_acrescimo',
	valor='$valor',
	data_vencimento='".mydate($data_vencimento)."',
	data_pagamento='".mydate($data_pagamento)."',
	descricao='$descricao'";
	echo $cols.$condition;
	upd($table,$cols,$condition);
}

if($gravar=="status"){
	$condition = " WHERE codigo=$codigo";
	$cols = "
	ativo = '$status'
	";
	upd($table,$cols,$condition);
	die;
}

?>