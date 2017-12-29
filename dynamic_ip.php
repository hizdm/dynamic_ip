<?php
error_reporting(E_ALL);
/**
 * 动态更换IP采集策略（PHP版）
 * @author  zzl<598515020@qq.com>
 * @version v1.0
 * @copyright http://w3schools.wang
 */
class dynamic{
    public function __construct() {
        // $this->mysql = new pdomysql();
    }

    /**
     * 测试网络(有问题)
     */
    public function testNet() {
    	$errNum = 0; // 采集错误数量

    	for ($i=0; $i < 10; $i++) {
    		$url = '测试网址';

	        $ch  = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			 'Accept: ext/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.87 Safari/537.36',
			 'Connection: Keep-Alive')
			);

	        ob_start();
	        ob_get_clean();

	        ob_start();
	        $out = curl_exec($ch);
	        $output = ob_get_contents();
	        ob_end_clean();
	        curl_close($ch);

	        // 采集内容分析
    	}
    	echo $aa = "Step1:ERROR-number: $errNum \n";

    	// 大于3次默认网络被屏蔽,尝试重启路由器（数量可自由定义）
    	if ($errNum > 3) {
    		echo $bb = "Step2:Reboot Router Begin! \n";
    		$reset_result = $this->resetIp();
    		if ( ! empty($reset_result) && ($reset_result == 'reset-ok')) {
    			echo $cc = "Reboot Router Success! \n";
    			echo $dd = "Step3:Ping Networks Begin! \n";
    			for ($j=1; $j < 4; $j++) {
    				sleep(10);
    				$pingRes = $this->pingIp();
    				echo $ee = "Ping Networks $j \n";
    				if ($pingRes != 'no') {
    					echo $ff = "Ping Networks Success! \n";
    					echo $gg = "Step4:Collect Begin! \n";
    					$onlineip = $this->getIp();
    					$file = 'operate.log';
				    	$handle = fopen($file, 'a');
						$content = $aa . $bb . $cc . $dd . $ee . $ff . $gg . date('Y-m-d H:i:s') . '-' . $onlineip . "\n";
						fwrite($handle, $content);
						fclose($handle);
    					$this->crawl();
    					break;
    				}

    				if ($j == 3) {
    					$dynamic_obj = new dynamic();
						$dynamic_obj->testNet();
						exit;
    				}
    			}
    		}
    	}
    }

    /**
     * 重启路由器
     * @return [type] [description]
     */
    private function resetIp() {
		$username = '路由器用户名';
		$password = '路由器密码';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, '路由器重启地址');
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 70);
		curl_exec($ch);
		curl_close($ch);
		return 'reset-ok';
	}

	/**
	 * 测试网络是否正常
	 * @return [type] [description]
	 */
	private function pingIp() {
		$this->getIp();
		$ip = '211.149.206.77'; // IP地址
		if (PATH_SEPARATOR==':') {
			// linux
		    exec("ping $ip -c 4",$info);
		    if (count($info) < 9) {
		        return 'no';
		    }
		    // 获取ping的时间
		    $str = $info[count($info)-1];
		    return round(substr($str, strpos($str,'/',strpos($str,'='))+1 , 4));
		}
		else {
			// windows
		    exec("ping $ip -n 4",$info);
		    if (count($info) < 10) {
		        return 'no';
		    }
		    // 获取ping的时间
		    $str = $info[count($info)-1];
		    return substr($str,  strripos($str,'=')+1);
		}
	}

	/**
	 * 获取当前IP
	 */
	private function getIp() {
		return file_get_content("http://myip.ipip.net/s",false);
	}

    /**
     * 开始采集
     */
    private function crawl() {
    	// 采集相关操作逻辑
    }
}

$dynamic_obj = new dynamic();
$dynamic_obj->testNet();
