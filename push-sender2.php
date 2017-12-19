<?php

    require dirname(__DIR__) . '/chat/vendor/autoload.php';
    use Ratchet\MessageComponentInterface;
    use Ratchet\ConnectionInterface;
    use Ratchet\Server\IoServer;
    use Ratchet\Http\HttpServer;
    use Ratchet\WebSocket\WsServer;
    use Ratchet\Wamp\WampServerInterface;
    use \ZMQContext;
    use \ZMQ;
    //use React\ZMQ //Interfaz para el envio de push
    // post.php ???
    // This all was here before  ;)
    //if(isset($_POST["category"])){
     $entryData = array(
        'category' => "Categoria"
      , 'title'    => "Titulo"
      , 'article'  => "Articulo"
      , 'when'     => time()
    );
    
    /*
    $entryData = array(
        'category' => $_POST['category']
      , 'title'    => $_POST['title']
      , 'article'  => $_POST['article']
      , 'when'     => time()
    );
    */

    //$pdo->prepare("INSERT INTO blogs (title, article, category, published) VALUES (?, ?, ?, ?)")
        //->execute($entryData['title'], $entryData['article'], $entryData['category'], $entryData['when']);

    // This is our new stuff
    $context = new \ZMQContext();
    //$context = new React\ZMQ\Context();
    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
    $socket->connect("tcp://localhost:5555");
    $socket->send(json_encode($entryData));
    

    echo "Los datos han sido enviados al servidor, ahora tus suscriptores van a recibirlo";
    echo json_encode($entryData);
    /*
    }else{
?>
<!DOCTYPE html>
<html>
<head>
	<title>Server pusher</title>
</head>
<body>
  <script type="text/javascript">
  </script>
  <strong>Welcome Push Publisher</strong><br>
  <form method="post" action="">
    <input type="text" name="category" value="" placeholder="category" id="category"><br>
    <input type="text" name="title" value="" placeholder="title" id="title"><br>
    <input type="text" name="article" value="" placeholder="article" id="category"><br> 
    <input type="submit" name="">Enviar</input>
  </form>
  <br>
  <div id="listMensajes"></div>
</body>
</html>
<?php
    }
?>*/
