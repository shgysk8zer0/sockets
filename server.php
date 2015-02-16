<?php

/**
 * Check dependencies
 */
if (! extension_loaded('sockets')) {
	echo "This example requires sockets extension (http://www.php.net/manual/en/sockets.installation.php)\n";
	exit(-1);
}

if (! extension_loaded('pcntl')) {
	echo "This example requires PCNTL extension (http://www.php.net/manual/en/pcntl.installation.php)\n";
	exit(-1);
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'autoload.php';

$server = new shgysk8zer0\Sockets\SocketServer();
$server->init();
$server->setConnectionHandler(
	function(\shgysk8zer0\Sockets\SocketClient $client)
	{
		$pid = pcntl_fork();

		if ($pid == -1) {
			die('could not fork');
		} elseif ($pid) {
			// parent process
			return;
		}

		$read = '';
		printf("[%s] Connected at port %d\n", $client->getAddress(), $client->getPort());

		while(true) {
			$read = $client->read();
			$read = trim($read);
			if (!empty($read)) {
				$client->send('[' . date(DATE_RFC822) . '] ' . $read . PHP_EOL);
			} else {
				break;
			}

			if (is_string($read) and strtolower($read) === 'exit') {
				break;
			} elseif (is_null($read)) {
				printf("[%s] Disconnected" . PHP_EOL, $client->getAddress());
				return false;
			} else {
				printf("[%s] recieved: %s" . PHP_EOL, $client->getAddress(), $read);
			}
		}
		$client->close();
		printf("[%s] Disconnected\n", $client->getAddress());
	}
);
$server->listen();
