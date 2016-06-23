<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cronograma MCC | Bienvenido</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/foundation.min.css" />
    <script src="js/vendor/modernizr.js"></script>
</head>
<body>
    <div>
        <br>
        <br>
        <br>
    </div>

    <header>
        <div class="row">
            <div class="large-12 columns">
                <h1>Cronograma MCC</h1>
            </div>
        </div>
    </header>

    <div class="row">
        <div class="large-12 columns">
            <form action="php/api.php" method="POST">
                <div class="row">
                    <?php if(isset($_GET["e"]))
                        {
                            if ($_GET["e"] === "1")
                            {
                                echo "<div class='large-12 columns'><small class='error'>Error en el usuario o contraseña. Vuelve a intentarlo.</small>";
                            }
                            else if ($_GET["e"] === "2")
                            {
                                echo "<div class='large-12 columns'><small class='error'>Acceso restringido.</small>";
                            }
                        }
                    ?>
                    <div class="large-8 large-offset-2 medium-8 medium-offset-2 small-12 columns">
                        <label for="usuario">Usuario</label>
                        <input id="usuario" name="usuario" type="text" required>
                    </div>

                    <div class="large-8 large-offset-2 medium-8 medium-offset-2 small-12 columns">
                        <label for="password">Contraseña</label>
                        <input id="password" name="password" type="password" required>
                    </div>

                    <div class="large-4 large-offset-6 medium-4 medium-offset-6 small-12 columns end">
                        <input type="submit" class="small button expand" value="Entrar">
                        <input type="hidden" name="accion" value="iniciar-sesion">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.onload = function() { document.getElementById("usuario").focus(); };
    </script>
</body>
</html>