<?php

namespace Application\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 *
 */
class GithubUser {

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $username;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	private $url;


	public function __construct($username){
		$this->username = $username;
	}

	public function getUsername(){
		return $this->username;
	}

	public function setUrl($url){
		$this->url = $url;
		return $this;
	}

	public function getUrl(){
		return $this->url;
	}
}