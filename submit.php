<?php

    header('Content-Type: text/html; charset=utf-8');
    require dirname(__DIR__) . '/vendor/autoload.php';
    use Ratchet\MessageComponentInterface;
    use Ratchet\ConnectionInterface;
    use Ratchet\Server\IoServer;
    use Ratchet\Http\HttpServer;
    use Ratchet\WebSocket\WsServer;
    use Ratchet\Wamp\WampServerInterface; //Interfaz para el envio de push
    // post.php ???
    // This all was here before  ;)
    $entryData = array(
        'category' => $_POST['category']
      , 'title'    => $_POST['title']
      , 'article'  => $_POST['article']
      , 'when'     => time()
    );

    //$pdo->prepare("INSERT INTO blogs (title, article, category, published) VALUES (?, ?, ?, ?)")
        //->execute($entryData['title'], $entryData['article'], $entryData['category'], $entryData['when']);

    // This is our new stuff
    $context = new ZMQContext();
    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
    $socket->connect("tcp://localhost:5555");
    $socket->send(json_encode($entryData));
    

    echo "Los datos han sido enviados al servidor, ahora tus suscriptores van a recibirlo";
    echo json_encode($entryData);
