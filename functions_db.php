<?php
@session_start();
//Conecta ao servidor e seleciona o banco de dados
function conn($server,$user,$password,$dbase,$retorno=false){
	if($GLOBALS["return"] || $retorno) echo $server.$user.$password.$dbase;
	$connect = mysql_connect($server,$user,$password) or die(mysql_error()."Ops!");
	$database = mysql_select_db($dbase,$connect) or die(mysql_error()."<br />Houve algum problema, não foi possivel localizar o banco de dados solicitado para o site");
}

//Desconectar do banco de dados
function dconn(){
	@mysql_close();
}

//Inserir dados no banco de dados
function ins($table,$cols,$values,$retorno=false){
	$sql = "INSERT INTO ".$table."(".$cols.") VALUES(".$values.")";
	if($GLOBALS["return"] || $retorno) echo $sql;
	return mysql_query($sql);
}

//Apagar algum registro
function del($table,$condition,$retorno=false){
	$sql = "DELETE FROM ".$table;
	if(trim($condition)){
		$sql .= " $condition";
	}
	if($GLOBALS["return"] || $retorno) echo $sql;
	return mysql_query($sql);
}

//Atualizar algum registro
function upd($table,$cols,$condition,$retorno=false){
	$sql = "UPDATE ".$table." SET ".utf8_decode($cols);
	if(trim($condition)){
		$sql .= " $condition";
	}
	if($GLOBALS["return"] || $retorno) echo $sql;
	return mysql_query($sql);
}

//Seleciona os dados
function sel($table,$cols,$condition,$retorno=false){
	$sql = "SELECT ".$cols." FROM ".$table;
	if(trim($condition)){
		$sql .= " $condition";
	}
	if($GLOBALS["return"] || $retorno) echo $sql;
	return mysql_query($sql);
}

//Seleciona um resultado e entrega num return
// utilizar echo campo ou <?= campo(...)
function campo($table,$field,$conditions){
	$cp = mysql_fetch_array(sel("$table","$field","$conditions"));
	return $cp[$field];
}

// comentario da coluna
function comment($dbase,$table,$campo,$retorno=false){
	if($table && $campo){
	$sqlc = "SELECT COLUMN_COMMENT AS comment 
	FROM INFORMATION_SCHEMA.COLUMNS
	WHERE table_schema = '$dbase'
	AND table_name = '$table'
	AND column_name = '$campo'";
	$qr = mysql_query($sqlc);
	$rs = mysql_fetch_array($qr);
	$comment = $rs[comment];
	if($GLOBALS["return"] || $retorno) echo $sqlc;
	if(strlen($comment)>1){
		return ucfirst($comment);
	}else{
		return ucfirst($campo);
	}
	}
}

// se uma coluna existe numa tabela
function is_column($dbase,$table,$campo,$retorno=false){
	$sqlc = "SELECT COLUMN_NAME 
	FROM INFORMATION_SCHEMA.COLUMNS
	WHERE table_schema = '$dbase'
	AND table_name = '$table'
	AND column_name = '$campo'";
	$qr = mysql_query($sqlc);
	$rs = mysql_fetch_array($qr);
	$column = $rs[COLUMN_NAME];
	if($GLOBALS["return"] || $retorno) echo $sqlc;
	if(strlen($column)>1){
		return true;
	}
}

function is_table($dbase,$table,$retorno=false){
	$sqlc = "SELECT TABLE_NAME 
	FROM INFORMATION_SCHEMA.TABLES
	WHERE table_schema = '$dbase'
	AND table_name = '$table'";
	$qr = mysql_query($sqlc);
	$rs = mysql_fetch_array($qr);
	$tbl = $rs[TABLE_NAME];
	if($GLOBALS["return"] || $retorno) echo $sqlc;
	if(strlen($tbl)>1){
		return true;
	}
}

//comentario de tabelas
function tcomment($dbase,$table,$retorno=false){
	if($table){
	$sqlc = "SELECT TABLE_COMMENT AS comment 
	FROM INFORMATION_SCHEMA.TABLES
	WHERE table_schema = '$dbase'
	AND table_name = '$table'";
	$qr = mysql_query($sqlc);
	$rs = mysql_fetch_array($qr);
	$comment = $rs[comment];
	if($GLOBALS["return"] || $retorno) echo $sqlc;
	if(strlen($comment)>1){
		return ucfirst($comment);
	}else{
		return ucfirst($table);
	}
	}
}
?>
