<?php

if (isset($_GET['file'])) {
	highlight_file(urldecode($_GET['file']));
}

?>