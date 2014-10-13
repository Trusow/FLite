<?
class Flite extends CFormModel{
	public function Parse($file, $flite = 'flite'){
		$fp = fopen($file,'rt');
		if(!$fp){
			return false;
		}
		$text = '';
		while (!feof($fp)){
			$text .= fgets($fp, 1024);
		}
		fclose($fp);
		if(is_string($flite) && $flite == ''){
			$flite = 'flite';
		}
		$sost = 0; // 0 -close 1 -open
		$kav = 0; // 0- close 1-open
		$slash = 0; // 0- close 1 -open
		$search = '';
		$search_str = '';
		$prefix = '';
		$search_length = 0;
		$search_process = 0;
		$actual_sost = 0;
		$reserve_words = Array($flite ,'name', 'password', 'label','prefix','value','end');
		$flite = 0;
		$words_length = Array();
		for($i = 0; $i < count($reserve_words); $i++){
			$words_length[$i] = strlen($reserve_words[$i]);
		}
		$words_process = Array();
		for($i = 0; $i < count($reserve_words); $i++){
			$words_process[$i] = 0;
		}
		$kost = 0;
		$array_el = Array();
		$array_el['name'] = '';
		$array_el['label'] = '';
		$array_el['value'] = '';
		$array_el['password'] = 0;
		$array_ret = Array();
		for($i = 0; $i < strlen($text); $i++){
			if(!$kav && $kost == 0)
			for($a = 0; $a < count($reserve_words); $a++){
				if($reserve_words[$a][$words_process[$a]] != $text[$i]){
					$words_process[$a] = 0;
				}else{
					if($words_process[$a] == $words_length[$a]-1){
						$actual_sost = $a;
						if($reserve_words[$a] == 'password'){
							$array_el['password'] = 1;
						}else if($reserve_words[$a] == 'end'){
							if($search_str != ''){
								$kost = 1;
							}
							$sost = 0;
						}else if($a == 0){
							$array_el['name'] = '';
							$array_el['label'] = '';
							$array_el['value'] = '';
							$array_el['password'] = 0;
							$prefix = '';
							$search_str = '';
							$search_length = 0;
							$search_process = 0;
							$sost = 1;
						}
						$words_process[$a] = 0;
					}else{
						$words_process[$a]++;
					}
				}
			}
			if($sost == 1){
				switch($text[$i]){
					case '"':
					if(!$slash){
						$kav = 1-$kav;
					}else{
						if($kav){
							$search .= '"';
						}
						$slash = 0;
					}
					if(!$kav){
						if($reserve_words[$actual_sost] == 'prefix'){
							$prefix = $search;
						}else if($actual_sost == 0 && $search != ''){
							$reserve_words[0] = $search;
							$words_length[0] = strlen($search);
						}else if($reserve_words[$actual_sost] == 'value'){
							$search_str = $prefix.$search;
							$array_el['value'] = $search;
							$search_length = strlen($search_str);
						}else if($reserve_words[$actual_sost] != 'prefix'){
							$array_el[$reserve_words[$actual_sost]] = $search;
						}
						$search = '';
					}
					break;
					case '\\':
					if($kav){
						$slash++;
						if($slash == 2){
							$search .= '\\';
							$slash = 0;
						}
					}else{
						$slash = 0;
					}
					break;
					default:
					if($kav){
						$search .= $text[$i]; 
					}
					$slash = 0;
					break;
				}
			}else{
				if($search_str != '')
				if($search_str[$search_process] != $text[$i]){
					$search_process = 0;
				}else{
					if($search_process == $search_length-1){
						$array_el['value'] = htmlspecialchars($array_el['value']);
						array_push($array_ret, $array_el);
						$search_str = '';
						$kost = 0;
					}
					$search_process++;
				}
			}
			
		}
		return $array_ret;
	}
	
	
	
	
	
