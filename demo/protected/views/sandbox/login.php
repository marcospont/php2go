<? if ($this->message) : ?>
<p><?=$this->message?></p>
<? endif; ?>

<?=$this->formBegin()?>

	<?=$this->formLabel('username', 'Login:')?><br/>
	<?=$this->formText('username', $this->app->request->getPost('username'), array('maxlength' => 15))?><br/>
	<?=$this->formLabel('password', 'Password:')?><br/>
	<?=$this->formPassword('password', null, array('maxlength' => 15))?><br/>
	<?=$this->formCheckbox('remember')?>&nbsp;<?=$this->formLabel('remember', 'Remember me')?><br/>
	<?=$this->formSubmit('Login')?>

<?=$this->formEnd()?>