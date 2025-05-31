
<div id='aviso'><b><?= (isset($msg))?$msg:"" ?></b></div>

<form name='NUEVA' enctype="multipart/form-data" method="POST" action="index.php?orden=Nuevo">

<table>
<tr>
    <td>Título del la película</td>
    <td><input name="nombre" type="text" value="" required></td>
</tr>

<tr>
    <td>Director </td>
    <td> <input name="director" type="text" value="" required> </td>
</tr>

<tr>
    <td>Genero</td>
    <td> <input name="genero" type="text" value ="" required></td>
</tr>
<tr>
    <td>Imagen <i>(opcional)</i></td>
    <td><input name="imagen" type="file" accept="image/*"></td>
</tr>
<tr>
    <td>Tráiler de YouTube</td>
    <td> <input name="trailer_id" type="text" id="trailer_id"></td>
</tr>
</table>

<button type="submit" name="orden" value="Nuevo">Añadir</button>
<button type="button" size="10" onclick="window.location='index.php'" >Volver</button>
</form>