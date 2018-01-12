<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Authentication\AuthenticationService;

class IndexController extends AbstractActionController {

	/**
	 * @param AdapterInterface $authService
	 * @param string $privateKey
	 */
	public function __construct (AuthenticationService $authService, $client) {
		$this->authService = $authService;
		$this->client = $client;
	}

	public function indexAction() {
		$view = new ViewModel();
		$view->setVariable("token", $this->client->getSessionTokenString());
		return $view;
	}

	public function getstateAction() {
		$json = new JsonModel();
		$json->setVariable("params", $this->client->getUrlParams());
		return $json;
	}

	public function loginAction() {
		$request = $this->getRequest();
		$view = new ViewModel();
		try {
			$this->client->getToken($request);
			$result = $this->authService->authenticate();
			if ($result->isValid()) {
				$view->setVariable('token', $this->client->getSessionToken());
			} else {
				$view->setVariable('error', $result->getMessages());
			}
		} catch (Exception $e) {
			return $view->setVariable('error', $e->getMessage());
		}
		$this->redirect()->toRoute("home");
	}

	public function logoutAction(){
		$this->authService->clearIdentity();
		$this->client->destroySession();
		$json = new JsonModel();
		$json->setVariable("loggedout", "success");
		return $json;
	}

	public function getAuthService() {
		return $this->authService;
	}
}
