//impressoes e notificações
function print_os(cod){
	win = open("plugins/ordem_de_servico/print_os.php?intranet=1&codigo_producao="+cod,"print_os"+cod);
}
function send_os(cod){
	win = open("plugins/ordem_de_servico/email_os.php?intranet=1&codigo_producao="+cod,"send_os"+cod);
}
function send_transp(cod){
	win = open("plugins/transporte/email_entrega.php?intranet=1&codigo_producao="+cod,"send_transp"+cod);
}
function send_venc(cod){
	win = open("plugins/contas_receber/email_boleto.php?intranet=1&codigo_producao="+cod,"send_venc"+cod);
}

//"width=770,height=450,scrollbars=1,menubar=0,resizable=1"
