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

/**
 * Connection handler
 */
function onConnect($client)
{
	$pid = pcntl_fork();

	if ($pid == -1) {
		 die('could not fork');
	} else if ($pid) {
		// parent process
		return;
	}

	$read = '';
	printf("[%s] Connected at port %d\n", $client->getAddress(), $client->getPort());

	while( true ) {
		$read = $client->read();
		if ( $read != '' ) {
			$client->send('[' . date( DATE_RFC822 ) . '] ' . $read );
		} else {
			break;
		}

		if (preg_replace('/[^a-z]/', '', $read) == 'exit') {
			break;
		}
		if ($read === null) {
			printf("[%s] Disconnected\n", $client->getAddress());
			return false;
		} else {
			printf("[%s] recieved: %s", $client->getAddress(), $read);
		}
	}
	$client->close();
	printf("[%s] Disconnected\n", $client->getAddress());

}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'autoload.php';

$server = new shgysk8zer0\Sockets\SocketServer();
$server->init();
$server->setConnectionHandler('onConnect');
$server->listen();
