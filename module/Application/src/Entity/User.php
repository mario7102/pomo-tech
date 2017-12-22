<?php

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Rhumsaa\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 *
 */
class User
{
	/**
	 * @ORM\Id @ORM\Column(type="string")
	 * @var string
	 */
	protected $id;
	/**
	 * @ORM\Column(type="string", length=100, nullable=TRUE)
	 * @var string
	 */
	protected $firstname;
	/**
	 * @ORM\Column(type="string", length=100, nullable=TRUE)
	 * @var string
	 */
	protected $lastname;

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
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $status;
	
	public static function create(User $createdBy = null) {
		$rv = new self();
		$rv->id = Uuid::uuid4()->toString();
		$rv->status = self::STATUS_ACTIVE;
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
	 * @param string $firstname
	 * @return $this
	 */
	public function setFirstname($firstname) {
		$this->firstname = $firstname;
		return $this;
	}
	/**
	 * @return string
	 */
	public function getFirstname() {
		return $this->firstname;
	}
	/**
	 * @param string $lastname
	 * @return $this
	 */
	public function setLastname($lastname) {
		$this->lastname = $lastname;
		return $this;
	}
	/**
	 * @return string
	 */
	public function getLastname() {
		return $this->lastname;
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
}