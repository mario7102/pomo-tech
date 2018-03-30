<?php 
namespace Application\Service;

use Doctrine\ORM\EntityManager;
use Application\Entity\GithubUser;
use Application\Entity\User;

class UserService
{

	protected $entityManager;
	
	public function __construct(EntityManager $entityManager) {
		$this->entityManager = $entityManager;
	}
	/**
	 * User Subscribe to the system the first time user log in with supported SSO
	 *
	 * @param array [email, family_name, given_name, picture]
	 * @return User
	 */
	public function subscribeUser($userInfo){
		$user = $this->create($userInfo, User::ROLE_USER);
		return $user;
	}

	public function create($userInfo, $role, User $createdBy = null)
	{
		$user = User::create($createdBy);
		$user->setName($userInfo['name'])
			->setEmail($userInfo['email'])
			->setRole($role);
		$this->saveUser($user);
		return $user;
	}

	public function addGithubUser(User &$user, $info){
		$githubUser = new GithubUser($info['login']);
		$githubUser->setAvatar($info['avatar_url'])
			->setUrl($info['html_url']);
		$user->setGithubUser($githubUser);
		$this->saveUser($user);
		return $user;
	}

	public function saveUser($user){
		$this->entityManager->persist($user);
		$this->entityManager->flush($user);
		return $user;
	}

	/**
	 * Find a User by id
	 *
	 * @param mixed $id
	 * @return User
	 */
	public function findUser($id) {

	}
	
	/**
	 * Find a User by Email
	 *
	 * @param string $email
	 * @return User
	 */
	public function findUserByEmail($email) {

	}

	/**
	 * Find users by $filters
	 * @param array
	 * @return User[]
	 */
	public function findUsers($filters) {

	}

	public function findUserByGithubUsername($username) {
		return $this->entityManager
			->getRepository(User::class)
			->findOneBy(array("githubUser.username" => $username));
	}
}