<?php
Class CurlRequest{

	var $mh,$conn=array(),$url_list=array();

	public function __construct($url_list=array()){
		if(count($url_list)>0){
			return self::getRequests($url_list);
		}
	}

	public function getRequests($url_list){
		if(count($url_list)==1){
			return self::_single_execute($url_list[0]);
		}else if (count($url_list)>0){
			self::_multi_init($url_list);
			return self::_multi_execute();
		}
	}

	private function _single_init($sUrl){
		$conn = curl_init();
		curl_setopt($conn, CURLOPT_URL, $sUrl);
		curl_setopt($conn, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($conn, CURLOPT_TIMEOUT, 5);
		curl_setopt($conn, CURLOPT_HEADER, FALSE);
		return $conn;
	}

	private function _single_execute($sUrl){
		$conn = self::_single_init($sUrl);
		$ret = curl_exec($conn);
		//var_dump(curl_error($conn));
		curl_close($conn);
		return $ret;
	}

	private function _multi_init($url_list=array()){
			$this->mh = curl_multi_init();
			self::add_url($url_list);
	}

	public function add_url($url_list){
			foreach($url_list as $i => $url){
//				$conn[$i] = curl_init($url);
				$this->url_list[]=$url;
				$conn=self::_single_init($url);
				curl_multi_add_handle($this->mh,$conn);
				$this->conn[]=$conn;
			}
	}

	private function _multi_execute(){
   //URLを取得
    //すべて取得するまでループ
		/*
    $active = null;
    do {
        $mrc = curl_multi_exec($this->mh,$active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
   
	 	while ($active && $mrc == CURLM_OK) {
        if (curl_multi_select($this->mh) != -1) {
            do {
                $mrc = curl_multi_exec($this->mh,$active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
    }
   
    if ($mrc != CURLM_OK) {
        error_log('読み込みエラーが発生しました:'.$mrc);
    }
		 */
		do {
			curl_multi_exec($this->mh, $running);
			curl_multi_select($this->mh);
		} while ($running > 0);


		//ソースコードを取得
		$res = array();
		foreach ($this->url_list as $i => $url) {
			if (($err = curl_error($this->conn[$i])) == '') {
				$res[$i] = curl_multi_getcontent($this->conn[$i]);
			} else {
				erro_log('取得に失敗しました:'.$this->url_list[$i]);
			}
			curl_multi_remove_handle($this->mh,$this->conn[$i]);
			curl_close($this->conn[$i]);
		}
		curl_multi_close($this->mh);

		return $res;
	}
}
