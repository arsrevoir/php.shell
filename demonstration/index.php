<?php 
    require '../main/general.php';

    $conn = new Connection('localhost', null, 'root', '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>php.shell demonstration</title>
</head>
<body>
    <div class="page">
        <main role="main">
            <form method='post' enctype="multipart/form-data">
                <input type="file" name='file[]' multiple>
                <input type="submit" name='submit'>
            </form>
        </main>
    </div>
</body>
</html>

<?php 
    if(isset($_POST['submit'])) {
        $ext = array(
            'png' => 'image/png'            
        );

        $file = new File($_FILES['file'], 'images/', $ext);

        echo var_dump($file->files_arr);
    }
?>