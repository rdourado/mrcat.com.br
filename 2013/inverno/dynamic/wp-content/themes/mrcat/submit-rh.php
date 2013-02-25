<?php

require( '../../../wp-load.php' );

$arquivo = $_FILES['cf_uploadfile'];

if ( ! empty( $arquivo['error'][0] ) ) { 
	echo '<p><b>Erro no Upload do arquivo<br>'; 
	switch( $arquivo['erro'][0] ) { 
		case  UPLOAD_ERR_INI_SIZE : 
			echo 'O Arquivo excede o tamanho máximo permitido';
			break;
		case UPLOAD_ERR_FORM_SIZE : 
			echo 'O Arquivo enviado é muito grande';
			break;
		case  UPLOAD_ERR_PARTIAL : 
			echo 'O upload não foi completo';
			break;
		case UPLOAD_ERR_NO_FILE : 
			echo 'Nenhum arquivo foi informado para upload';
			break;
	}
	echo '</b></p>';
	exit;
} 

if ( $arquivo['size'][0] == 0 || $arquivo['tmp_name'][0]== NULL ) { 
	echo '<p><b>Envie um arquivo</b></p>';
	exit;
}
if ( $arquivo['size'][0] > 100002048 ) {
	echo '<p><b>O Arquivo enviado é maior que o limite: ' . round( 100002048 / 1024 ) . 'KB</b></p>';
	exit;
}

// Pega extensão do arquivo
preg_match( "/\.(gif|png|jpg|jpeg){1}$/i", $arquivo["name"][0], $ext );

// Gera um nome único para a imagem
$imagem_nome = md5(uniqid(time())) . "." . $ext[1];

// Caminho de onde a imagem ficará
$imagem_dir =  ABSPATH . "/wp-content/uploads/" . $imagem_nome;

$message .= "<table width=92% bgcolor=#ffffff border=1 bordercolor=#cccccc cellspacing=0><tr bgcolor=#d6d6d6>";
$message .= "<td colspan=4><div align=center>FICHA DE SOLICITACAO DE EMPREGO: </div></td></tr><tr><td colspan=4>";
$message .= "<img src='".home_url('/wp-content/uploads/').$imagem_nome."'></td></tr><tr><td colspan=2><strong><font face=Verdana, Arial, Helvetica, sans-serif>NOME:</strong> ".$_POST['nome']."</td><td colspan=2><strong>IDADE:</strong>".$_POST['idade']."</td></tr>";
$message .= "<tr><td colspan=2><strong >EMAIL:</strong> ".$_POST['email']."</td><td colspan=2><strong>NASCIMENTO:</strong> ".$_POST['nascimento']."</td></tr>";
$message .= "<tr><td><strong >CARGO PRETENDIDO:</strong> ".$_POST['cargo_pret']."</td><td><strong>SALARIO PRETENDIDO:</strong> ".$_POST['sal_pret']."</td><td><strong>DISP.HORARIO:</strong> ".$_POST['horario']."</td><td><strong>LOJAS:</strong> ".$_POST['lojas_possiveis']."</td></tr>";
$message .= "<tr><td colspan=3><div align=right><div align=left><span><strong>ENDEREÇO:</strong> ".$_POST['end_pessoal']."</td><td><strong>CEP:</strong> ".$_POST['cep']." </div>";
$message .= "</div></td></tr><tr><td colspan=4><strong>BAIRRO:</strong> ".$_POST['bairro']." - <strong>CIDADE:</strong> ".$_POST['cidade']." - <strong>ESTADO:</strong> ".$_POST['estado']." </td></tr>";
$message .= "<tr><td colspan=2><strong>TELEFONE:</strong> ".$_POST['tel']."</td><td colspan=2><strong>OUTRO:</strong> ".$_POST['tel2']."</td></tr>";
$message .= "<tr><td colspan=2><strong>NATURALIDADE:</strong> ".$_POST['naturalidade']."</td><td colspan=2><strong>NACIONALIDADE:</strong> ".$_POST['nacional']."</td></tr>";
$message .= "<tr><td colspan=2><strong>RG:</strong> ".$_POST['rg']."</td><td colspan=2><strong>CPF:</strong> ".$_POST['cpf']." </td></tr>";
$message .= "<tr><td colspan=2><strong>ESTADO CIVIL:</strong> ".$_POST['estcivil']."</td><td colspan=2><strong>Nº DE DEPENDENTES:</strong>".$_POST['ndep']."</td></tr>";
$message .= "<tr><td colspan=4><strong>NOME DO PAI:</strong> ".$_POST['pai']."</td></tr>";
$message .= "<tr><td colspan=4><strong>NOME DA MÃE:</strong> ".$_POST['mae']."</td></tr><tr bgcolor=#d6d6d6><td colspan=4>&nbsp;</td></tr>";
$message .= "<tr><td colspan=4><strong>ESCOLARIDADE</strong>: ".$_POST['grau_escolaridade']."</td></tr>";
$message .= "<tr><td colspan=2><strong>CURSO:</strong> ".$_POST['curso1']."</td><td><strong>CONCLUIDO:</strong> ".$_POST['periodo1']."</td><td><strong>INSTITUIÇÃO:</strong> ".$_POST['ensino1']."</td></tr>";

