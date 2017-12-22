<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\MvcEvent;

class Module {
	const VERSION = '3.0.3-dev';

	public function onBootstrap(MvcEvent $e) {
		$application = $e->getApplication();
		$eventManager = $application->getEventManager();
		$serviceManager = $application->getServiceManager();

		$request = $e->getRequest();
		$eventManager->attach(MvcEvent::EVENT_DISPATCH, function($event) use($serviceManager, $request) {
			if($token = $request->getHeaders('GITHUB-JWT')) {
				$adapter = $serviceManager->get(\Application\Service\OAuth2Adapter::class);
				$adapter->setToken($token->getFieldValue());
				$authService = $serviceManager->get('Zend\Authentication\AuthenticationService');
				$result = $authService->authenticate($adapter);
			}
		}, 100);
	}

	public function getConfig() {
		return include __DIR__ . '/../config/module.config.php';
	}

	public function getControllerConfig() {
		return [
			'factories' => [
				Controller\IndexController::class => function($sm) {
					$locator = $sm->getServiceLocator();
					$authService = $locator->get(\Zend\Authentication\AuthenticationService::class);
					$client = $locator->get(\Application\Service\Github::class);
					$controller = new Controller\IndexController($authService, $client);
					return $controller;
				}
			],
		];
	}

	public function getServiceConfig() {
		return [
			'factories' => [
				\Application\Service\Github::class => function($serviceLocator) {
					$cf = $serviceLocator->get('Config');
					$githubClient = new \Application\Service\Github();
					$githubClient->setOptions(new \Application\Service\ClientOptions($cf['oauth2']['github']));
					return $githubClient;
				},
				\Application\Service\OAuth2Adapter::class => function($serviceLocator) {
					$adapter = new \Application\Service\OAuth2Adapter();
					$client = $serviceLocator->get(\Application\Service\Github::class);
					$adapter->setOAuth2Client($client);
					return $adapter;
				},
				\Zend\Authentication\AuthenticationService::class => function ($serviceLocator) {
					$adapter = $serviceLocator->get(\Application\Service\OAuth2Adapter::class);
					$authService = new \Zend\Authentication\AuthenticationService();
					$authService->setAdapter($adapter);
					$authService->setStorage(new \Zend\Authentication\Storage\NonPersistent());
					return $authService;
				},
			]
		];
	}
}
