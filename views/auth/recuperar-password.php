<h1 class="nombre-pagina">Recuperar Password</h1>
<p class="descripcion-pagina">Ingrese su nuevo Password a continuación</p>
<?php
 include_once __DIR__ . '/../templates/alertas.php';
?>
<?php if($error) return; ?>
<form class="formulario" method="POST">
<div class="campo">
        <label for="password">Password</label>
        <input 
            type="password"
            id="password"
            name="password"
            placeholder="Tu Password"
        />
    </div>
    <input type="submit" class="boton" value="Restablecer Password">
</form>
<div class="acciones">
    <a href="/">¿Ya tienes una Cuenta? Inicia Sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes una Cuenta? Crear una.</a>
</div>