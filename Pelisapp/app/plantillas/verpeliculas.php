<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Películas</title>
</head>

<body>

    <div id="container">
        <div id="header">
            <h1>Listado de Películas</h1>
        </div>

        <button onclick="window.location.href='index.php?orden=Descargar&formato=json'">Descargar JSON</button>
        <button onclick="window.location.href='index.php?orden=Descargar&formato=csv'">Descargar CSV</button>

        <br><br>

        <form>
            <input type="text" name="valor">
            <button type="submit" name="orden" value="BuscarPorTitulo">Buscar Por Título</button>
            <button type="submit" name="orden" value="BuscarPorGenero">Buscar Por Género</button>
            <button type="submit" name="orden" value="BuscarPorDirector">Buscar Por Director</button>
        </form>

        <h2>Listado de Películas</h2>
        <table border="1">
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Director</th>
                <th>Género</th>
                <?php if (isset($_SESSION['user'])): ?>
                    <th>Modificar</th>
                    <th>Detalles</th>
                    <th>Borrar</th>
                <?php else: ?>
                    <th colspan="3">Acciones</th>
                <?php endif; ?>
            </tr>
            <?php foreach ($peliculas as $peli): ?>
                <tr>
                    <td><?= $peli->codigo_pelicula ?></td>
                    <td><?= $peli->nombre ?></td>
                    <td><?= $peli->director ?></td>
                    <td><?= $peli->genero ?></td>
                    
                    <?php if (isset($_SESSION['user'])): ?>
                        <td><a href="?orden=Modificar&id=<?= $peli->codigo_pelicula ?>">Modificar</a></td>
                        <td><a href="?orden=Detalles&id=<?= $peli->codigo_pelicula ?>">Detalles</a></td>
                        <td><a href="#" onclick="confirmarBorrar('<?= $peli->nombre ?>','<?= $peli->codigo_pelicula?>');">Borrar</a></td>
                    <?php else: ?>
                        <td colspan="3"><a href="?orden=Detalles&id=<?= $peli->codigo_pelicula ?>">Ver detalles</a></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>

        <br>
        <form>
            <?php if (isset($_SESSION['user'])): ?>
                <button type="submit" name="orden" value="Nuevo">Pelicula Nueva</button>
            <?php endif; ?>
            <button type="button" onclick="window.location='index.php?orden=VerPelis'">Ver Todas</button>
        </form>

        <script>
            function confirmarBorrar(nombre, id) {
                if (confirm("¿Seguro que quieres borrar la película '" + nombre + "'?")) {
                    window.location = "?orden=Borrar&id=" + id;
                }
            }
        </script>

    </div>
</body>
</html>
