<?php 

session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 'On');

require_once 'validator.php';

$validator = new Validator();

$validator->set_error_delimiters('<div class="error">', '</div>');

//Задаем правила валидации
$rules = array(
	array(
		'field' => 'user_name',
		'label' => 'Ваше имя',
		'rules' => array(
                        'trim' => '', //Обрезаем пробелы по бокам
                        'strip_tags' => '', // Удаляем HTML и PHP теги
                        'required' => 'Поле %s обязательно для заполнения'
					)
	),
	array(
		'field' => 'user_email',
		'label' => 'Ваш e-mail адрес',
		'rules' => array(
                        'trim' => '',
                        'required' => 'Поле %s обязательно для заполнения',
                        'valid_email' => 'Поле %s должно содержать правильный email-адрес'
					)
	),
	array(
		'field' => 'user_url',
		'label' => 'URL адрес сайта',
		'rules' => array(
                        'trim' => '',
                        'valid_url' => 'Поле %s должно содержать правильный URL адрес'
					)
	),
    array(
		'field' => 'subject',
		'label' => 'Тема письма',
		'rules' => array(
                        'trim' => '', //Обрезаем пробелы по бокам
                        'strip_tags' => '', // Удаляем HTML и PHP теги
                        'required' => 'Поле %s обязательно для заполнения'
					)
	),
    array(
		'field' => 'text',
		'label' => 'Текст сообщения',
		'rules' => array(
                        'trim' => '', //Обрезаем пробелы по бокам
                        'strip_tags' => '', // Удаляем HTML и PHP теги
                        'required' => 'Поле %s обязательно для заполнения'
					)
	),
    array(
		'field' => 'keystring',
		'label' => 'Капча',
		'rules' => array(
                        'trim' => '', //Обрезаем пробелы по бокам
                        'required' => 'Вы не ввели цифры изображенные на картинке',
    					'valid_captcha[keystring]' => 'Вы ввели не правильный цифры с картинки'
					)
	)
);

//Устанавливаем правила валидации
$validator->set_rules($rules);
$message = '';

//Запускаем валидацию POST данных
if($validator->run()){
	
	//Здесь впишите свой e-mail адрес
	//на негу будут приходить уведомления с формы
	$to = 'mail@domain.name';
 
	$from = "=?UTF-8?b?" . base64_encode($validator->postdata('user_name')) . "?=";
	$subject = "=?UTF-8?b?" . base64_encode( $validator->postdata('subject') ) . "?=";
	
	$mail_body = "Поступил новый ответ от формы обратной связи.\r\nАвтор оставил такие данные:\r\n";
	
	//Формируем текст сообщения
	foreach($rules as $rule){
		if($rule['field'] == 'keystring') continue;
		$mail_body .= $rule['label'].': '.$validator->postdata($rule['field'])."\r\n";
	}
	
	$header = "MIME-Version: 1.0\n";
	$header .= "Content-Type: text/plain; charset=UTF-8\n";
	$header .= "From: ". $from . " <" . $validator->postdata('user_email'). ">";

	//Отправка сообщения
	if(mail($to, $subject, $mail_body, $header)){
		
		$message = '<div class="error">Ваше сообщение успешно отправлено!</div>';
		
		//Очищаем форму обратной связи
		$validator->reset_postdata();
	}
	else{
		
		$message = '<div class="error">Ваше сообщение не отправлено!</div>';
	}
}
else{
	
    //Получаем сообщения об ошибках в виде строки
	$message = $validator->get_string_errors();
	
    //Получаем сообщения об ошибках в виде массива
	$errors = $validator->get_array_errors();

}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Форма обратной связи для сайта с капчей</title>

