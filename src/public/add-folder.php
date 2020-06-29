<?php
    if($_SERVER["REQUEST_METHOD"] != "POST") {
        http_response_code(405);
        die();
    }

    require_once("templates/globals.php");
    require_once(Config::constructFilePath("/Business/FoldersService.php")); 

    $folderName = $_POST['name'];
    $folderService = new FoldersService();
    
    $result = $folderService->addFolder($folderName);
    echo json_encode($result);
    
    header('Location: home.php');
?>