	public function Save($file, $name, $value, $flite = ''){
		$value = htmlspecialchars_decode($value);
		$fp = fopen($file,'rt');
		if(!$fp){
			return false;
		}
		$text = '';
		while (!feof($fp)){
			$text .= fgets($fp, 1024);
		}
		fclose($fp);
		if(is_string($flite) && $flite == ''){
			return false;
		}
		$ret_text = $text;
		$sost = 0; // 0 -close 1 -open
		$kav = 0; // 0- close 1-open
		$slash = 0; // 0- close 1 -open
		$search = '';
		$search_str = '';
		$prefix = '';
		$search_name = false;
		$search_start = 0;
		$search_stop = 0;
		$search_length = 0;
		$search_process = 0;
		$actual_sost = 0;
		$search_value = '';
		$kost = 0;
		$reserve_words = Array($flite ,'name', 'password', 'label','prefix','value','end');
		$flite = 0;
		$words_length = Array();
		for($i = 0; $i < count($reserve_words); $i++){
			$words_length[$i] = strlen($reserve_words[$i]);
		}
		$words_process = Array();
		for($i = 0; $i < count($reserve_words); $i++){
			$words_process[$i] = 0;
		}
		$array_ret = Array();
		for($i = 0; $i < strlen($text); $i++){
			if(!$kav && $kost == 0)
			for($a = 0; $a < count($reserve_words); $a++){
				if($reserve_words[$a][$words_process[$a]] != $text[$i]){
					$words_process[$a] = 0;
				}else{
					if($words_process[$a] == $words_length[$a]-1){
						$actual_sost = $a;
						if($reserve_words[$a] == 'end'){
							if($search_str != ''){
								$kost = 1;
							}
							$sost = 0;
						}else if($a == 0){
							$prefix = '';
							$search_str = '';
							$search_value = '';
							$search_length = 0;
							$search_process = 0;
							$search_name = false;
							$search_start = 0;
							$search_stop = 0;
							$sost = 1;
						}
						$words_process[$a] = 0;
					}else{
						$words_process[$a]++;
					}
				}
			}
			if($sost == 1){
				switch($text[$i]){
					case '"':
					if(!$slash){
						$kav = 1-$kav;
					}else{
						if($kav){
							$search .= '"';
						}
						$slash = 0;
					}
					if($kav){
						if($search_name && $reserve_words[$actual_sost] == 'value' && $search_start == 0){
							$search_start = $i;
						}
					}
					if(!$kav){
						if($search_name && $search_start != 0){
							$search_stop = $i;
						}
						if($reserve_words[$actual_sost] == 'prefix'){
							$prefix = $search;
						}else if($actual_sost == 0 && $search != ''){
							$reserve_words[0] = $search;
							$words_length[0] = strlen($search);
						}else if($reserve_words[$actual_sost] == 'value'){
							$search_str = $prefix.$search;
							$search_value = $search;
							$search_length = strlen($search_str);
						}else if($reserve_words[$actual_sost] == 'name'){
							if($search == $name){
								$search_name = true;
							}
						}
						$search = '';
					}
					break;
					case '\\':
					if($kav){
						$slash++;
						if($slash == 2){
							$search .= '\\';
							$slash = 0;
						}
					}else{
						$slash = 0;
					}
					break;
					default:
					if($kav){
						$search .= $text[$i]; 
					}
					$slash = 0;
					break;
				}
			}else{
				if($search_str != '')
				if($search_str[$search_process] != $text[$i]){
					$search_process = 0;
				}else{
					if($search_process == $search_length-1){
						
						if($search_name){
							$value_new = '';
							for($s = 0; $s < strlen($value); $s++){
								switch($value[$s]){
									case '"':
									$value_new .= '\\"';
									break;
									case '\\':
									$value_new .= '\\\\';
									break;
									default:
									$value_new .= $value[$s];
									break;
								}
							}
							$ret_text = substr($text, 0, $i-strlen($search_value)+1).$value.substr($text, $i+1);
							$ret_text = substr($ret_text, 0, $search_start+1).$value_new.substr($ret_text, $search_stop);
							$fp = fopen($file, 'w');
							if(!$fp){
								return false;
							}
							fwrite($fp, $ret_text);
							fclose($fp);
							return true;
						}
						$search_name = false;
						$search_str = '';
						$kost = 0;
					}
					$search_process++;
				}
			}
			
		}
		return false;
	}
} 
