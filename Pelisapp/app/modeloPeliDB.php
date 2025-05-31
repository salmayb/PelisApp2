<?php

include_once 'config.php';
include_once 'Pelicula.php';

/*
 * Acceso a datos con BD Peliculas y Patrón Singleton 
 * Un único objeto para la clase
 * VERSION: El contructor crea las sentencias precompiladas
 */
class ModeloPeliDB {

    private  static $modelo = null;
    private  $dbh = null; 
    // Consultas:
    private $stmt_peliculas = null;
    private $stmt_peli = null;
    private $stmt_update = null;
    private $stmt_insert = null;
    private $stmt_delete = null;
   
    public static function getModelo(){
        if (self::$modelo == null){
            self::$modelo = new ModeloPeliDB();
        }
        return self::$modelo;
    }
    
    

   // Constructor privado  Patron singleton
   
    private function __construct(){

        // Establezco la conexión
        try {
            $dsn = "mysql:host=".DB_SERVER.";dbname=".DB_NAME.";charset=utf8";
            $this->dbh = new PDO($dsn,DB_USER,DB_PASSWORD);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            $this->dbh->setAttribute( PDO::ATTR_EMULATE_PREPARES, FALSE );

        } catch (PDOException $e){
            echo "Error de conexión ".$e->getMessage();
            exit();
        }  

        // Creo la sentencias prepardas
         try {
             // Creo las consultas de preparadas
             $this->stmt_peliculas  = $this->dbh->prepare("select * from peliculas");
             $this->stmt_peli       = $this->dbh->prepare("select * from peliculas where codigo_pelicula=:id");
             $this->stmt_update     = $this->dbh->prepare("UPDATE peliculas set  nombre=:nombre, director =:director, ".
                                      "genero=:genero, imagen=:imagen, trailer_id=:trailer_id where codigo_pelicula =:codigo_pelicula");
             $this->stmt_insert = $this->dbh->prepare("INSERT INTO peliculas (codigo_pelicula, nombre, director, genero, imagen, trailer_id) VALUES (:codigo_pelicula, :nombre, :director, :genero, :imagen, :trailer_id)");


         } catch (PDOException $e ){
            echo " Error al crear las sentencias SQL ".$e->getMessage();
         }

    }
     

// Cierro la conexión anulando todos los objectos relacioanado con la conexión PDO (stmt)
public static function closeModelo(){
    if (self::$modelo != null){
        $obj = self::$modelo;
        // Cierro la base de datos
        $obj->dbh = null;
        self::$modelo = null; // Borro el objeto.
    }
}


// Tabla de objetos con todas las peliculas
public  function GetAll ():array{
       
    $tpelis = [];
    $this->stmt_peliculas->setFetchMode(PDO::FETCH_CLASS, 'Pelicula');
    $this->stmt_peliculas->execute();
    while ( $peli = $this->stmt_peliculas->fetch()){
        $tpelis[] = $peli;       
    }
    // tpelis = 
    return $tpelis;
}

public function getById ($id):object {
    $peli = null;
    $this->stmt_peli->setFetchMode(PDO::FETCH_CLASS, 'Pelicula');
    $this->stmt_peli->bindParam(':id', $id);
    if ( $this->stmt_peli->execute() ){
             if ( $obj = $this->stmt_peli->fetch()){
                $peli= $obj;
            }
        }
    return $peli;
}

public function update ($peli) {

    $this->stmt_update->bindValue(':codigo_pelicula',$peli->codigo_pelicula );
    $this->stmt_update->bindValue(':nombre', $peli->nombre );
    $this->stmt_update->bindValue(':genero', $peli->genero );
    $this->stmt_update->bindValue(':director',$peli->director );
    $this->stmt_update->bindValue(':imagen',$peli->imagen );
    $this->stmt_update->bindValue(':trailer_id', $peli->trailer_id );

    if ($this->stmt_update->execute ()){
        return true;
    }
    return false; 
}

public function insert ($peli){
    $this->stmt_insert->bindValue(':codigo_pelicula', $peli->codigo_pelicula);
    $this->stmt_insert->bindValue(':nombre', $peli->nombre);
    $this->stmt_insert->bindValue(':genero', $peli->genero);
    $this->stmt_insert->bindValue(':director', $peli->director);
    $this->stmt_insert->bindValue(':imagen', $peli->imagen);
    $this->stmt_insert->bindValue(':trailer_id', $peli->trailer_id);

    if ($this->stmt_insert->execute()) {
        return true;
    }
    return false;
}

public function deletebyId($id) {
    $stmt = $this->dbh->prepare("DELETE FROM peliculas WHERE codigo_pelicula = :id");
    $stmt->bindValue(':id', $id);
    return $stmt->execute();
}

//Busqueda por titulo, genero o director

public function buscarPorTitulo($valor): array{
    $stmt = $this->dbh->prepare("SELECT * FROM peliculas WHERE nombre LIKE :valor");
    $stmt ->execute([':valor' => "%$valor%"]);
    return $stmt->fetchAll(PDO::FETCH_CLASS, 'Pelicula');
}

public function buscarPorGenero($valor): array{
    $stmt = $this->dbh->prepare("SELECT * FROM peliculas WHERE genero LIKE :valor");
    $stmt ->execute([':valor' => "%$valor%"]);
    return $stmt->fetchAll(PDO::FETCH_CLASS, 'Pelicula');
}

public function buscarPorDirector($valor): array{
    $stmt = $this->dbh->prepare("SELECT * FROM peliculas WHERE director LIKE :valor");
    $stmt ->execute([':valor' => "%$valor%"]);
    return $stmt->fetchAll(PDO::FETCH_CLASS, 'Pelicula');
}

public function insertarVotos($codigo_pelicula, $voto, $usuario_ip){
    $stmt = $this->dbh->prepare("INSERT INTO votos (codigo_pelicula, voto, fecha, usuario_ip) VALUES (:codigo_pelicula, :voto, NOW(), :usuario_ip)");
    $stmt-> bindValue(':codigo_pelicula', $codigo_pelicula);
    $stmt->bindValue(':voto', $voto);
    $stmt->bindValue(':usuario_ip', $usuario_ip);
    return $stmt->execute();
}

public function obtenerMediaVotos($codigo_pelicula){
    $stmt = $this->dbh->prepare("SELECT AVG(voto) AS media, COUNT(*) AS total FROM votos WHERE codigo_pelicula = :codigo_pelicula");
    $stmt -> bindValue(':codigo_pelicula', $codigo_pelicula);
    $stmt -> execute();
    return $stmt -> fetch(PDO::FETCH_ASSOC);
}

} // class
