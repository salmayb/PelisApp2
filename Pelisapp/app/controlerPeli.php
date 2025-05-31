<?php
// ------------------------------------------------
// Controlador que realiza la gestión de usuarios
// ------------------------------------------------

include_once 'config.php';
include_once 'modeloPeliDB.php'; 

/**********
/*
 * Inicio Muestra o procesa el formulario (POST)
 */

function  ctlPeliInicio(){
    include 'plantillas/todo.php';
   }

/*
 *  Muestra y procesa el formulario de alta 
 */

function ctlPeliAlta (){
    if (!isset($_SESSION['user'])) {
    $_SESSION['msg'] = "Debes iniciar sesión para realizar esta acción.";
    ctlPeliVerPelis();
    return;
}


    if ($_SERVER['REQUEST_METHOD'] == 'GET'){
        $msg = $_SESSION['msg'] ?? '';
        include_once 'plantillas/fnueva.php';
        //Procesar POST----
    } else{
        include_once 'app/Pelicula.php';
        $peli = new Pelicula;
        $peli -> nombre = htmlspecialchars($_POST['nombre'], ENT_QUOTES, 'UTF-8');
        $peli -> director = htmlspecialchars($_POST['director'], ENT_QUOTES, 'UTF-8');
        $peli -> genero = htmlspecialchars($_POST['genero'], ENT_QUOTES, 'UTF-8');
        $peli -> imagen = null;
        $peli -> trailer_id = htmlspecialchars($_POST['trailer_id'], ENT_QUOTES, 'UTF-8');


        if ( !empty ($_FILES['imagen']['name'])) {
            if(procesarImagen()) {
                $peli->imagen = $_FILES['imagen']['name'];
            }else{
                $msg = $_SESSION['msg'] ?? 'Error al procesar imagen';
                include_once 'plantillas/fnueva.php';
                return;
            }
        }

        $db = ModeloPeliDB::getModelo();
        $peli = $db->insert($peli);
        $_SESSION['msg'] = 'Pelicula añadida correctamente';
        ctlPeliVerPelis();

    }
}
/*
 *  Muestra y procesa el formulario de Modificación 
 */
function ctlPeliModificar (){
    if (!isset($_SESSION['user'])) {
    $_SESSION['msg'] = "Debes iniciar sesión para realizar esta acción.";
    ctlPeliVerPelis();
    return;
}

    
    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        if(isset($_GET['id'])) {
            $db = ModeloPeliDB::getModelo();
            $peli = $db->getById($_GET['id']);
            $msg = $_SESSION['msg'] ?? '';
            include_once 'plantillas/fmodificar.php';
        }
    }else{
        $peli = new Pelicula();
        $peli->codigo_pelicula = $_POST['codigo_pelicula'];
        $peli->nombre = htmlspecialchars($_POST['nombre'], ENT_QUOTES, 'UTF-8');
        $peli->director = htmlspecialchars($_POST['director'], ENT_QUOTES, 'UTF-8');
        $peli->genero = htmlspecialchars($_POST['genero'], ENT_QUOTES, 'UTF-8');
        $peli->trailer_id = htmlspecialchars($_POST['trailer_id'], ENT_QUOTES, 'UTF-8');

        if (!empty($_FILES['imagen']['name'])) {
            if(procesarImagen()){
                $peli->imagen = $_FILES['imagen']['name'];
            } else{
                $db = ModeloPeliDB::getModelo();
                $peli = $db->getById($_POST['codigo_pelicula']);
                $msg = $_SESSION['msg'] ?? 'Error al procesar imagen';
                include_once 'plantillas/fmodificar.php';
                return;
            }
        } else{
            $peli->imagen = $_POST['imagenold'] ?? null;
        }

        $db = ModeloPeliDB::getModelo();
        $peli = $db->Update($peli);
        $_SESSION['msg'] = "Pelicula actualizada correctamente";
        ctlPeliVerPelis();
    }
}
    


function procesarImagen(){
    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK ){
        return false;
    }

    $nombre = $_FILES['imagen']['name'];
    $nombreTmp = $_FILES['imagen']['tmp_name'];
    $tamano = $_FILES['imagen']['size'];

    //validamos tamaño
    $maxSize = 2 * 1024 * 1024; //2MB
    if($tamano > $maxSize){
        $_SESSION['msg'] = "ERROR: El archivo supera el tamaño máximo permitido.";
        return false;
    }

    //validar que es una imagen real
    $infoImagen = getimagesize($nombreTmp);
    if ($infoImagen === false){
        $_SESSION['msg'] = "ERROR: El archivo no es una imagen válida.";
        return false;
    }

    //validar la extension
    $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
    $extPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
    if(!in_array($ext,$extPermitidas)){
        $_SESSION['msg'] = "ERROR: Solo se permiten archivos jpg, jpeg, png y gif.";
        return false;
    }

    //Limpiamos nombre
    $nombreLimpio = limpiarNombreArchivo($nombre);


    //Movemos la imagen a la carpeta img
    if(move_uploaded_file($nombreTmp, 'app/img/'.$nombreLimpio)){
        $_FILES['imagen']['name'] = $nombreLimpio;
        return true;
    } else {
        $_SESSION['msg'] = "ERROR: No se pudo mover el archivo.";
        return false;
    }
}



