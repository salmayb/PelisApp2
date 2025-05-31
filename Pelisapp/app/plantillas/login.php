<h2>Iniciar Sesión</h2>

<?php if (isset($msg)) echo "<p style='color:red';> $msg</p>"; ?>

<form method="POST" action="index.php?orden=Login">
    <label>Usuario:</label>
    <input type="text" name="usuario" required><br><br>

    <label>Contraseña:</label>
    <input type="password" name="clave" required><br><br>

    <button type="submit">Entrar</button>
</form>

<br>
<button onclick="window.location='index.php'">Volver</button>