<? header("Content-Type: text/html; charset=ISO-8859-1");

  # Corpo da Mensagem e texto e em HTML
  #$text = 'Escreva aqui o texto do seu e-mail';
  	$html = '<strong>Nome:</strong> '.utf8_decode($_POST['form_name'])."<br>";
	$html .= '<strong>Email:</strong> '.$_POST['form_email']."<br>";	
	$html .= '<strong>Sexo:</strong> '.$_POST['radiobt']."<br>";

	
	
	

$headers = "MIME-Version: 1.0\n";
$headers .= "Content-type: text/html; charset=ISO-8859-1\n";
$headers .= "From: {$_POST['email']}\n";
$headers .= "To: guimaraesdeb@gmail.com\n";
$headers .= "Return-Path: guimaraesdeb@gmail.com\n";
$message = $html; 

mail("guimaraesdeb@gmail.com", "Cadastro newsletter enviado através do blog Mr. Cat", $message, $headers);
print utf8_decode($_POST['nome']).", seu comentário foi enviado com sucesso!<br><br>Agradecemos o seu contato e interesse";
?>