/*
 *  Muestra detalles de la pelicula
 */

function ctlPeliDetalles($id = null){
    if($id === null) $id = $_GET['id'] ?? null;
    if ($id === null) {
        $_SESSION['msg'] = "No se indicó la película";
        ctlPeliVerPelis();
        return;
    }

    $db = ModeloPeliDB::getModelo();
    $peli = $db->getById($id);
    if(!$peli){
        $_SESSION['msg'] = "Película no encontrada";
        ctlPeliVerPelis();
        return;
    }

    $votos = $db->obtenerMediaVotos($id);
    include_once 'plantillas/detalle.php';
}

/*
 * Borrar Peliculas
 */

 function ctlPeliBorrar(){
    if (!isset($_SESSION['user'])) {
    $_SESSION['msg'] = "Debes iniciar sesión para realizar esta acción.";
    ctlPeliVerPelis();
    return;
}


    if (isset($_GET['id'])) {
        $db = ModeloPeliDB::getModelo();
        $db->deletebyId($_GET['id']);
        $_SESSION['msg'] = "Película eliminada";
    }
    ctlPeliVerPelis();
}


/*
 * Cierra la sesión y vuelca los datos
 */
function ctlPeliCerrar(){
    session_destroy();
    ModeloPeliDB::closeModelo();
    header('Location:index.php');
}

/*
 * Muestro la tabla con los usuario 
 */ 
function ctlPeliVerPelis (){
    // Obtengo los datos del modelo
    $db = ModeloPeliDB::getModelo();
    $peliculas = $db->getAll();
    // Invoco la vista 
    include_once 'plantillas/verpeliculas.php';
}

function ctlPeliBuscarTitulo() {
    $valor = $_GET['valor'] ?? '';
    $db = ModeloPeliDB::getModelo();
    $peliculas = $db->buscarPorTitulo($valor);
    include_once 'plantillas/verpeliculas.php';
}

function ctlPeliBuscarGenero() {
    $valor = $_GET['valor'] ?? '';
    $db = ModeloPeliDB::getModelo();
    $peliculas = $db->buscarPorGenero($valor);
    include_once 'plantillas/verpeliculas.php';
}

function ctlPeliBuscarDirector() {
    $valor = $_GET['valor'] ?? '';
    $db = ModeloPeliDB::getModelo();
    $peliculas = $db->buscarPorDirector($valor);
    include_once 'plantillas/verpeliculas.php';
}


//Función que limpia el nombre 

function limpiarNombreArchivo($nombreOriginal) {
    $nombre = strtolower(trim($nombreOriginal));
    $nombre = str_replace(' ', '_', $nombre);             // Espacios por guiones bajos
    $nombre = preg_replace('/[^a-z0-9_\.-]/', '', $nombre); // Solo letras, números, _ . -
    return $nombre;
}


//Funcion para LogIn
function ctlPeliLogin(){
    $msg = "";
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $usuario = $_POST['usuario'];
        $clave = $_POST['clave'];

        if($usuario == 'AlbertoProfe' && $clave == '1234'){
            $_SESSION['user'] = $usuario;
            $_SESSION['msg'] = "Bienvenido, $usuario";
            ctlPeliVerPelis();
            return;
        }else{
            $msg = "Usuario o contraseña incorrectos";
        }
    }

    include 'plantillas/login.php';
}

function ctlPeliCerrarSesion(){
    session_destroy();
    session_start();
    $_SESSION['msg'] = "Sesión cerrada";
    ctlPeliVerPelis();
}


//funcion para recibir la votación
function ctlPeliVotar(){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $codigo_pelicula = $_POST['codigo_pelicula'];
        $voto = intval($_POST['voto']);
        $usuario_ip = $_SERVER['REMOTE_ADDR'];

        $db = ModeloPeliDB::getModelo();
        $db->insertarVotos($codigo_pelicula,$voto, $usuario_ip);

        $_SESSION['msg'] = "Gracias por votar";

        ctlPeliDetalles($codigo_pelicula);
    }
}

//funcion para descargar datos en json o pdf
function ctlPeliDescargar(){
    $formato = $_GET['formato'] ?? 'json';
    $db = ModeloPeliDB::getModelo();
    $peliculas = $db->GetAll();

    if($formato === 'json'){
        if ($formato === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="peliculas.json"');
    header('Content-Encoding: UTF-8');

    $outputArray = [];
    foreach ($peliculas as $p) {
        $outputArray[] = [
            'codigo_pelicula' => $p->codigo_pelicula,
            'nombre'          => $p->nombre,
            'director'        => $p->director,
            'genero'          => $p->genero,
            'trailer_id'      => $p->trailer_id
        ];
    }

    echo json_encode($outputArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}


    } elseif($formato === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="peliculas.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Código', 'Nombre', 'Director', 'Género', 'Trailer']);

        foreach($peliculas as $p){
            fputcsv($output, [
                $p->codigo_pelicula,
                $p->nombre,
                $p->director,
                $p->genero,
                $p->trailer_id
            ]);
        }
        fclose($output);
        exit;

    } else{
        $_SESSION['msg'] = "Formato no soportado";
        ctlPeliVerPelis();
    }

}