$message .= "<tr bgcolor=#d6d6d6><td colspan=4></td></tr><tr><td colspan=4><strong><u>1ª - EMPRESA</u>:</strong> ".$_POST['emp1']."</td></tr>";
$message .= "<tr><td colspan=2><strong>ENDEREÇO:</strong> ".$_POST['end_emp1']."</td><td colspan=2><strong>TELEFONE:</strong> ".$_POST['tel_emp1']."</td></tr>";
$message .= "<tr><td colspan=2><strong>DATA DE ADMISSÃO:</strong> ".$_POST['adm_emp1']."</td><td><strong>CARGO INICIAL:</strong> ".$_POST['cargo1_emp1']."</td><td><strong>SALÁRIO INICIAL:</strong> ".$_POST['sal1_emp1']."</td></tr>";
$message .= "<tr><td colspan=4><strong>CHEFE IMEDIATO:</strong> </td></tr><tr><td colspan=2><strong>DATA DE DEMISSÃO:</strong> ".$_POST['demit_emp1']."<td><strong>CARGO FINAL:</strong> ".$_POST['cargo2_emp1']."</td><td><strong>SALÁRIO FINAL:</strong> ".$_POST['sal2_emp1']."</td></tr>";
$message .= "<tr><td colspan=4><strong>MOTIVO DE SAÍDA:</strong> ".$_POST['mot_emp1']."</td></tr>";
$message .= "<tr><td colspan=4><strong>RESPONSABILIDADES:</strong> ".$_POST['resp_emp1']."</td></tr>";

$message .= "<tr bgcolor=#d6d6d6><td colspan=4></td></tr><tr><td colspan=4><strong><u>2ª - EMPRESA</u>:</strong> ".$_POST['emp2']."</td></tr>";
$message .= "<tr><td colspan=2><strong>ENDEREÇO:</strong> ".$_POST['end_emp2']."</td><td colspan=2><strong>TELEFONE:</strong> ".$_POST['tel_emp2']."</td></tr>";
$message .= "<tr><td colspan=2><strong>DATA DE ADMISSÃO:</strong> ".$_POST['adm_emp2']."</td><td><strong>CARGO INCIAL:</strong> ".$_POST['cargo1_emp2']."</td><td><strong>SALÁRIO INICIAL:</strong> ".$_POST['sal1_emp2']."</td></tr>";
$message .= "<tr><td colspan=4><strong>CHEFE IMEDIATO:</strong> </td></tr><tr><td colspan=2><strong>DATA DE DEMISSÃO:</strong> ".$_POST['demit_emp2']."<td><strong>CARGO FINAL:</strong> ".$_POST['cargo2_emp2']."</td><td><strong>SALÁRIO FINAL:</strong> ".$_POST['sal2_emp2']."</td></tr>";
$message .= "<tr><td colspan=4><strong>MOTIVO DE SAÍDA:</strong> ".$_POST['mot_emp2']."</td></tr>";
$message .= "<tr><td colspan=4><strong>RESPONSABILIDADES:</strong> ".$_POST['resp_emp2']."</td></tr>";

$message .= "<tr bgcolor=#d6d6d6><td colspan=4></td></tr><tr><td colspan=4><strong><u>3ª - EMPRESA</u>:</strong> ".$_POST['emp3']."</td></tr>";
$message .= "<tr><td colspan=2><strong>ENDEREÇO:</strong> ".$_POST['end_emp3']."</td><td colspan=2><strong>TELEFONE:</strong> ".$_POST['tel_emp3']."</td></tr>";
$message .= "<tr><td colspan=2><strong>DATA DE ADMISSÃO:</strong> ".$_POST['adm_emp3']."</td><td><strong>CARGO INCIAL:</strong> ".$_POST['cargo1_emp3']."</td><td><strong>SALÁRIO INICIAL:</strong> ".$_POST['sal1_emp3']."</td></tr>";
$message .= "<tr><td colspan=4><strong>CHEFE IMEDIATO:</strong> </td></tr><tr><td colspan=2><strong>DATA DE DEMISSÃO:</strong> ".$_POST['demit_emp3']."<td><strong>CARGO FINAL:</strong> ".$_POST['cargo2_emp3']."</td><td><strong>SALÁRIO FINAL:</strong> ".$_POST['sal2_emp3']."</td></tr>";
$message .= "<tr><td colspan=4><strong>MOTIVO DE SAÍDA:</strong> ".$_POST['mot_emp3']."</td></tr>";
$message .= "<tr><td colspan=4><strong>RESPONSABILIDADES:</strong> ".$_POST['resp_emp3']."</td></tr>";

