<?php

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 *
 */
class User
{

	const ROLE_USER = "user";
	const ROLE_ADMIN = "admin";

	/**
	 * @ORM\Id @ORM\Column(type="string")
	 * @var string
	 */
	protected $id;
	/**
	 * @ORM\Column(type="string", length=200, nullable=TRUE)
	 * @var string
	 */
	protected $name;

	/**
	 * @ORM\Column(type="string", length=200, nullable=TRUE)
	 * @var string
	 */
	protected $email;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	protected $createdAt;
	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="createdBy_id", referencedColumnName="id", nullable=TRUE)
	 * @var User
	 */
	protected $createdBy;
	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	protected $mostRecentEditAt;
	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="mostRecentEditBy_id", referencedColumnName="id", nullable=TRUE)
	 * @var User
	 */
	protected $mostRecentEditBy;

	/**
	 * @ORM\Embedded(class="Application\Entity\GithubUser")
	 * @var GithubUser
	 */
	protected $githubUser;

	/**
	 * @ORM\Column(type="string")
	 * @var string 
	 */
	private $role = self::ROLE_USER;
	
	public static function create(User $createdBy = null) {
		$rv = new self();
		$rv->id = Uuid::uuid4()->toString();
		$rv->createdAt = new \DateTime();
		$rv->createdBy = $createdBy;
		$rv->mostRecentEditAt = $rv->createdAt;
		$rv->mostRecentEditBy = $rv->createdBy;
		return $rv;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}
	/**
	 * @param string $name
	 * @return $this
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}
	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}

	/**
	 * @param \DateTime $when
	 * @return $this
	 */
	public function setCreatedAt(\DateTime $when) {
		$this->createdAt = $when;
		return $this;
	}

	/**
	 * @return User
	 */
	public function getCreatedBy() {
		return $this->createdBy;
	}

	/**
	 * @param User $user
	 * @return $this
	 */
	public function setCreatedBy(User $user) {
		$this->createdBy = $user;
		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getMostRecentEditAt() {
		return $this->mostRecentEditAt;
	}

	/**
	 * @param \DateTime $when
	 * @return $this
	 */
	public function setMostRecentEditAt(\DateTime $when) {
		$this->mostRecentEditAt = $when;
		return $this;
	}

	/**
	 * @return User
	 */
	public function getMostRecentEditBy() {
		return $this->mostRecentEditBy;
	}

	/**
	 * @param User $user
	 * @return $this
	 */
	public function setMostRecentEditBy(User $user) {
		$this->mostRecentEditBy = $user;
		return $this;
	}

	/**
	 * @param User|null $object
	 * @return bool
	 */
	public function equals(User $object = null) {
		if(is_null($object)) {
			return false;
		}
		return $this->id == $object->getId();
	}

	/**
	 * @param $status
	 * @return $this
	 */
	public function setStatus($status) {
		$this->status = $status;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	* @param GithubUser
	**/
	public function setGithubUser(GithubUser $github_user){
		$this->githubUser = $github_user;
		return $this;
	}

	public function getGithubUser(){
		return $this->githubUser;
	}

	public function setEmail($email){
		$this->email = $email;
		return $this;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setRole($role) {
		$this->role = $role;
		return $this;
	}

	public function getRole() {
		return $this->role;
	}

	public function updateInfo($info){
		if(isset($info['github_avatar'])){
			$this->githubUser->setAvatar($info['github_avatar']);
		}
		if(isset($info['name'])){
			$this->name = $info['name'];
		}
		if(isset($info['github_user_url'])){
			$this->githubUser->setUrl($info['github_user_url']);
		}
		if(isset($info['email'])){
			$this->email = $info['email'];
		}
		return $this;
	}

	public function getAvatarUrl(){
		if(is_null($this->githubUser)){
			return "";
		}
		return $this->githubUser->getAvatar();
	}
}