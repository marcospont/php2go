<?php

class SandboxController extends Controller
{
	public function filters() {
		return array(
			'authorization',
			'postOnly + upload'
		);
	}

	public function authorizationRules() {
		return array(
			array('allow', '@'),
			array('allow', '*', 'actions' => 'login'),
			array('deny', '*')
		);
	}

	public function viewHelpers() {
		return array(
			'menu' => array(
				'container' => new Navigation(Config::fromFile('protected/config/navigation.php'))
			)
		);
	}

	public function actionLogin() {
		$data = array();
		$authenticator = $this->app->getAuthenticator();
		if ($authenticator->valid)
			$this->redirect();
		try {
			$authenticator->authenticate();
		} catch (AuthenticatorException $e) {
			$data['message'] = $e->getMessage();
		}
		$this->render('login', $data);
	}

	public function actionLogout() {
		$this->app->getAuthenticator()->logout();
	}

	public function actionIndex() {
		$this->render('index', array(
			'clients' => DAO::instance()->findPairs('client', 'name', array('order' => 'name'))
		));
	}

	public function actionHelpers() {
		$countries = Country::model()->findAll(array('limit' => 10, 'order' => 'name'));
		$this->render('helpers', array(
			'countries' => $countries,
			'navigation' => new Navigation(Config::fromFile('protected/config/navigation.php'))
		));
	}

	public function actionWidgets1() {
		$this->view->head()->addLibrary('ajaxfilemanager');
		$this->render('widgets1');
	}

	public function actionWidgets2() {
		$this->render('widgets2');
	}

	public function actionGetClients() {
		$request = $this->getRequest();
		if (($q = $request->getQuery('q'))) {
			$q = "%{$q}%";
			$result = array();
			$clients = DAO::instance()->findAll('client', array(
				'condition' => 'name like ?',
				'limit' => $request->getQuery('limit'),
				'order' => 'name'
			), array($q));
			foreach ($clients as $client)
				$result[] = $client['name'];
			echo implode("\n", $result);
		}
	}

	public function actionTabContent() {
		$this->renderText('Tab Contents!');
	}

	public function actionPerson() {
		$person = Person::model()->find(1);
		$request = $this->getRequest();
		// toggle active
		if ($toggle = $request->getPost('toggle')) {
			if ($person->saveAttributes(array('active' => ($person->active == 1 ? 0 : 1)))) {
				Flash::set('success', 'Registro salvo com sucesso!');
				$this->redirect('person');
			}
		}
		// save
		if (($data = $request->getPost('person'))) {
			$person->import($data);
			if ($person->save()) {
				Flash::set('success', 'Registro salvo com sucesso!');
				$this->redirect('person');
			}
		}
		$this->render('person', array(
			'person' => $person,
			'sex' => array(
				'M' => 'M',
				'F' => 'F'
			),
			'countries' => Country::model()->findPairs('name', array('order' => 'name'))
		));
	}

	public function actionUpload() {
		$mgr = new UploadManager();
		$mgr->addRules(array(
			'Filedata' => array(
				'mimeType' => array('allow' => 'image/jpg,image/jpeg'),
				'imageSize' => array('maxWidth' => 1024, 'maxHeight' => 768)
			)
		));
		if ($mgr->validate('Filedata')) {
			try {
				$mgr->getFile('Filedata')->saveTo($this->app->getRootPath() . DS . 'upload');
				echo '1';
			} catch (Exception $e) {
				echo sprintf('Erro ao salvar o arquivo "%s".', $mgr->getFile('Filedata')->getName());
			}
		} else {
			$errors = $mgr->getErrors('Filedata');
			echo $errors[0];
		}
	}

	public function actionAjax() {
		echo date('r');
	}

	public function actionForward() {
		$this->forward('index');
	}

	public function actionRedirect() {
		$this->redirect();
	}

	public function actionJson() {
		$this->renderJson(array(1, 2, 3));
	}

	public function actionXml() {
		$this->renderXml('xml');
	}
}