$message .= "<tr><td><strong>INGLES:</strong> ".$_POST['idioma_ingles']."</td><td><strong>ESPANHOL:</strong> ".$_POST['idioma_espanhol']."</td><td><strong>FRANCÊS: </strong>".$_POST['idioma_frances']."</td><td><strong>OUTROS:</strong> ".$_POST['idioma_outro_nome']." ".$_POST['idioma_outro']."</td></tr>";

$message .= "<tr><td colspan=2><strong>TRABALHOU NA EMPRESA:</strong> ".$_POST['trabalhou']."</td><td colspan=2><strong>QUAL CARGO:</strong> ".$_POST['cargo_trabalhou']."</td></tr>";
$message .= "<tr><td colspan=2><strong>JÁ FEZ ENTREVISTA:</strong> ".$_POST['entrevista']."</td><td colspan=2><strong>QUANDO:</strong> ".$_POST['entrevista_quando']."</td></tr>";
$message .= "<tr><td colspan=2><strong>POSSUI PARENTES:</strong> ".$_POST['parente']."</td><td colspan=2><strong>PARENTE:</strong> ".$_POST['nome_parente']."</td></tr>";
$message .= "<tr><td colspan=2><strong>CONHECE ALGUÉM:</strong> ".$_POST['conhece_na_MrCat']."</td><td colspan=2><strong>QUEM:</strong> ".$_POST['quem_conhece_na_MrCat']."</td></tr>";
$message .= "<tr><td colspan=4><strong>MOTIVO POR TRABALHAR  NA EMPRESA:</strong> ".$_POST['porque']."</td></tr><tr><td colspan=4><strong>OBJETIVOS PROFISSIONAIS:</strong> ".$_POST['objetivos']."</td></tr><tr><td colspan=4 bgcolor=#d6d6d6>&nbsp;</td></tr>";

$message .= "<tr><td><strong>MANEQUIM:</strong> ".$_POST['manequim']."</td><td><strong>SAPATO:</strong> ".$_POST['spt']."</td><td><strong>ALTURA:</strong> ".$_POST['altura']."</td><td><strong>PESO:</strong> ".$_POST['peso']."</td></tr>";
$message .= "<tr><td colspan=4><strong>JORNAL e REVISTA:</strong> ".$_POST['jornal_revista']."</td></tr>";
$message .= "<tr><td colspan=2><strong>CANAL DE TV:</strong> ".$_POST['canal']."</td><td colspan=2><strong>VIAJOU:</strong> ".$_POST['viajou']."</td></tr><tr><td colspan=4><strong>OUTRAS RENDAS:</strong> ".$_POST['outra_renda']."</td></tr>";
$message .= "<tr><td colspan=4><strong>HOBBY:</strong> ".$_POST['hobby']."</td></tr><tr><td colspan=4><div align=center><a href=javascript:window.print()><img src=http://www.mrcat.com.br/print.gif width=63 border=0 height=23></a></div></td></tr></table>";




$emaildestinatario = "rh@mrcat.com.br";
//$emaildestinatario = "rafael@mgstudio.com.br";
$emailsender = "news@mrcat.com.br";
$assunto = "Cadastro de RH pelo site";
$mensagemHTML = $message;
$quebra_linha = "\n";

$headers = "MIME-Version: 1.0\n";
$headers .= "Content-type: text/html; charset=utf-8\n";
$headers .= "From: {$emailsender}\n";
$headers .= "To: {$emaildestinatario}\n";

if ( move_uploaded_file( $arquivo["tmp_name"][0], $imagem_dir ) ) {
	if ( !mail( $emaildestinatario, $assunto, $mensagemHTML, $headers ,"-r".$emailsender ) ) { // Se for Postfix
		$headers .= "Return-Path: " . $emailsender . $quebra_linha; // Se "não for Postfix"
		mail( $emaildestinatario, $assunto, $mensagemHTML, $headers );
	}
	echo 'Currículo enviado. Por favor, aguarde nosso retorno.';
} else {
	echo "Ocorreu um Erro durante o Envio";
}
