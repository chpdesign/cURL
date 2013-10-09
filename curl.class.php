<?php
/**
 *
 * @author Nagy Gergely info@nagygergely.eu
 * @version 0.1
 *
 */
class cURL
{
	private $url = "http://www.google.com";
	private $ch;
	private $proxy;
	private $p_port;
	private $p_type = CURLPROXY_HTTP;
	private $head = "";
	private $body = "";
	private $cookie;
	private $info;
	private $error;
	private $user;
	private $password;
	private $timeout = -1;

	/**
	 * Set the url of the curl.
	 * @param string $url
	 */
	public function __construct($url = null, $port = null)
	{
		//if(filter_var($url, FILTER_VALIDATE_URL) !== FALSE)
			$this->url = $url;
		//if(is_numeric($port))
			$this->port = $port;
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_HTTPHEADER,array());
		curl_setopt($this->ch, CURLOPT_URL, $this->url);
		curl_setopt($this->ch, CURLOPT_PORT, $this->port);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->ch, CURLOPT_HEADER, TRUE);
		curl_setopt($this->ch, CURLOPT_NOBODY, FALSE);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($this->ch, CURLOPT_ENCODING, "");
		//curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($this->ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:17.0) Gecko/20100101 Firefox/17.0");
	}

	public function __destruct()
	{
		$this->close();
	}

	public function setAuth($user=null,$password=null)
	{
		$this->user = $user;
		$this->password = $password;
		curl_setopt($this->ch, CURLOPT_USERPWD, $user . ":" . $password);
	}

	/**
	 * Set the url of the curl.
	 * @param string $url
	 */
	public function setUrl($url = null)
	{
		//if(filter_var($url, FILTER_VALIDATE_URL) !== FALSE)
		{
			$this->url = $url;
			curl_setopt($this->ch, CURLOPT_URL, $this->url);
		}
	}

	/**
	 * Set the port of the curl.
	 * @param int $port
	 */
	public function setPort($port = null)
	{
		//if(is_numeric($port))
		{
			$this->port = $port;
			curl_setopt($this->ch, CURLOPT_PORT, $this->port);
		}
	}

	public function setTimeOut($timeout = 0)
	{
		$this->timeout = $timeout;
		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	}

	/**
	 * Get the actual url.
	 * @return string
	 */
	public function getUrl()
	{
		return curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL);
		return $this->url;
	}

	/**
	 * Get the info of the last curl execution.
	 * @return mixed
	 */
	public function getInfo()
	{
		return $this->info;
	}

	public function followLocation($arg = false)
	{
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, $arg);
	}

	/**
	 * Set the cookie file that we need to do the saving method if cookie is need for job.
	 *
	 * @param string $cookie
	 */
	public function setCookieFile($cookie = null)
	{
		if(file_exists(realpath($cookie)))
		{
			$this->cookie = realpath($cookie);
			curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookie);
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookie);
		}
	}

	/**
	 * Get the cookie file that we need to do the saving method if cookie is need for job.
	 *
	 * @param string $cookie
	 */
	public function getCookieFile()
	{
		return $this->cookie;
	}

	/**
	 * Extract any cookies found from the cookie file. This function expects to get
	 * a string containing the contents of the cookie file which it will then
	 * attempt to extract and return any cookies found within.
	 *
	 * @return array The array of cookies as extracted from the string.
	 *
	 */
	public function getCookie()
	{
		if(file_exists($this->cookie))
		{
			$string = file_get_contents($this->cookie);
		}
		$cookies = array();

		$lines = explode("\n", $string);

		// iterate over lines
		foreach ($lines as $line) {

			// we only care for valid cookie def lines
			if (isset($line[0]) && substr_count($line, "\t") == 6) {

				// get tokens in an array
				$tokens = explode("\t", $line);

				// trim the tokens
				$tokens = array_map('trim', $tokens);

				$cookie = array();

				// Extract the data
				$cookie['domain'] = $tokens[0];
				$cookie['flag'] = $tokens[1];
				$cookie['path'] = $tokens[2];
				$cookie['secure'] = $tokens[3];

				// Convert date to a readable format
				$cookie['expiration'] = date('Y-m-d h:i:s', $tokens[4]);

				$cookie['name'] = $tokens[5];
				$cookie['value'] = $tokens[6];

				// Record the cookie.
				$cookies[] = $cookie;
			}
		}

		return $cookies;
	}

	public function extractCookies($string)
	{
		$cookies = array();

		$lines = explode("\n", $string);

		// iterate over lines
		foreach ($lines as $line) {

			// we only care for valid cookie def lines
			if (isset($line[0]) && substr_count($line, "\t") == 6) {

				// get tokens in an array
				$tokens = explode("\t", $line);

				// trim the tokens
				$tokens = array_map('trim', $tokens);

				$cookie = array();

				// Extract the data
				$cookie['domain'] = $tokens[0];
				$cookie['flag'] = $tokens[1];
				$cookie['path'] = $tokens[2];
				$cookie['secure'] = $tokens[3];

				// Convert date to a readable format
				$cookie['expiration'] = date('Y-m-d h:i:s', $tokens[4]);

				$cookie['name'] = $tokens[5];
				$cookie['value'] = $tokens[6];

				// Record the cookie.
				$cookies[] = $cookie;
			}
		}

		return $cookies;
	}

	/**
	 * Set the proxy parameters.
	 * @param string $proxy Server
	 * @param string $port Server port
	 * @param string $type Type of proxy connection. Either CURLPROXY_HTTP (default) or CURLPROXY_SOCKS5.
	 */
	public function setProxy($proxy = null,$port = null,$type = null)
	{
		$this->proxy = $proxy;
		$this->p_port = $port;
		$this->p_type = $type;
		curl_setopt($this->ch, CURLOPT_PROXY, $this->proxy);
		curl_setopt($this->ch, CURLOPT_PROXYPORT, $this->p_port);
		curl_setopt($this->ch, CURLOPT_PROXYTYPE, $this->p_type);
	}

	/**
	 * Set the user agent
	 * @param string $agent
	 */
	public function setUserAgent($agent = "Mozilla/5.0 (Windows NT 6.1; rv:17.0) Gecko/20100101 Firefox/17.0")
	{
		curl_setopt($this->ch, CURLOPT_USERAGENT, $agent);
	}

	public function setReferer($referer = "")
	{
		curl_setopt($this->ch, CURLOPT_REFERER, $referer );
	}

	/**
	 * Set custom headers
	 * @param array $headers
	 */
	public function setHeaders($headers = array())
	{
		curl_setopt($this->ch, CURLOPT_HTTPHEADER,$headers);
	}

	/**
	 * Set post
	 * @param array $post
	 */
	public function setPost($post = array())
	{
		curl_setopt($this->ch, CURLOPT_POST,TRUE);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,http_build_query($post));
	}

	/**
	 * Set get
	 */
	public function setGet()
	{
		curl_setopt($this->ch, CURLOPT_POST,FALSE);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,NULL);
	}

	/**
	 * Execute the process.
	 */
	public function execute()
	{
		$response = curl_exec($this->ch);
		$this->error = curl_error($this->ch);
		$this->info = curl_getinfo($this->ch);
		$header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
		$this->head = substr($response, 0, $header_size);
		$this->body = substr($response, $header_size);
	}

	/**
	 * Close the curl.
	 */
	public function close()
	{
		curl_close($this->ch);
	}

	/**
	 * Get the last curl error message.
	 * @return string
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * Get the last page's body.
	 * @return string
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * Get the last page's header.
	 * @return string
	 */
	public function getHeader()
	{
		return $this->head;
	}
}
?>
