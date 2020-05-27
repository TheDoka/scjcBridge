<?php


function createPDO()
{
    
    try {
        $pdo = new PDO("mysql:host=localhost; dbname=scjcBridge", "root", "root");
    } catch (PDOException $e) {
        die();
    }
        
    return $pdo;

}


    
?>