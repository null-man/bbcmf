<?php

if(!empty($_GET['update'])) {

	$output = [];
	exec('sh /home/www/bb.sh', $output);
	foreach ($output as $o) {
		echo $o.'<br/>';
	}

	echo 'update ok';

} else {

	phpinfo();

}


?>
