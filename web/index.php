<?php
    session_name("cronograma_mcc_2015");
    session_start();

    if ( !isset($_SESSION["usuario"]) )
    {
        header("Location: ../");
        exit;
    }

    $usuario_nombre = $_SESSION["usuario"]["nombre"];
    $claves = $_SESSION["usuario"]["claves"];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Cronograma MCC | Aplicación de Acciones Calendarizadas</title>
        <link rel="shortcut icon" href="../favicon.ico">
        <link rel="stylesheet" href="../css/normalize.css">
        <link rel="stylesheet" href="../css/foundation.min.css" />
        <link rel="stylesheet" href="../css/dataTables.foundation.css">
        <style>
            .overlay
            {
                width: 100%;
                height: 100%;
                position: absolute;
                background: rgba(0,0,0,0.5);
                opacity: 0;
                visibility: hidden;
                z-index: 100000;
            }

            .overlay h3
            {
                position: absolute;
                top: calc(50% - 18px);
                left: calc(50% - 70px);
                color: white;
            }

            .open
            {
                opacity: 1;
                visibility: visible;
            }

            .breadcrumbs li.active a
            {
                color: #9c9c9c;
            }

            #dt-acciones tbody tr
            {
                cursor: pointer;
            }

            #dt-acciones tbody tr:hover
            {
                background-color: #ECEBEB;
            }

            .active-row
            {
                background-color: #29166F !important;
            }

            .active-row td
            {
                color: white !important;
            }

            .no-scroll {
                overflow: hidden;
            }

            .white-background {
                background-color: white !important;
            }

            .off-canvas-list .off-canvas-list-title {
                font-size: 1em;
            }

            .exit-off-canvas {
                width: calc(100% - 15.625rem);
                height: 100%;
                overflow: auto;
                cursor: default !important;
            }

            .exit-off-canvas .exit-off-canvas-x {
                font-size: 2.5rem;
            padding: 1rem 2rem;
            cursor: pointer;
            }

            .exit-off-canvas #eoc-main {
                padding: 20px;
            }

            .off-canvas-wrap.move-right, .off-canvas-wrap.move-left,
            .inner-wrap {
                height: 100%;
            }

            ul.list
            {
                font-size: 0.75rem;
                list-style-type: decimal;
            }

            @media screen and (max-width: 600px) {
                .left-off-canvas-menu {
                    width: 10.625rem
                }

                .exit-off-canvas {
                    width: calc(100% - 10.625rem);
                }

                .move-right>.inner-wrap {
                    -webkit-transform: translate3d(10.625rem, 0, 0);
                    -moz-transform: translate3d(10.625rem, 0, 0);
                    -ms-transform: translate(10.625rem, 0);
                    -ms-transform: translate3d(10.625rem, 0, 0);
                    -o-transform: translate3d(10.625rem, 0, 0);
                    transform: translate3d(10.625rem, 0, 0)
                }
            }
        </style>
        <script src="../js/vendor/modernizr.js"></script>
