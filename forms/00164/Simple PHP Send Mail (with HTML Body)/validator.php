<?php 

class Validator{
	
	private $_field_data = array();
	private $_error_messages = array();
	private $_error_prefix	= '<p>';
	private $_error_suffix	= '</p>';
	
	
	function __construct(){
		
	}
	
	
	/**
	 * Установка полей валидации
	 */
	function set_rules($field, $label = '', $rules = ''){
		
		//Нет POST данных
		if (sizeof($_POST) == 0){
			
			return;
		}
		
		//Если правила валидации переданы в виде массива
		if(is_array($field)){
			
			foreach ($field as $row){
				
				//Если не установлено поле валидации или правила валидации,
				//то пропускаем это поле
				if ( ! isset($row['field']) OR ! isset($row['rules'])){
					
					continue;
				}

				//Если название поля не передано используем имя поля
				$label = ( ! isset($row['label'])) ? $row['field'] : $row['label'];

				
				$this->set_rules($row['field'], $label, $row['rules']);
			}
			return;
		}
		
		//Правила валидации должны быть переданы в виде массива,
		//а поле валидации строкой
		if ( ! is_string($field) OR  ! is_array($rules) OR $field == ''){
			
			return;
		}


		//Если название поля не передано используем имя поля
		$label = ($label == '') ? $field : $label;
		
		
		$this->_field_data[$field] = array(
											'field'		=> $field, 
											'label'		=> $label, 
											'rules'		=> $rules,
											'postdata'	=> NULL,
											'error'		=> ''
											);
	}
	
	
	
	/**
	 * Валидация данных
	 */
	function run(){
		
		//Нет POST данных
		if (sizeof($_POST) == 0){
			
			return FALSE;
		}
		
		//Если нет заданных полей для валидации
		if(sizeof($this->_field_data) == 0){
			
			return FALSE;
		}
		

		foreach ($this->_field_data as $field => $row){

			//Получаем POST данные для установленных полей валидации
			//if (isset($_POST[$field])){
				
				$this->_field_data[$field]['postdata'] = (isset($_POST[$field]))? $_POST[$field]: NULL;
                
                //Проверка правил валидации
                $this->checkrule($row,$this->_field_data[$field]['postdata']);
			//}	
		}
		
		
		$total_errors = sizeof($this->_error_messages);
		
		if($total_errors == 0){
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	
	/**
	 * 
	 * Проверка правил валидации
	 */
	function checkrule($field,$postdata){
		
		if(is_array($postdata)){
			
			foreach($postdata as $key => $val){
				
				$this->checkrule($field,$val);
			}
			
			return;
		}
		
		foreach($field['rules'] as $rule => $message){

			$param = FALSE;
			
			if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match))
			{
				$rule	= $match[1]; //Правило валидации
				$param	= $match[2]; //Параметры
			}
			
			//если это правило не входит с состав библиотеки
			if(!method_exists($this, $rule)){
				
				//то будем считать, что это стандартная функция PHP
				//которая принимает только один входной параметр
				if(function_exists($rule)){
					
					$result = $rule($postdata);
					
					//Если функция возвращает булевое значение (TRUR|FALSE),
					//то мы не изменяем переданные POST данные, иначе записываем
					//отформатированные данные					
					$postdata = (is_bool($result)) ? $postdata : $result;
					$this->set_field_postdata($field['field'],$postdata);
					
					continue;
				}				
			}
			else{
				
				$result = $this->$rule($postdata,$param);
			}
			

			$postdata = (is_bool($result)) ? $postdata : $result;
			$this->set_field_postdata($field['field'],$postdata);
			
			//если данные не прошли валидацию
			if($result === FALSE && $message != ''){

				//Формируем сообщение об ошибке
				$error = sprintf($message, $field['label']);
				
				//Сохраняем сообщение об ошибке
				$this->_field_data[$field['field']]['error'] = $error;
				
				if ( ! isset($this->_error_messages[$field['field']])){
					
					$this->_error_messages[$field['field']] = $error;
				}
				
			}
			
			continue;
		}
		
		return;
	}
	
	
	/**
	 * Установка POST данных
	 */
	private function set_field_postdata($field,$postdata){

		if(isset($this->_field_data[$field]['postdata'])){
			
			$this->_field_data[$field]['postdata'] = $postdata;

		}	
	}
	
	
	
