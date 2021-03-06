<?php

namespace Application\Service;

use Zend\Http\Request;
use Zend\Session\Container;
use Application\Service\ClientOptions;
use Zend\Http\Client as OAuth2HttpClient;

abstract class AbstractOAuth2Client{
	
	/**
	 * @var Container
	 */
	protected $session;
	
	/**
	 * @var ClientOptions
	 */
	protected $options;
	
	protected $error;
	
	/**
	 * @var OAuth2HttpClient
	 */
	protected $httpClient;

	abstract public function getUrlParams();
	abstract public function getToken(Request $request);
	abstract public function getAuthSuccessEvent();
	
	public function __construct(){
		$this->session = new Container('ZendOAuth2_'.get_class($this));
	}

	public function getInfo(){
		if(is_object($this->session->info)) {
			return $this->session->info;
		} elseif(isset($this->session->token->access_token)) {
			$urlProfile = $this->options->getInfoUri() . '?access_token='.$this->session->token->access_token;
			
			$client = $this->getHttpclient()
							->resetParameters(true)
							->setHeaders(array('Accept-encoding' => 'gzip, deflate, identity'))
							->setMethod(Request::METHOD_GET)
							->setUri($urlProfile);
			$response = $client->send();
			$retVal = $response->getBody();

			if(strlen(trim($retVal)) > 0) {
				$this->session->info = \Zend\Json\Decoder::decode($retVal);
				return $this->session->info;
			} else {
				$this->error = array('internal-error' => 'Get info return value is empty.');
				return false;
			}
			
		} else {
			$this->error = array('internal-error' => 'Session access token not found.');
			return false;
		}
	}
	
	public function getScope($glue = ' ') {
		if(is_array($this->options->getScope()) AND count($this->options->getScope()) > 0) {
			$str = urlencode(implode($glue, array_unique($this->options->getScope())));
			return '&scope=' . $str;
		}
		return '&scope=' . $this->options->getScope();
	}
	
	public function getState() {
		return $this->session->state;
	}
	
	protected function generateState() {
		$this->session->state = md5(microtime().'-'.get_class($this));
		return $this->session->state;
	}
	
	public function setOptions(ClientOptions $options) {
		$this->options = $options;
	}
	
	public function getOptions() {
		return $this->options;
	}
	
	public function getError() {
		return $this->error;
	}
	
	public function getSessionToken() {
		return $this->session->token;
	}

	public function getSessionTokenString() {
		return $this->session->token->access_token;
	}
	
	public function getSessionContainer() {
		return $this->session;
	}
	
	public function destroySession() {
		$this->session->getManager()->getStorage()->clear('ZendOAuth2_'.get_class($this));
		return $this;
	}

	public function getProvider() {
		return $this->providerName;
	}
	
	public function setHttpClient($client) {
		if($client instanceof OAuth2HttpClient) {
			$this->httpClient = $client;
		} else {
			throw new Exception\HttpClientException('Passed HTTP client is not supported.');
		}
	}
	
	public function getHttpClient() {
		if(!$this->httpClient) {
			$this->httpClient = new OAuth2HttpClient(null, array('timeout' => 30, 'adapter' => '\Zend\Http\Client\Adapter\Curl'));
		}
		return $this->httpClient;
	}
	
}