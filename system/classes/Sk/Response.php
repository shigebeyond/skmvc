<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 响应对象
 * 	TODO: 支持响应文件
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-7 下午11:32:07 
 *
 */
class Sk_Response implements Interface_Response
{
	
	/**
	 * 过期的期限
	 * @var string
	 */
	const EXPIRES_OVERDUE = 'Mon, 26 Jul 1997 05:00:00 GMT';
	
	// http状态码及其消息
	public static $messages = array(
		// 信息性状态码 1xx
		100 => 'Continue',
		101 => 'Switching Protocols',
	
		// 成功状态码 2xx
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
	
		// 重定向状态码 3xx
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found', // 1.1
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
	
		// 客户端错误状态码 4xx
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
	
		// 服务端错误状态码 5xx
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		509 => 'Bandwidth Limit Exceeded'
	);
	
	/**
	 * 响应状态码
	 * @var  integer
	*/
	protected $_status = 200;
	
	/**
	 * http协议
	 * @var string
	 */
	protected $_protocol = 'HTTP/1.1';
	
	/**
	 * 响应头部
	 * @var array
	 */
	protected $_headers = array();
	
	/**
	 * 响应主体
	 * @var string
	 */
	protected $_body = '';
	
	/**
	 * 获得与设置响应主体
	 * 
	 * @param string $content
	 * @return string|Sk_Response
	 */
	public function body($content = NULL)
	{
		//getter
		if ($content === NULL)
			return $this->_body;
	
		//setter
		if($content instanceof View)
			$content = $content->render();
		
		$this->_body = (string) $content;
		return $this;
	}
	
	/**
	 * 追加响应主体
	 *
	 * @param string $content
	 * @return string|Sk_Response
	 */
	public function append($content = NULL)
	{
		if($content instanceof View)
			$content = $content->render();
		
		$this->_body .= $content;
		return $this;
	}
	
	/**
	 * 获得与设置http协议
	 * 
	 * @param string $protocol 协议
	 * @return Sk_Response|string
	 */
	public function protocol($protocol = NULL)
	{
		//getter
		if ($protocol === NULL)
			return $this->_protocol;
		
		//setter
		$this->_protocol = strtoupper($protocol);
		return $this;
	}
	
	/**
	 * 读取与设置响应状态码
	 * 
	 * @param string $status 状态码
	 * @return number|Sk_Response
	 */
	public function status($status = NULL)
	{
		// getter
		if ($status === NULL)
			return $this->_status;
		
		// setter
		if(!isset(static::$messages[$status]))
			throw new Exception("无效响应状态码");
		
		$this->_status = (int) $status;
		return $this;
	}
	
	/**
	 * 读取与设置全部头部字段
	 *
	 *       // 获得全部头部字段
	 *       $headers = $response->headers();
	 *
	 *       // 设置头部
	 *       $response->headers(array('Content-Type' => 'text/html', 'Cache-Control' => 'no-cache'));
	 *
	 * @param array $headers 头部字段 
	 * @return mixed
	 */
	public function headers(array $headers = NULL, $merge = TRUE)
	{
		// getter
		if ($headers === NULL)
			return $this->_headers;
		
		// setter
		$this->_headers = $merge ? merge_array($this->_headers, $headers) : $headers;
		return $this;
	}
	
	/**
	 * 获得与设置单个头部字段
	 * 
	 *       // 获得一个头部字段
	 *       $accept = $response->header('Content-Type');
	 *
	 *       // 设置一个头部字段
	 *       $response->header('Content-Type', 'text/html');
	 * 
	 * @param string $key 字段名
	 * @param string $value 字段值
	 * @return string|Sk_Response
	 */
	public function header($key, $value = NULL)
	{
		// getter
		if ($value === NULL)
			return Arr::get($this->_headers, $key);
		
		// setter
		$this->_headers[$key] = $value;
		return $this;
	}
	
	/**
	 * 设置响应缓存
	 *
	 * @param int|string $expires 过期时间
	 * @return string|Response
	 */
	public function cache($expires = NULL) {
		// getter
		if ($expires === NULL) 
		{
			// 无缓存
			if(!isset($this->_headers['Expires']) OR $this->_headers['Expires'] == static::EXPIRES_OVERDUE)
				return FALSE;
			
			// 有缓存
			return $this->_headers['Expires'];
		}
		
		// setter
		if ($expires) { // 有过期时间, 则缓存
			$expires = is_int($expires) ? $expires : strtotime($expires);
			$this->_headers['Last-Modified'] = gmdate('D, d M Y H:i:s', time()) . ' GMT';
			$this->_headers['Expires'] = gmdate('D, d M Y H:i:s', $expires) . ' GMT';
			$this->_headers['Cache-Control'] = 'max-age='.($expires - time());
			if (isset($this->_headers['Pragma']) && $this->_headers['Pragma'] == 'no-cache')
				unset($this->_headers['Pragma']);
		}else{ // 否则, 不缓存
			$this->_headers['Expires'] = static::EXPIRES_OVERDUE;
			$this->_headers['Cache-Control'] = array(
					'no-store, no-cache, must-revalidate',
					'post-check=0, pre-check=0',
					'max-age=0'
			);
			$this->_headers['Pragma'] = 'no-cache';
		}
		return $this;
	}
	
	/**
	 * 发送头部给客户端
	 * @return Response
	 */
	public function send_headers()
	{
		if(headers_sent())
			return;
		
		// 1 状态行
		header($this->_protocol.' '.$this->_status.' '.static::$messages[$this->_status]);
	
		// 2 各个头部字段
		foreach ($this->_headers as $header => $value)
		{
			// cookie字段
			if($key == 'Set-Cookie')
			{
				Cookie::set($value);
				continue;
			}
			
			// 其他字段
			if (is_array($value)) // 多值拼接
				$value = implode(', ', $value);
		
			header(Text::ucfirst($header).': '.$value, TRUE);
		}
		
		// 正文大小
		if (($length = strlen($this->_body)) > 0) {
			header('Content-Length: '.$length);
		}
	
		return $this;
	}
	
	/**
	 * 发送响应该客户端
	 */
	public function send()
	{
		// 先略过, 不排除有其他输出
		// 清空内容缓存
		/* if (ob_get_length() > 0)
			ob_end_clean(); */
		
		// 先头部，后主体
		echo $this->send_headers()->body();
	}
}