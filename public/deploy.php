<?php
// Use in the "Post-Receive URLs" section of your GitHub repo.
if ($_SERVER['HTTP_X_GITHUB_EVENT'] == 'push') {
	echo shell_exec('cd C:/web/www/sept && git reset --hard HEAD && git pull');
}
?>