<?php

namespace Application\Service;

use Application\Service\UserService;
use Application\Service\OAuth2Adapter;
use Zend\Authentication\Result;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

class LoadProfileListener implements ListenerAggregateInterface
{
	protected $listeners = array();
	protected $userService;

	public function __construct(UserService $userService){
		$this->userService = $userService;
	}

	/**
	 * Attach one or more listeners
	 *
	 * Implementors may add an optional $priority argument; the EventManager
	 * implementation will pass this to the aggregate.
	 *
	 * @param EventManagerInterface $events
	 *
	 * @return void
	 */
	public function attach(EventManagerInterface $events, $priority = 1){
		$this->listeners[] = $events->getSharedManager()->attach(OAuth2Adapter::class, 'oauth2.success', array($this, 'loadUserByEmail'));
		$this->listeners[] = $events->getSharedManager()->attach(OAuth2Adapter::class, Github::AUTH_SUCCESS_EVENT, array($this, 'loadGithubUser'));
	}

	/**
	 * Detach all previously attached listeners
	 *
	 * @param EventManagerInterface $events
	 *
	 * @return void
	 */
	public function detach(EventManagerInterface $events){
		if ($events->getSharedManager()->detach(OAuth2Adapter::class)) {
			unset($this->listeners[0]);
		}
	}

	public function loadUserByEmail(Event $event){
		$args = $event->getParams();
		$info = $args['info'];
		$user = $this->userService->findUserByEmail($info['email']);
		if (is_null($user)) {
			$args['code'] = Result::FAILURE_IDENTITY_NOT_FOUND;
			$args['info'] = null;
			return;
		}
		$args['info'] = $user;
	}

	public function loadGithubUser(Event $event) {
		$args = $event->getParams();
		$info = $args['info'];
		$user = $this->userService->findUserByGithubUsername($info['login']);
		if (is_null($user)) {
			$user = $this->userService->subscribeUser($info);
			$this->userService->addGithubUser($user, $info);
			$args['info'] = $user;
			return;
		}
		$user->updateInfo(array(
			"github_avatar" => $info['avatar_url'],
			"name" => $info['name'],
			"github_user_url" => $info['html_url']
		));
		$this->userService->saveUser($user);
		$args['info'] = $user;
	}
}