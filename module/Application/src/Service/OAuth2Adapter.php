<?php

namespace Application\Service;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\Authentication\Result;
use Zend\Authentication\Adapter\AdapterInterface;
use Application\Service\AbstractOAuth2Client;

class OAuth2Adapter implements AdapterInterface, EventManagerAwareInterface {
	
	protected $client;
	protected $eventManager;
	protected $token;

	public function setOAuth2Client($client) {
		if($client instanceof AbstractOAuth2Client) {
			$this->client = $client;
		}
	}
	
	//aggiunto per l'autenticazione ad ogni chiamata delle API
	public function setToken($token) {
		die($token);
		$this->token = $token;
	}

	public function authenticate(){
		//aggiunto per l'autenticazione ad ogni chiamata delle API
		if(!is_null($this->token) && $this->token !== $this->client->getSessionToken()) {
			return new Result(Result::FAILURE_CREDENTIAL_INVALID, "Token mismatch");
		}
		if(is_object($this->client) AND is_object($this->client->getInfo())) { 
			
			$args['code'] = Result::SUCCESS;
			$args['info'] = (array)$this->client->getInfo();
			$args['provider'] = $this->client->getProvider();
			$args['token'] = (array)$this->client->getSessionToken();
			
			$args = $this->getEventManager()->prepareArgs($args);

			$this->getEventManager()->trigger('oauth2.success', $this, $args);
			return new Result($args['code'], $args['info']);
		} else {
			return new Result(Result::FAILURE, $this->client->getError());
		}
		
	}
	
	public function setEventManager(EventManagerInterface $eventManager){
		$eventManager->setIdentifiers([__CLASS__, get_called_class()]);
		$this->eventManager = $eventManager;
		return $this;
	}
	
	public function getEventManager(){
		if (null === $this->eventManager) {
			$this->setEventManager(new EventManager());
		}
		return $this->eventManager;
	}

	public function getClient() {
		return $this->client;
	}
	
}