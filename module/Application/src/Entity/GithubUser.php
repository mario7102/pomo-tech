<?php

namespace Application\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 *
 */
class GithubUser {

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	private $username;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	private $url;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	private $avatar;

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

	public function setAvatar($url){
		$this->avatar = $url;
		return $this;
	}

	public function getAvatar() {
		return $this->avatar;
	}
}