<style type="text/css">
<!--
body {
	font: 100% Verdana, Arial, Helvetica, sans-serif;
	background: #666666;
	margin: 0; 
	padding: 0;
	text-align: center; 
	color: #000000;
}
.oneColFixCtrHdr #container {
	width: 780px;
	background: #FFFFFF;
	margin: 15px auto;
	border: 1px solid #000000;
	text-align: left; 
}
.oneColFixCtrHdr #mainContent {
	padding: 0 20px;
	background: #FFFFFF;
	position: relative;
}
.oneColFixCtrHdr #footer {
	padding: 0 10px;
	background:#DDDDDD;
}
.oneColFixCtrHdr #footer p {
	margin: 0;
	padding: 10px 0;
}


form.form{
    width: 600px;
    margin: 0 auto;
}

form.form div {
    padding:4px;
    margin: 4px 0;
    position:relative;
}

form.form input.text,
.textarea {
    padding:5px 10px;
    height:20px;
    border:1px solid #ddd;
    color:#333;
    background:url(images/bginput.jpg) repeat-x bottom #fff;
    position:relative;
    z-index:2;
    font-size: 16px;
}

form.form input.text {
    width:290px;
}

form.form .textarea {
    height:150px;
    width:290px;
}

form.form label {
    float:left;
    width:120px;
    text-align:right;
    margin-right:15px;
    font-weight:bold;
    color:#666;
    font-size: 13px;
}

form.form .btn {
    display:block;
    height:31px;
    padding:0 10px;
    background:url(images/bgbtn.jpg) repeat-x;
    color:#565e62;
    font-weight:bold;
    font-size:12px;
    border:1px solid #e1e0df;
    outline:none;
    cursor: pointer;
}

/* CSS3 */
form.form .btn,
form.form .text,
form.form .textarea { 
-moz-border-radius:8px;
-webkit-border-radius:8px;
border-radius:8px;
}


div.error_field{
    background: #FEDAFB;
    border: 1px solid #FA74F0;
}

div.errors{
    width: 580px;
    margin: 15px auto;
    padding: 10px;
    border: 1px solid #ccc;
    background: #FDFEC2;
}

div.errors .error{
    color: red;
    font-weight: bold;
    font-size: 12px;
    margin: 5px;
}


-->
</style>
</head>
<body class="oneColFixCtrHdr">

<div id="container">
  
  <div id="mainContent">

   <?=(!empty($message))? '<div class="errors">'.$message.'</div>': ''?>
   <form action="" method="post" class="form">
    <div <?=(!empty($errors['user_name']))? 'class="error_field"': '';?>>
    	<label>Ваше имя:</label>
    	<input type="text" class = "text" name="user_name" value="<?=$validator->postdata('user_name');?>" />
    </div> 
    
    <div <?=(!empty($errors['user_email']))? 'class="error_field"': '';?>>
    	<label>Ваш e-mail адрес:</label>
    	<input type="text" class = "text" name="user_email" value="<?=$validator->postdata('user_email');?>" />
    </div> 
    
    <div <?=(!empty($errors['user_url']))? 'class="error_field"': '';?>>
    	<label>URL адрес сайта:</label>
    	<input type="text" class = "text" name="user_url" value="<?=$validator->postdata('user_url');?>" />
    </div> 
    
    <div <?=(!empty($errors['subject']))? 'class="error_field"': '';?>>
    	<label>Тема письма:</label>
    	<input type="text" class = "text" name="subject" value="<?=$validator->postdata('subject');?>"/>
    </div> 
    
    <div class="area<?=(!empty($errors['text']))? ' error_field': '';?>">
    	<label>Текст сообщения:</label>
    	<textarea cols="40" class = "textarea" rows="5" name="text"><?=$validator->postdata('text');?></textarea>
    </div> 
    
    <div <?=(!empty($errors['keystring']))? 'class="error_field"': '';?>>
    	<label class="captcha">Введите цифры изображенные на картинке:</label>
    	<div class="capth_images"><?php require 'captcha.php';?></div>
    	<input type="text" class = "text" name="keystring" value=""/>
    </div> 

    <div>
    	<label>&nbsp;</label>
    	<input type="submit" class="btn" value="Отправить сообщение" />
    </div>

	
 </form> 
 
</div>


</div>
</body>
</html>