</head>
<body>
    <div class="overlay"><h3>Cargando...</h3></div>
    <div class="off-canvas-wrap" data-offcanvas>
        <div class="inner-wrap">
            <nav id="top-bar-principal" class="top-bar" data-topbar>
                <ul class="title-area">
                    <li class="name">
                        <h1>
                            <a href="#">
                                Cronograma MCC
                            </a>
                        </h1>
                    </li>
                    <li class="toggle-topbar menu-icon">
                        <a href="#">
                            <span>Menu</span>
                        </a>
                    </li>
                </ul>

                <section class="top-bar-section">
                    <ul class="right">
                        <li class="active"><a href="#">Inicio</a></li>
                        <?php
                            if (in_array("0001", $claves) || in_array("1004", $claves))
                            {
                                echo '<li>
                                    <a href="graficas.php">
                                        Gráficas
                                    </a>
                                </li>';
                            }
                        ?>
                        <?php
                            if (in_array("0001", $claves) || in_array("1005", $claves))
                            {
                                echo '<li>
                                    <a href="reportes.php">
                                        Reportes
                                    </a>
                                </li>';
                            }
                        ?>
                        <!-- <li class="has-dropdown">
                            <a href="eventos.php">Eventos</a>
                            <ul class="dropdown">
                                <li><a class="evento" href="#">Crear Evento</a></li>
                                <li><a class="evento" href="#">Editar Evento</a></li>
                            </ul>
                        </li> -->
                        <li>
                            <a id="cerrar-sesion" href="#">
                                Cerrar Sesión
                            </a>
                        </li>
                    </ul>

                    <ul class="left hide-for-small-only">
                        <li>
                            <a href="#">
                                <?php echo "Bienvenido <b>$usuario_nombre</b>"; ?>
                            </a>
                        </li>
                    </ul>
                </section>
            </nav>

            <!-- Off Canvas Menu -->
            <aside class="left-off-canvas-menu">
                <!-- whatever you want goes here -->
                <ul class="fixed off-canvas-list">
                    <li>
                        <label class="off-canvas-list-title">
                            ¿Qué deseas realizar?
                        </label>
                    </li>
                    <li><label>Acción</label></li>
                    <?php
                        // Clave para editar una acción.
                        /*if (in_array("0001", $claves) || in_array("2002", $claves))
                        {
                            echo '<li>
                                <a id="editar-accion" href="#edit">
                                    Editar Acción
                                </a>
                            </li>';
                        }*/
                    ?>

                    <?php
                        // Clave para eliminar una acción.
                        if (in_array("0001", $claves) || in_array("3001", $claves))
                        {
                            echo '<li>
                                <a id="eliminar-accion" href="#delete">
                                    Eliminar Acción
                                </a>
                            </li>';
                        }
                    ?>
                    <li><label>Evidencias</label></li>
                    <?php
                        // Clave para agregar evidencia.
                        if (in_array("0001", $claves) || in_array("2003", $claves))
                        {
                            echo '<li>
                                <a id="agregar-evidencia" href="#add-file">
                                    Agregar Evidencia
                                </a>
                                <input id="archivo-evidencia" class="hide" type="file"
                                    accept="image/jpeg,image/jpg,image/png,application/pdf">
                            </li>';
                        }
                    ?>
                    <?php
                        // Clave para evaluar evidencias.
                        if (in_array("0001", $claves) || in_array("2004", $claves))
                        {
                            echo '<li>
                                <a id="evaluar-evidencia" href="#eval-file">
                                    Evaluar Evidencias
                                </a>
                            </li>';
                        }
                    ?>
                    <!-- <li><a href="#more-info">Ver Más Información</a></li> -->
                </ul>
            </aside>

            <!-- main content goes here -->
            <section class="main-section">
                <header>
                    <div class="row">
                        <div class="large-12 columns">
                            <h2 style="margin-bottom:0;">Cronograma MCC</h2>
                        </div>

                        <?php
                            // Clave para agregar una nueva acción.
                            if (in_array("0001", $claves) || in_array("2001", $claves)) {
                                echo '<div class="large-6 large-offset-6 medium-6
                                    medium-offset-6 small-12 columns">
                                    <label for="agregar-accion">
                                        Da click a este <strong>botón</strong>
                                        para agregar una nueva acción.
                                    </label>
                                    <a id="agregar-accion" href="#add"
                                        class="small button expand">Agregar Acción</a>
                                </div>';
                            }
                        ?>

                        <div class="large-12 columns">
                            <p class="subheader">Para <strong>Ver más Información</strong>,
                            da <em>clic izquierdo</em> en la
                            acción dentro del listado.</p>
                        </div>

                        <!-- <div class="large-12 columns">
                            <p class="subheader">Da clic en
                            <strong>Crear Nueva Acción</strong>
                            para ingresar manualmente la acción que desees.
                            Si deseas importarlo desde un archivo Excel,
                            da clic en <strong>Importar desde Excel</strong>.</p>
                        </div>

                        <div class="large-6 medium-6 small-12 columns">
                            <a href="#" class="small button expand">Crear Nueva Acción</a>
                        </div>-->

                        <div class="large-6 medium-6 small-12 columns">
                            <a href="#" class="small button expand"
                                onclick="archivo.click()">Importar desde Excel</a>
                            <form action="../php/api.php" method="POST"
                                enctype="multipart/form-data">
                                <input id="archivo" name="file" type="file"
                                    class="hide" onchange="var fd = new FormData(this.parentNode);
                                    fd.append('file', this.files[0]); console.log(fd);
                                    nombreArchivo.textContent = this.files[0].name;
                                    subirArchivo.removeAttribute('disabled');">
                                <small>Nombre del archivo: <strong id="nombreArchivo">
                                    No ha elegido ningún archivo</strong>.
                                </small>
                                <input id="subirArchivo" type="submit"
                                    class="small button" value="Subir" disabled="true">
                                <input type="hidden" name="accion"
                                    value="importar-acciones-excel">
                            </form>
                        </div>

                        <!-- <div class="large-12 columns text-center">
                            <label for="presupuesto-global">
                                Presupuesto solicitado por todas las Comisiones
                            </label>
                            <h3 id="presupuesto-global"></h3>
                        </div> -->
                    </div>
                </header>

                <div class="row">
                    <div class="large-12 columns">
                        <hr>
                    </div>
                </div>

                <div class="row">
                    <?php
                        // Clave para filtrar las acciones.
                        if (in_array("0001", $claves) || in_array("1006", $claves)) {
                            echo '<div class="large-12 medium-12
                                small-12 columns">
                                <ul class="breadcrumbs">
                                    <li class="active">
                                        <a id="ver-todos" href="#">Todos</a>
                                    </li>
                                    <li>
                                        <a id="ver-pendientes" href="#">
                                            Pendientes
                                        </a>
                                    </li>
                                    <li>
                                        <a id="ver-condicionados" href="#">
                                            Condicionados
                                        </a>
                                    </li>
                                </ul>
                            </div>';
                        }
                    ?>

                    <div class="large-12 columns">
                        <table id="dt-acciones" class="tdisplay compact"
                            style="width: 100%;">
                            <thead>
                                <th>ID</th>
                                <th>Núm.</th>
                                <th>Comisión</th>
                                <th>Subcomisión</th>
                                <th>Accion</th>
                                <th>Tareas</th>
                                <th>Unidad Medida</th>
                                <th>Presupuesto</th>
                                <th>Veces</th>
                                <th>Pendientes</th>
                                <th>Condicionados</th>
                            </thead>
                        </table>
                    </div>
                </div>
                </section>
            <!-- close the off-canvas menu -->
            <a class="exit-off-canvas white-background hide">
                <span class="exit-off-canvas-x right">&#215;</span>
                <form id="eoc-main">
                    <div class="row">
                        <div class="large-12 columns">
                            <h3>Información sobre la acción</h3>
                        </div>

                        <div class="large-12 columns">
                            <p class="subheader"></p>
                        </div>

                        <div class="large-12 columns">
                            <label for="num-accion"
                                title="El número de acción.">Número de la Acción
                            </label>
                            <input id="num-accion" type="text" class="numeros"
                                placeholder="Número de la comisión" disabled>
                        </div>

                        <div class="large-12 columns">
                            <label for="comisiones"
                                title="Eige una comisión.">Comisión
                            </label>
                            <select id="comisiones" disabled></select>
                        </div>

                        <div class="large-12 columns">
                            <label for="subcomisiones"
                                title="Elige una subcomisión.">Subcomisión
                            </label>
                            <select id="subcomisiones" disabled></select>
                        </div>

                        <div class="large-12 columns">
                            <label for="accion"
                                title="Describe la acción.">Acción
                            </label>
                            <textarea id="accion" rows="5"
                                placeholder="Acción." disabled></textarea>
                        </div>

                        <div class="large-12 columns">
                            <label for="tareas-realizar"
                                title="Las tareas a realizar.">Tareas a Realizar
                            </label>
                            <textarea id="tareas-realizar" rows="5"
                                placeholder="Tareas a realizar." disabled></textarea>
                        </div>

                        <div class="large-12 columns">
                            <label for="unidad-medida"
                                title="La unidad de medida.">Unidad de Medida
                            </label>
                            <input id="unidad-medida" type="text"
                                placeholder="Unidad de medida" disabled>
                        </div>

                        <div class="large-12 columns">
                            <label for="cantidad-medida"
                                title="La cantidad en la medida.">Cantidad en la Medida
                            </label>
                            <input id="cantidad-medida" type="text" class="numeros"
                                placeholder="Cantidad en la medida" disabled>
                        </div>

                        <div class="large-4 medium-4 small-6 columns">
                            <label for="uso-presupuesto"
                                title="¿Hará uso de presupuesto?.">Uso de Presupuesto
                            </label>
                            <input id="uso-presupuesto" type="checkbox" disabled>
                        </div>

                        <div class="large-8 medium-8 small-6 columns">
                            <label for="monto-presupuesto"
                                title="El monto del presupuesto.">Monto del Presupuesto
                            </label>
                            <input id="monto-presupuesto" type="text" class="montos"
                                placeholder="$" disabled>
                        </div>

                        <div class="large-12 medium-12 small-12 columns">
                            <label for="nombre-responsable"
                                title="El nombre del responsable.">Nombre del Responsable
                            </label>
                            <input id="nombre-responsable" type="text"
                                placeholder="Nombre del Responsable" disabled>
                        </div>

                        <div class="large-6 medium-6 small-6 columns">
                            <label for="email-responsable"
                                title="El email del responsable.">Email del Responsable
                            </label>
                            <input id="email-responsable" type="text"
                                placeholder="Email del Responsable" disabled>
                        </div>

                        <div class="large-6 medium-6 small-6 columns">
                            <label for="telefono-responsable"
                                title="El telefono del responsable.">Telefono del Responsable
                            </label>
                            <input id="telefono-responsable" type="text" class="telefonos"
                                placeholder="Telefono del Responsable" disabled>
                        </div>

                        <?php
                            if (in_array("0001", $claves)
                                || in_array("2001", $claves)
                                || in_array("2002", $claves))
                            {
                                echo '<div class="large-4 large-offset-8 medium-6
                                    medium-offset-6 small-12 columns">
                                    <input id="guardar" type="button"
                                        class="small success button expand" value="Guardar">
                                </div>';
                            }
                        ?>
                    </div>
                </form>
            </a>

        </div>
    </div>

    <div id="evaluar-evidencia-rm" class="medium reveal-modal" data-reveal
        aria-labelledby="evaluar-evidencia-rm-titulo"
        aria-hidden="true" role="dialog">
            <h3 id="evaluar-evidencia-rm-titulo">Evaluar Evidencias</h3>
            <div class="row">
                <div class="large-12 columns">
                    <p class="subheader">
                        Da <em>clic izquierdo</em> en el boton <b>Aceptar</b>
                        para aceptar la evidencia, o el botón <b>Condicionar</b>
                        para que revaluen la evidencia.
                        <!-- <b>Ten en cuenta que solo podrás adjuntar
                        un solo archivo por fecha</b>. -->
                    </p>
                </div>
                <div id="fechas-calendario-accion-con-archivos"
                    class="large-12 columns">
                </div>

                <!-- <div class="large-4 medium-4 small-12 columns">
                    <input id="subir-evidencia" type="button"
                        class="tiny button expand" value="Subir Evidencia">
                </div> -->
            </div>
            <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>

    <div id="agregar-evidencia-rm" class="medium reveal-modal" data-reveal
        aria-labelledby="agregar-evidencia-rm-titulo"
        aria-hidden="true" role="dialog">
            <h3 id="agregar-evidencia-rm-titulo">Agregar Evidencia de una Fecha</h3>
            <div class="row">
                <div class="large-12 columns">
                    <p class="subheader">
                        Da <em>clic izquierdo</em> en la <b>fecha del calendario</b>
                        programado para ésta acción, en la cual quieras <b>adjuntar</b>
                        una evidencia.
                        <!-- <b>Ten en cuenta que solo podrás adjuntar
                        un solo archivo por fecha</b>. -->
                    </p>
                </div>
                <div class="large-12 columns">
                    <ul id="fechas-calendario-accion">

                    </ul>
                </div>

                <!-- <div class="large-4 medium-4 small-12 columns">
                    <input id="subir-evidencia" type="button"
                        class="tiny button expand" value="Subir Evidencia">
                </div> -->
            </div>
            <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>

    <div id="cargando-rm" class="tiny reveal-modal" data-reveal
        aria-hidden="true" role="dialog">
        <p class="text-center">
            Cargando... <img src="../css/images/cargando.gif">
        </p>
    </div>

    <script src="../js/vendor/jquery.js"></script>
    <script src="../js/vendor/jquery.mask.min.js"></script>
    <script src="../js/vendor/jquery.dataTables.min.js"></script>
    <script src="../js/vendor/dataTables.foundation.js"></script>
    <script src="../js/foundation.min.js"></script>
    <script src="../js/foundation/foundation.offcanvas.js"></script>
    <script src="../js/foundation/foundation.topbar.js"></script>
    <script src="../js/foundation/foundation.reveal.js"></script>
    <script>
        (function(){
            /*
            *
            * FOUNDATION INIT
            *
            */
            $(document).foundation({
                topbar :
                {
                    custom_back_text: false,
                    is_hover: false,
                    mobile_show_parent_link: false
                },
                reveal :
                {
                    animation_speed: 0,
                    close_on_background_click: false,
                    multiple_opened: true
                }
            });
        })();
    </script>
    <script>
        var globalData = {};
        window.onload = function()
        {
            /*
            *
            * DECLARAR FUNCIONES
            *
            */
            var mostrarOverlay = function()
            {
                $(".overlay").addClass("open");
                $(document.body).addClass("no-scroll");
            };

            var esconderOverlay = function()
            {
                $(".overlay").removeClass("open");
                $(document.body).removeClass("no-scroll");
            };

            /*
            *
            * OBTENER INFORMACION ESTATICA
            *
            */
            mostrarOverlay();
            // Comisiones
            $.post( "../php/api.php",
            {
                accion: "obtener-comisiones"
            }, function( data )
            {
                if (data.status === "OK")
                {
                    var actualizarSubcomisiones = function(comision, subcomision)
                    {
                        $("#subcomisiones").empty();
                        for (var i = 0; i < globalData.subcomisiones.length; i++)
                        {
                            if (comision === globalData.subcomisiones[i].id_comision)
                            {
                                $("#subcomisiones").append("<option value="+
                                    globalData.subcomisiones[i].id+">"+
                                    globalData.subcomisiones[i].nombre+"</option>");
                            }
                        }

                        if (subcomision !== undefined)
                        {
                            $("#subcomisiones").val(subcomision);
                        }
                    };

                    globalData.comisiones = data.resultado;
                    for (var i = 0; i < globalData.comisiones.length; i++)
                    {
                        $("#comisiones").append("<option value="+
                            globalData.comisiones[i].id+">"+
                            globalData.comisiones[i].nombre+"</option>");
                    }

                    $("#comisiones").on("change", function()
                    {
                        actualizarSubcomisiones(this.value,
                            globalData.subcomisionSeleccionada);

                        globalData.subcomisionSeleccionada = undefined;
                    });

                    // Subcomisiones
                    $.post( "../php/api.php",
                    {
                        accion: "obtener-subcomisiones"
                    }, function( data )
                    {
                        if (data.status === "OK")
                        {
                            globalData.subcomisiones = data.resultado;
                            actualizarSubcomisiones($("#comisiones").val());
                        }
                        else
                        {
                            alert("¡Oh no, faltó cargar información!\nRecarga la página"+
                                " aplanando la tecla F5.");
                        }

                        esconderOverlay();
                    }, "json");
                }
                else
                {
                    alert("¡Oh no, faltó cargar información!\nRecarga la página"+
                        " aplanando la tecla F5.");
                }
            }, "json");

            /*
            *
            * EVENTOS EN OFF-CANVAS
            *
            */

            // Evento que se ejecuta al abrirse el off-canvas.
            $(document).on("open.fndtn.offcanvas", "[data-offcanvas]", function()
            {
                // Quita el scroll-bar del body, no es necesario en esta vista.
                $(document.body).addClass("no-scroll");

                // Cambia el color del contenedor off-canvas
                // para ocultar la info de abajo.
                // $("a.exit-off-canvas").addClass("white-background");

                // Muestra el contenedor de la info sobre la acción.
                $(".exit-off-canvas").removeClass("hide");
            });

            // Evento que se ejecuta al cerrarse el off-canvas.
            $(document).on("close.fndtn.offcanvas", "[data-offcanvas]", function()
            {
                // Regresa el scroll-bar al body.
                $(document.body).removeClass("no-scroll");

                // Esconde el contenedor de la info sobre la acción.
                $(".exit-off-canvas").addClass("hide");

                // Quita todos los active-row que se encuentren de las filas mostradas.
                $("tr.active-row").removeClass("active-row");
            });

            // Evento que esconde el off-canvas con la tecla ESC.
            $(document).on("keyup", function()
            {
                if ( !$(".exit-off-canvas").hasClass("hide") &&
                    event.which === 27 &&
                    !$(".reveal-modal").hasClass("open") )
                {
                    $(".off-canvas-wrap").foundation("offcanvas", "toggle", "move-right");
                }
            });

            // Evento que pinta de negro el <li> activo en el menu del off-canvas.
            /*$(document).on("click", "ul.off-canvas-list li a", function()
            {
                $("ul.off-canvas-list").find("li.active").removeClass("active");
                this.parentNode.classList.add("active");
            });*/

            $(document).on("scroll", function(event)
            {
                $("ul.off-canvas-list").css("top", this.body.scrollTop + "px");
            });

            /*
            *
            * EVENTOS EN TABLA ACCIONES '#dt-acciones'
            *
            */

            // Inicializar Datatables
            $('#dt-acciones').dataTable(
            {
                "language":
                {
                "url": "../json/datatables.spanish.lang.json"
                },
                "processing": true,
                "serverSide": true,
                "ajax": '../php/scripts/server_processing.php',
                "columns":
                [
                    { "width": "", "className" : "hide id" },
                    { "width": "5%", "className" : "hide-for-small-only num-accion" },
                    { "width": "10%", "className" : "hide-for-small-only" },
                    { "width": "10%", "className" : "" },
                    { "width": "25%", "className" : "" },
                    { "width": "20%", "className" : "show-for-large-only" },
                    { "width": "15%", "className" : "hide-for-small-only" },
                    { "width": "10%", "className" : "" },
                    { "width": "5%", "className" : "show-for-large-only" },
                    { "width": "0%", "className" : "pendientes", "visible": false },
                    { "width": "0%", "className" : "condicionados", "visible": false }
                ],
                "initComplete": function(settings, json)
                {

                }
            });

            // Evento del click para cada renglon de la tabla de acciones.
            $(document).on("click", "#dt-acciones tbody tr", function()
            {
                var id = $(this).find("td.id").text();

                // Quitar todos los tr.active-row que se encuentren.
                $("tr.active-row").removeClass("active-row");

                // Agregar active-row al tr actual.
                this.classList.add("active-row");

                // Mostrar el menu izquierdo si es que fue escondido.
                $("aside.left-off-canvas-menu").removeClass("hide");

                // Activar la opción de editar si el usuario tiene acceso.
                <?php
                    if (in_array("0001", $claves) || in_array("2002", $claves))
                    {
                        // echo '$("#editar-accion").click();';
                        echo 'if ($("tr.active-row").length === 0)
                        {
                            alert("No se ha elegido alguna acción.");
                            return;
                        }

                        $("#guardar")[0].dataset.accion = "guardar-cambios-accion";
                        $("#eoc-main [disabled]").prop("disabled", false);
                        $("#num-accion").focus();';
                    }
                ?>

                // Obtener toda la info de la accion.
                mostrarOverlay();
                $.post( "../php/api.php",
                {
                    accion: "obtener-accion",
                    id: id
                }, function( data )
                {
                    if (data.status === "OK")
                    {
                        var resultado = data.resultado;
                        $("#num-accion").val(resultado.num);

                        // Prepara a la subcomision para ser seleccionada.
                        globalData.subcomisionSeleccionada = resultado.subcomision;

                        // Puede o no que se ejecute el evento "change".
                        $("#comisiones").val(resultado.comision).change();

                        // En caso de que no se haya ejecutado
                        // el evento "change" de comision, dar focus a la subcomision.
                        // $("#subcomisiones").val(resultado.subcomision);
                        // globalData.subcomisionSeleccionada = undefined;

                        $("#accion").val(resultado.accion);
                        $("#tareas-realizar").val(resultado.tareas);
                        $("#unidad-medida").val(resultado.unidad);
                        $("#cantidad-medida").val(resultado.cantidad);
                        $("#uso-presupuesto")[0].checked = resultado.presupuesto ? true : false;
                        $("#monto-presupuesto").val(resultado.monto);
                        $("#nombre-responsable").val(resultado.responsable);
                        $("#email-responsable").val(resultado.email);
                        $("#telefono-responsable").val(resultado.telefono);
                    }
                    else
                    {
                        alert("¡Oh no, falló al cargar información!\nVuelve a dar clic"+
                        " en la acción porfavor.");
                    }

                    esconderOverlay();
                }, "json");

                // Ejecutar el OffCanvas.
                $(".off-canvas-wrap").foundation("offcanvas", "toggle", "move-right");
            });

            <?php
                // Agregar una acción.
                if (in_array("0001", $claves) || in_array("2002", $claves))
                {
                    echo '$("#agregar-accion").on("click", function()
                    {
                        // Esconder el menu izquierdo.
                        $("aside.left-off-canvas-menu").addClass("hide");

                        // Limpiar el form.
                        $("#eoc-main")[0].reset();
                        $("#comisiones").change();

                        // Habilitar todos los inputs.
                        $("#eoc-main [disabled]").prop("disabled", false);

                        // Agregar la accion al boton Guardar.
                        $("#guardar")[0].dataset.accion = "agregar-accion";

                        // Mostrar el canvas
                        $(".off-canvas-wrap").foundation("offcanvas", "show", "move-right");

                        // Hacer focus en el primer input.
                        $("#eoc-main input:first").focus();
                    });';

                    echo '$("#guardar").on("click", function()
                    {
                        // Prepara el nombre de la accion para usarlo en el "confirm".
                        var accionSplit = this.dataset.accion.split("-");

                        var confirmar = confirm("Estas a punto de " +
                            accionSplit.join(" ").toUpperCase() +
                            ".\n\n¿Deseas continuar?");

                        if (confirmar)
                        {
                            mostrarOverlay();
                            var id = $("#dt-acciones tbody tr.active-row").find("td.id").text();

                            $.post("../php/api.php",
                            {
                                accion: this.dataset.accion,
                                id: id,
                                num: $("#num-accion").val(),
                                comision: $("#comisiones").val(),
                                subcomision: $("#subcomisiones").val(),
                                _accion: $("#accion").val(),
                                tareas: $("#tareas-realizar").val(),
                                unidad: $("#unidad-medida").val(),
                                cantidad: $("#cantidad-medida").val(),
                                presupuesto: $("#uso-presupuesto").prop("checked"),
                                monto: $("#monto-presupuesto").val(),
                                responsable: $("#nombre-responsable").val(),
                                email: $("#email-responsable").val(),
                                telefono: $("#telefono-responsable").val()
                            }, function( data )
                            {
                                if (data.status === "OK")
                                {
                                    $("#dt-acciones").DataTable().draw();
                                    $(".off-canvas-wrap").foundation("offcanvas",
                                        "toggle", "move-right");
                                }

                                alert(data.msg);
                                esconderOverlay();
                            }, "json");
                        }
                    });';
                }

                // Editar una acción.
                if (in_array("0001", $claves) || in_array("2002", $claves))
                {
                    /*echo '$("#editar-accion").on("click", function()
                    {
                        if ($("tr.active-row").length === 0)
                        {
                            alert("No se ha elegido alguna acción.");
                            return;
                        }

                        $("#guardar")[0].dataset.accion = "guardar-cambios-accion";
                        $("#eoc-main [disabled]").prop("disabled", false);
                        $("#num-accion").focus();
                    });';*/
                }


                // Clave para eliminar una acción.
                if (in_array("0001", $claves) || in_array("3001", $claves))
                {
                    echo '$("#eliminar-accion").on("click", function()
                    {
                        var confirmar = confirm("Estás a punto de ELIMINAR ésta acción.\n\n"+
                            "¿Deseas continuar?");
                        if (confirmar)
                        {
                            mostrarOverlay();
                            var id = $("#dt-acciones tbody tr.active-row").find("td.id").text();

                            $.post("../php/api.php",
                            {
                                accion: "eliminar-accion",
                                id: id
                            }, function( data )
                            {
                                if (data.status === "OK")
                                {
                                    $("#dt-acciones").DataTable().draw();
                                    $(".off-canvas-wrap").foundation("offcanvas",
                                        "toggle", "move-right");
                                }

                                alert(data.msg);
                                esconderOverlay();
                            }, "json");
                        }
                    });';
                }

                // Clave para agregar una evidencia.
                if (in_array("0001", $claves) || in_array("2003", $claves)) {
                    // Evento para agregar evidencia.
                    echo '$("#agregar-evidencia").click(function()
                    {
                        if ($("tr.active-row").length === 0)
                        {
                            alert("No se ha elegido alguna acción.");
                            return;
                        }

                        // Cargar las fechas del calendario para esta acción.
                        mostrarOverlay();
                        $.post( "../php/api.php",
                        {
                            accion: "obtener-calendario-por-accion-con-archivos",
                            id: $("tr.active-row").find("td.id").text()
                        }, function( data )
                        {
                                if ( data.status === "OK" )
                                {
                                    var resultado = data.resultado;
                                    $("#fechas-calendario-accion").empty();

                                    resultado.forEach(function(value, index, array)
                                    {
                                        var observaciones = value.observaciones === "" ? "" : "<p style=\'"+
                                            "margin:0;font-size:14px;color:#D9251C;\'>*"+
                                            value.observaciones+"</p>";
                                        var archivos = value.archivos;
                                        var listaArchivos = "";
                                        if (archivos.length === 0)
                                        {
                                            listaArchivos = "<li><em>No hay evidencias.</em></li>";
                                        }
                                        else
                                        {
                                            archivos.forEach(function(value, index, array)
                                            {
                                                var nombreArchivo = value.split(\'/\').pop();
                                                listaArchivos += "<li>"+
                                                    "<a href=\'"+value+"\' target=\'_blank\'>"+
                                                        nombreArchivo+
                                                    "</a>"+
                                                "</li>";
                                            });
                                        }

                                        $("#fechas-calendario-accion").append("<li><a href=#1."+index+" data-id="+value.id+" data-inicio=\'"+value.inicio+"\' data-termino=\'"+value.termino+
                                            "\' data-bloqueado=\'"+value.bloqueado+"\' data-upload>Del "+value.inicio+" al "+value.termino+".</a>&nbsp;"+value.estatus+observaciones+
                                            "<ul class=\'list\'>"+listaArchivos+"</ul></li>");
                                    });

                                    // Agregar evento para ejecutar el \'clic\' del input file.
                                    $("#fechas-calendario-accion li a[data-upload]").click(function()
                                    {
                                        if (this.dataset.bloqueado === "1")
                                        {
                                            alert("No es posible subir un archivo.\n\nEsta acción ya esta COMPLETADA o CANCELADA en esta fecha.");
                                        }
                                        else
                                        {
                                            $("#archivo-evidencia").attr("data-id", this.dataset.id)
                                            .attr("data-inicio", this.dataset.inicio)
                                            .attr("data-termino", this.dataset.termino)
                                            .click();
                                        }
                                    });

                                    setTimeout(function() { $("#agregar-evidencia-rm").foundation("reveal", "open"); }, 1);
                                }
                                else
                                {
                                    alert("¡Oops! Ocurrió un error, por favor vuelve a intentarlo.");
                                }

                                esconderOverlay();
                        }, "json");
                    });';

                    // Evento para subir evidencia.
                    echo '$("#archivo-evidencia").change(function()
                    {
                        var file = this.files[0];
                        //var reader = new FileReader();
                        //reader.readAsDataURL(file);
                        //handleFiles(files);

                        if (!file.type.match(\'image.*\') && !file.type.match(\'pdf.*\'))
                        {
                            alert("Tipo de archivo no válido.\n\nPor favor verifica que el archivo sea imagen o PDF.");
                            return false;
                        }
                        else
                        {
                            var confirmarSubida = confirm("¿Estás seguro que deseas adjuntar el archivo:\n\n\'"+file.name+"\'\n\n...como evidencia de ésta acción que se realizó del "+this.dataset.inicio+" al "+this.dataset.termino+"?")

                            if (confirmarSubida)
                            {
                                mostrarOverlay();
                                // IE 10+, Firefox 4.0+, Chrome 7+, Safari 5+, Opera 12+
                                var data = new FormData();
                                    data.append("evidencia", file);
                                    data.append("accion", "subir-evidencia");
                                    data.append("id", this.dataset.id);

                                    $.ajax({
                                        url: "../php/api.php",
                                        type: "POST",
                                        data: data,
                                        cache: false,
                                        dataType: "json",
                                            processData: false, // Don\'t process the files
                                            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                                            success: function(data, textStatus, jqXHR)
                                            {
                                                if(data.status === "OK")
                                                {
                                                    alert("Archivo guardado con éxito.");

                                                    // Refrescar el modal de evidencias para que aparescan los labels \'En Revision\'
                                                    $("#agregar-evidencia-rm").foundation("reveal", "close");
                                                    setTimeout(function() { $("#agregar-evidencia").click(); }, 100);

                                                    $("#archivo-evidencia").attr("data-id", "")
                                                    .attr("data-inicio", "")
                                                    .attr("data-termino", "");
                                                }
                                                else
                                                {
                                                        // Handle errors here
                                                        //console.log(\'ERRORS: \' + data.error);
                                                }

                                                esconderOverlay();
                                            },
                                            error: function(jqXHR, textStatus, errorThrown)
                                            {
                                                    // Handle errors here
                                                    console.log(\'ERRORS: \' + textStatus);
                                                    // STOP LOADING SPINNER
                                                    esconderOverlay();
                                            }
                                    });
                            };
                        };

                        // Para provocar que el evento \'change\' se ejecute aunque eliga el mismo archivo y no marque error si no elige ningun archivo.
                        this.value = "";
                    });';
                }

                // Clave para evaluar una evidencia.
                if (in_array("0001", $claves) || in_array("2004", $claves)) {
                    // Evento para evaluar la evidencia
                    echo '$("#evaluar-evidencia").click(function()
                    {
                        if ($("tr.active-row").length === 0)
                        {
                            alert("No se ha elegido alguna acción.");
                            return;
                        }

                        // Cargar las fechas del calendario para esta acción.
                        mostrarOverlay();
                        $.post( "../php/api.php",
                        {
                            accion: "obtener-calendario-por-accion-con-archivos",
                            id: $("tr.active-row").find("td.id").text()
                        }, function( data )
                        {
                                if ( data.status === "OK" )
                                {
                                    var resultado = data.resultado;
                                    $("#fechas-calendario-accion-con-archivos").empty();

                                    resultado.forEach(function(value, index, array)
                                    {
                                        var observaciones = value.observaciones === "" ? "" : "<p style=\'"+
                                            "margin:0;font-size:14px;color:#D9251C;\'>*"+
                                            value.observaciones+"</p>";
                                        var archivos = value.archivos;
                                        var listaArchivos = "";
                                        if (archivos.length === 0)
                                        {
                                            listaArchivos = "<li><em>No hay evidencias.</em></li>";
                                        }
                                        else
                                        {
                                            archivos.forEach(function(value, index, array)
                                            {
                                                var nombreArchivo = value.split(\'/\').pop();
                                                listaArchivos += "<li>"+
                                                    "<a href=\'"+value+"\' target=\'_blank\'>"+
                                                        nombreArchivo+
                                                    "</a>"+
                                                "</li>";
                                            });
                                        }


                                        $("#fechas-calendario-accion-con-archivos").append("<div class=\'row\'>"+
                                            "<div class=\'large-12 medium-12 small-12 columns\'>"+
                                                "<span>Del "+value.inicio+" al "+value.termino+"&nbsp;"+value.estatus+observaciones+"</span>"+
                                            "</div>"+
                                            "<div class=\'large-8 medium-6 small-12 columns\'>"+
                                                "<ul class=\'list\'>"+listaArchivos+"</ul>"+
                                            "</div>"+
                                            "<div class=\'large-2 medium-3 small-6 columns\'>"+
                                                "<a href=#acept class=\'aceptar-evidencia tiny success button expand\' data-id="+value.id+" style=\'margin:0;\'>Aceptar</a>"+
                                            "</div>"+
                                            "<div class=\'large-2 medium-3 small-6 columns\'>"+
                                                "<a href=#reval class=\'condicionar-evidencia tiny warning button expand\' data-id="+value.id+" style=\'margin:0;\'>Condicionar</a>"+
                                            "</div>"+
                                        "</div>");
                                    });

                                    // Agregar evento para ejecutar el \'clic\' de Condicionar y Aceptar.
                                    $(".condicionar-evidencia").click(function()
                                    {
                                        var confirmar = prompt("¿Estás seguro que deseas CONDICIONAR las evidencias?\n\nFavor de agregar el motivo de la revaluación.")

                                        if (confirmar !== null)
                                        {
                                            mostrarOverlay();
                                            var observaciones = confirmar;
                                            $.post( "../php/api.php",
                                            {
                                                accion: "condicionar-evidencia",
                                                id: this.dataset.id,
                                                observaciones: observaciones
                                            }, function( data )
                                            {
                                                if (data.status === "OK")
                                                {
                                                    alert("¡Evidencia revaluada con éxito!");

                                                    $("#evaluar-evidencia-rm").foundation("reveal", "close");
                                                                    setTimeout(function() { $("#evaluar-evidencia").click(); }, 100);
                                                }
                                                else
                                                {
                                                    alert("¡Oops! Ocurrió un error, por favor vuelve a intentarlo.");
                                                }

                                                esconderOverlay();
                                            }, "json");
                                        }
                                    });

                                    $(".aceptar-evidencia").click(function()
                                    {
                                        var confirmar = confirm("¿Estás seguro que deseas ACEPTAR las evidencias?");

                                        if (confirmar)
                                        {
                                            mostrarOverlay();
                                            $.post( "../php/api.php",
                                            {
                                                accion: "aceptar-evidencia",
                                                id: this.dataset.id
                                            }, function( data )
                                            {
                                                if (data.status === "OK")
                                                {
                                                    alert("¡Evidencia aceptada con éxito!");

                                                    $("#evaluar-evidencia-rm").foundation("reveal", "close");
                                                                    setTimeout(function() { $("#evaluar-evidencia").click(); }, 100);

                                                    $("#dt-acciones").DataTable().draw();
                                                }
                                                else
                                                {
                                                    alert("¡Oops! Ocurrió un error, por favor vuelve a intentarlo.");
                                                }

                                                esconderOverlay();
                                            }, "json");
                                        };
                                    });

                                    // setTimeout(function() {
                                    //  if (!$("#evaluar-evidencia-rm").hasClass("open"))
                                    //  {
                                    //      $("#evaluar-evidencia-rm").foundation("reveal", "open");
                                    //  };
                                    // }, 1);

                                    setTimeout(function() { $("#evaluar-evidencia-rm").foundation("reveal", "open"); }, 1);
                                }
                                else
                                {
                                    alert("¡Oops! Ocurrió un error, por favor vuelve a intentarlo.");
                                }

                                esconderOverlay();
                        }, "json");
                    });';
                }
            ?>

            <?php
                // Clave para filtrar las acciones.
                if (in_array("0001", $claves) || in_array("1006", $claves)) {
                    echo '$("#ver-todos").on("click", function()
                    {
                        $("ul.breadcrumbs li.active").removeClass("active");
                        $(this).parent().addClass("active");

                        $("#dt-acciones").DataTable()
                            .columns().search("").draw();
                    });';

                    echo '$("#ver-pendientes").on("click", function()
                    {
                        $("ul.breadcrumbs li.active").removeClass("active");
                        $(this).parent().addClass("active");

                        $("#dt-acciones").DataTable()
                            .columns().search("")
                            .columns(".pendientes")
                            .search(1).draw();
                    });';

                    echo '$("#ver-condicionados").on("click", function()
                    {
                        $("ul.breadcrumbs li.active").removeClass("active");
                        $(this).parent().addClass("active");

                        $("#dt-acciones").DataTable()
                            .columns().search("")
                            .columns(".condicionados")
                            .search(1).draw();
                    });';
                }
            ?>

            /*
            *
            * JQUERY MASK
            *
            */

            $(".numeros").mask("000000");
            // $(".montos").mask("#,##0.00", {reverse: true});
            $(".montos").mask("000000000");
            $(".telefonos").mask("(000) 000-0000", {clearIfNotMatch: true});

            var date = new Date();
            var dia = date.getDate();
            var mes = date.getMonth() + 1;
            var temporada = date.getFullYear();
            var topBar =
            {
                cerrarSesion : document.getElementById("cerrar-sesion")
            };

            topBar.cerrarSesion.onclick = function()
            {
                mostrarOverlay();
                $.post( "../php/api.php",
                {
                    accion: "cerrar-sesion"
                }, function( data )
                {
                        if ( data.status === "OK" )
                        {
                            window.location.href = "../";
                        }
                }, "json");
            };

            // Imprimir el Presupuesto Global
            // $.post( "../php/api.php",
            // {
            //  accion: "obtener-presupuesto"
            // }, function( data )
            // {
            //      if ( data.status === "OK" )
            //      {
            //          var resultado = data.resultado;
            //      var presupuesto = '$' + resultado.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
            //          document.getElementById("presupuesto-global").innerHTML = presupuesto;
            //      };
            // }, "json");
        };
    </script>
</body>
</html>