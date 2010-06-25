<?php

include 'bootstrap.php';

class MyModel extends FormModel
{
	public $files;
	public $picture;

	public function rules() {
		return array(
			array('files,picture', 'required'),
			array('files', 'upload', 'rules' => array(
				'count' => array('min' => 1, 'max' => 2),
				'size' => array('max' => '10k')
			)),
			array('picture', 'upload', 'rules' => array(
				'mimeType' => array('allow' => 'image/gif'),
				'imageSize' => array('width' => 200, 'height' => 200)
			))
		);
	}
}

$mgr = new UploadManager();
$model = new MyModel();
$model->files = $mgr->getFile('files');
$model->picture = $mgr->getFile('picture');
if (!$model->validate()) {
	echo '<pre>';
	var_dump($model->getErrors());
	echo '</pre>';
}

?>
<html>
<body>
<form name="form" method="post" enctype="multipart/form-data" action="test-upload.php">
<input name="files[]" type="file"/><br/>
<input name="files[]" type="file"/><br/>
<input name="files[]" type="file"/><br/>
<input name="files[]" type="file"/><br/>
<input name="files[]" type="file"/><br/>
<input name="picture" type="file"/><br/>
<input type="submit" value="Enviar"/>
</form>
</body>
</html>
