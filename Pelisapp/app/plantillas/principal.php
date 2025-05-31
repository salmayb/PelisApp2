<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD DE PELÍCULAS</title>
    <link href="web/css/default.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="web/js/funciones.js"></script>
</head>

<body>

    <div id="container">
        <div id="header">
            <h1>MI PELÍCULAS PREFERIDAS versión 1.0</h1>
            <div style="float:right;">
                <?php if (isset($_SESSION['user'])): ?>
                     <b><?= $_SESSION['user'] ?></b> |
                    <a href="index.php?orden=Cerrar">Cerrar sesión</a>
                <?php else: ?>
                    <a href="index.php?orden=Login">Iniciar sesión</a>
                <?php endif; ?>
            </div>
        </div>
        <div id="msg">
            <?= $_SESSION['msg'] ?> 
        </div>
        <div id="content">
            <?= $contenido ?>
        </div>
    </div>
</body>

</html>