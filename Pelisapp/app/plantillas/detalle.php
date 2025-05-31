<h1>Detalles de la Película</h1>
    <div id="pelicula">
             <p><strong>Código : </strong><?= $peli->codigo_pelicula ?></p>
            <p><strong>Nombre : </strong><?= $peli->nombre ?></p>
            <p><strong>Director :</strong> <?= $peli->director ?></p>
            <p><strong>Género : </strong> <?=$peli->genero ?></p>  
            <img src="<?='app/img/'.$peli->imagen; ?>" alt="Imagen no disponible">
            
            <?php if (!empty($peli->trailer_id)) : ?>
                <div class="trailer">
                <h3>Tráiler</h3>
                <iframe width="560" height="315" src="https://www.youtube.com/embed/<?= $peli->trailer_id ?>" frameborder="0" allowfullscreen></iframe>
    </div>
<?php endif; ?>

    </div>
    <?php if(isset($votos)) : ?>
        <div id="votacion">
            <p><strong>Puntuación media:</strong> <?= $votos['total'] > 0 ? number_format($votos['media'], 2) : 'Sin votos aún' ?> </p>
            <p><strong>Número de votos:</strong> <?= $votos['total'] ?></p>
        </div>
    <?php endif;?>

    <form action="index.php?orden=Votar" method="POST">
        <input type="hidden" name="codigo_pelicula" value="<?= $peli->codigo_pelicula ?>">
        <label for="voto">Valora esta película:</label>
        <select name="voto" id="voto" required>
            <option value="1">1 estrella</option>
            <option value="2">2 estrella</option>
            <option value="3">3 estrella</option>
            <option value="4">4 estrella</option>
            <option value="5">5 estrella</option>
        </select>
        <button type="submit">Votar</button>
    </form>

<br>
<input type="button" value=" Volver "  class="btn-volver" onclick="javascript:window.location='index.php'" >
