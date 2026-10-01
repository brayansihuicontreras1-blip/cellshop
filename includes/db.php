<?php
// Configuración de la conexión (valores por defecto de XAMPP)
const DB_HOST='localhost';
const DB_NAME='cellshop';
const DB_USER='root';
const DB_PASS='';

function db(): PDO {
  static $pdo=null;
  if($pdo===null){
    try{
      $pdo=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES=>false,
      ]);
    }catch(PDOException $e){
      http_response_code(500);
      exit('No se pudo conectar a la base de datos. Inicia MySQL en XAMPP, importa sql/cellshop.sql y revisa includes/db.php');
    }
  }
  return $pdo;
}
