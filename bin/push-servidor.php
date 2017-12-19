<?php
header('Content-Type: text/html; charset=utf-8');
require dirname(__DIR__) . '/vendor/autoload.php';
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServerInterface; //Interfaz para el envio de push
    

    class Pusher implements WampServerInterface {
    	/**
	     * A lookup of all the topics clients have subscribed to
	     */
	    protected $subscribedTopics = array();

	    public function onSubscribe(ConnectionInterface $conn, $topic) {
                echo "a user has suscribed";
	        $this->subscribedTopics[$topic->getId()] = $topic;
	    }

	    /**
	     * @param string JSON'ified string we'll receive from ZeroMQ
	     */
	    public function onBlogEntry($entry) {
	        $entryData = json_decode($entry, true);
	        // If the lookup topic object isn't set there is no one to publish to
	        if (!array_key_exists($entryData['category'], $this->subscribedTopics)) {
	            return;
	        }
	        $topic = $this->subscribedTopics[$entryData['category']];
	        // re-send the data to all the clients subscribed to that category
	        $topic->broadcast($entryData);
	    }

	    public function onUnSubscribe(ConnectionInterface $conn, $topic) {
	    	echo "User has unsuscribed";
	    }
	    public function onOpen(ConnectionInterface $conn) {
	    	echo "Connected";
	    }
	    public function onClose(ConnectionInterface $conn) {
	    	echo "Disconnected: conn";
	    }
	    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
	    	echo "Someone has called on the application";
	        // In this application if clients send data it's because the user hacked around in console
	        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
	    }
	    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
	        // In this application if clients send data it's because the user hacked around in console
	        echo "Someone has published something";
	        $conn->close();
	    }
	    public function onError(ConnectionInterface $conn, \Exception $e) {
	    	echo $e;
	    }
	}

    $loop   = React\EventLoop\Factory::create();
    $pusher = new Pusher();

    // Listen for the web server to make a ZeroMQ push after an ajax request
    $context = new React\ZMQ\Context($loop);
    $pull = $context->getSocket(ZMQ::SOCKET_PULL);
    $pull->bind('tcp://127.0.0.1:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
    $pull->on('message', array($pusher, 'onBlogEntry'));

    // Set up our WebSocket server for clients wanting real-time updates
    $webSock = new React\Socket\Server('0.0.0.0:9016', $loop); // Binding to 0.0.0.0 means remotes can connect
    $webServer = new Ratchet\Server\IoServer(
        new Ratchet\Http\HttpServer(
            new Ratchet\WebSocket\WsServer(
                new Ratchet\Wamp\WampServer(
                    $pusher
                )
            )
        ),
        $webSock
    );

    $loop->run();
