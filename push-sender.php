<?php

    require dirname(__DIR__) . '/chat/vendor/autoload.php';
    use Ratchet\MessageComponentInterface;
    use Ratchet\ConnectionInterface;
    use Ratchet\Server\IoServer;
    use Ratchet\Http\HttpServer;
    use Ratchet\WebSocket\WsServer;
    use Ratchet\Wamp\WampServerInterface;
  //  use \ZMQContext;
//    use \ZMQ;
    //use React\ZMQ //Interfaz para el envio de push
    // post.php ???
    // This all was here before  ;)
   // if(isset($_POST["category"])){
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
   // }else{
?>
<!DOCTYPE html>
<html>
<head>
	<title>Server pusher</title>
</head>
<body>
  <script type="text/javascript">
  /*
  function enviar() {
    var title = document.getElementById('title').value;
    var category document.getElementById('category').value;
    var article = document.getElementById('article').value;
    var response = {
      tittle: title,
      category: category,
      article: article
    }
    document.getElementById('listMensajes').innerHTML += '<br> dato agregado: ' + JSON.stringify(response);
    document.getElementById('category').value = "";
    document.getElementById('title').value = "";
    document.getElementById('article').value = "";
  }
		*/

		  
  </script>
  <br>
  <strong>Welcome Push Publisher</strong><br>
  <form method="post" action="">
    <input type="text" name="category" value="kittensCategory" placeholder="category" id="category"><br>
    <input type="text" name="title" value="Where do the cats play?" placeholder="title" id="title"><br>
    <input type="text" name="article" value="the cats play on their sand box. The end" placeholder="article" id="category"><br> 
    <input type="submit" name="" value="enviar" />
  </form>
  <br>
  <div id="listMensajes"></div>
</body>
</html>