	/**
	 * Возвращает POST данные для нужного элемента
	 */
	function postdata($field){
		
		if(isset($this->_field_data[$field]['postdata'])){
			
			return $this->_field_data[$field]['postdata'];
		}
		else return FALSE;
	}
	
	
	
	/**
	 * Очищает все POST данные
	 */
	function reset_postdata(){
		
		$this->_field_data = array();
	}
	
	
	/** 
	 * Возвращает все сообщения об ошибках в виде строки
	 */
	function get_string_errors($prefix = '',$suffix = ''){
		
		
		if (count($this->_error_messages) === 0){
			
			return '';
		}
		
		if ($prefix == '')
		{
			$prefix = $this->_error_prefix;
		}

		if ($suffix == '')
		{
			$suffix = $this->_error_suffix;
		}
		
		// Формируем строку с ошибками
		$str = '';
		foreach ($this->_error_messages as $val)
		{
			if ($val != '')
			{
				$str .= $prefix.$val.$suffix."\n";
			}
		}
		
		return $str;

	}
	
	
	/** 
	 * Возвращает все сообщения об ошибках в виде строки
	 */
	function get_array_errors(){
		
		return $this->_error_messages;
	}
	
	
	/**
	 * Возвращает сообщение об ошибка для указанного поля
	 * @param string
	 */
	function form_error($field){
		
		if(isset($this->_error_messages[$field])){
			
			return $this->_error_prefix.$this->_error_messages[$field].$this->_error_suffix;
		}
		else return FALSE;
	}
	
	
	
	function set_error_delimiters($prefix = '<p>', $suffix = '</p>')
	{
		$this->_error_prefix = $prefix;
		$this->_error_suffix = $suffix;
	}
	
	
	
	/**
	 * Значение не может быть пустым
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function required($str)
	{
		if ( ! is_array($str))
		{
			return (trim($str) == '') ? FALSE : TRUE;
		}
		else
		{
			return ( ! empty($str));
		}
	}
	
	
	/**
	 * 
	 * Проверка поля на целое цисло
	 * @param string
	 */
	function integer($str){
		
		
		return filter_var($str, FILTER_VALIDATE_INT);
	}
	
	
	/**
	 * 
	 * Проверка поля на цисло c плавающей точкой
	 * @param string
	 */
	function float($str){
		
		
		return filter_var($str, FILTER_VALIDATE_FLOAT);
	}
	
	
	/**
	 * Валидация URL
	 * @param string
	 */
	function valid_url($str){

		return filter_var($str, FILTER_VALIDATE_URL);
	}
	
	
	/**
	 * 
	 * Валидация email-адреса
	 * @param string
	 */
	function valid_email($str){
		
		
		return filter_var($str, FILTER_VALIDATE_EMAIL);
	}
	
	
	/**
	 * 
	 * Валидация IP-адреса
	 * @param string
	 */
	function valid_ip($str){
		
		
		return filter_var($str, FILTER_VALIDATE_IP);
	}
	
	
	/**
	 * Match one field to another
	 *
	 * @access	public
	 * @param	string
	 * @param	field
	 * @return	bool
	 */
	function matches($str, $field)
	{
		if ( ! isset($_POST[$field]))
		{
			return FALSE;				
		}
		
		$field = $_POST[$field];

		return ($str !== $field) ? FALSE : TRUE;
	}
	
	
	
	/**
	 * Только буквы латинского алфавита
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */		
	function alpha($str)
	{
		return ( ! preg_match("/^([a-z])+$/i", $str)) ? FALSE : TRUE;
	}
	
	
	
	/**
	 * Проверка капчи
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	bool
	 */	
	function valid_captcha($str,$name){
		
		return (!empty($_SESSION[$name]) && $_SESSION[$name] == $str)? TRUE: FALSE;
	}
	
	
	/**
	 * Проверка даты
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function valid_date($str){
		
		$stamp = strtotime( $str );

		if (!is_numeric($stamp)){
			
			return FALSE;
		}
		
		$month = date( 'm', $stamp );
		$day   = date( 'd', $stamp );
		$year  = date( 'Y', $stamp );
		
		return checkdate($month, $day, $year); 
	}
	
	
	function unique($str,$fields){
		
		list($table,$field) = explode('.',$fields);
		
		$result = mysql_query("SELECT COUNT(*) AS count FROM `".$table."` WHERE ".mysql_real_escape_string($field)."='".mysql_real_escape_string($str)."'");
	
		$myrow  =  mysql_fetch_assoc($result);
		
		return $myrow['count'] == 0;
		
	}
}

?>