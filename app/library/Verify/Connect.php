<?php
namespace Verify;
/**
 * 链接类
 */
abstract class Connect
{
	private static $_curl     = null; //curl链接状态,每次请求使用同一个，
	protected static $_cookie = null; //cookie保存位置

	/**
	 * post数据
	 * @method post
	 * @param  [type] $url  [description]
	 * @param  [数组] $data [description]
	 * @author NewFuture
	 */
	protected static function getHtml($url, $data = null, $from_encode = '')
	{
		$result = self::request($url, $data);
		if ($from_encode)
		{
			$result = iconv($from_encode, 'UTF-8//IGNORE', $result);
		}
		return $result;
	}

	/**
	 * 请求数据
	 * @method request
	 * @param  [type]  $url  [description]
	 * @param  [type]  $data [description]
	 * @return string        [请求结果body]
	 * @author NewFuture
	 */
	protected static function request($url, $data = null)
	{
		$ch = self::_getCURL();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1); //显示请求头
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //跟随重定向
		if (self::$_cookie)
		{
			/*加入cookie*/
			curl_setopt($ch, CURLOPT_COOKIE, self::$_cookie);
		}
		if ($data)
		{
			/*附加数据*/
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}

		$response            = curl_exec($ch);
		list($header, $body) = explode("\r\n\r\n", $response, 2);
		if (preg_match('/Set-Cookie:(.*);/iU', $header, $matchs))
		{
			self::$_cookie = $matchs[1];
		}
		return $body;
	}

	/**
	 * 解析姓名
	 * @method parseName
	 * @param  [type]    $html  [html代码]
	 * @param  [type]    $start [姓名起始位置]
	 * @param  [type]    $end   [姓名结束为止]
	 * @return [string]           [姓名]
	 * @author NewFuture
	 */
	protected static function parseName($html, $start, $end)
	{
		//截取$start右边的
		$html = substr(strstr($html, $start), strlen($start));
		$name = strstr($html, $end, true); // 截取$end左边的
		return trim($name);
	}

	/**
	 * 清空，删除链接和cookie
	 * @method clear
	 * @author NewFuture
	 */
	public static function clear()
	{
		if (self::$_curl)
		{
			curl_close(self::$_curl);
			self::$_curl = null;
		}
		self::$_cookie = null;
	}

	/**
	 * 获取CURL链接
	 * @method _getCURL
	 * @return [type]   [description]
	 * @access private
	 * @author NewFuture
	 */
	private static function _getCURL()
	{
		return (null === self::$_curl) ? (self::$_curl = curl_init()) : self::$_curl;
	}
}