<?php
    require('inprocess.php');

    $server = 'localhost';
    // cr400906.mysql.tools
    $db = 'gatne-rada';
    // cr400906_gatnerada
    $user = 'root';
    // cr400906_gatnerada
    $password = ''; 
    // K9*jdG9+7r

    $conn = new Connection($server, $db, $user, $password);
    $conn->setConnection();

    $action = $_GET['action'];

    $request = new Request($conn->conn);
    
    if($action == 'select') {

        $sql = '';

        if(isset($_GET['table'])) $sql = 'SELECT * FROM ' . $_GET['table'];

        $res = $request->select($sql, null);
    
        header('Content-Type: application/json');
        echo json_encode($res);
        
    } else if($action == 'session-id-select') {
        $id = $_SESSION['transfer-id'];
        $dir = $_GET['dir'];

        $sql = '';

        if(isset($_GET['table'])) $sql = 'SELECT * FROM ' . $_GET['table'] . ' WHERE id=:id';

        $res = $request->select($sql, [':id' => $id]);

        if(isset($_GET['dir'])) {
            $article_file = '';
            foreach($res as $key) {
                if($key['basename'] ) {
                    $article_file =  file_get_contents($dir . $key['basename']);

                    $response = array($res, $article_file);
                } else {
                    $response = $res;
                }
            }
        } else {
            $response = $res;
        }
    
        header('Content-Type: application/json');
        echo json_encode($response);
    } 
    

?>