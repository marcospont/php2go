<?php

class ValidatorUpload extends Validator
{
	protected $rules = array();
	
	public function validate($value) {
		if ($value instanceof UploadFile || $value instanceof UploadFileCollection)
			$key = $value->getUploadKey();
		else
			$key = $value;
		$manager = new UploadManager();
		$manager->addRules(array(
			$key => $this->rules
		));
		if (!$manager->validate($key)) {
			$error = array();
			if (($global = $manager->getGlobalErrors()) !== null)
				$error = array_merge($error, $global);
			if (($errors = $manager->getErrors($key)) !== null)
				$error = array_merge($error, $errors);
			$this->setError(implode(' ', $error));
			return false;
		}
		return true;
	}
	
	protected function validateModelAttribute(Model $model, $attr) {
		$value = $model->{$attr};
		if ($value instanceof UploadFile || $value instanceof UploadFileCollection) {
			$key = $value->getUploadKey();
			$manager = new UploadManager();
			$manager->addRules(array(
				$key => $this->rules
			));
			if (!$manager->validate($key)) {
				$global = $manager->getGlobalErrors();
				if ($global)
					$model->addGlobalErrors($global);
				$errors = $manager->getErrors($key);
				if ($errors)
					$model->addErrors(array($attr => $errors));
			}
		}
	}
}