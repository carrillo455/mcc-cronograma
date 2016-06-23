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

    if (!in_array("0001", $claves) && !in_array("1004", $claves)) {
        header("Location: index.php");
        exit;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cronograma MCC | Reportes</title>
    <link rel="shortcut icon" href="../favicon.ico">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/foundation.min.css" />
    <link rel="stylesheet" href="../css/foundation.calendar.css">
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

        table
        {
            width: 100%;
        }

        table caption
        {
            padding: 10px;
            font-size: 1.5em;
            text-align: center;
            background: #797979;
            color: #fff;
        }

        table thead th,
        table tbody td
        {
            font-size: 0.65rem !important;
            line-height: 0.70rem !important;
        }

        table thead th
        {
            text-align: center;
            text-transform: uppercase;
        }

        table tbody td.pendientes hr
        {
            margin: 3px 0;
        }

        .subtitle
        {
            display: block;
            margin: 5px 0;
            line-height: 0.8rem;
        }

        img.radius
        {
            border-radius: 3px;
        }

        [data-graph]
        {
            width:100%;height:500px;
        }

        @media print
        {
            @page
            {
                margin: 15px;
            }

            .no-print
            {
                display: none;
            }

            table
            {
                border: none;
            }

            h4,
            h5
            {
                margin: 0;
            }

            h4
            {
                font-size: 1rem;
            }

            h5
            {
                font-size: 0.75rem;
            }

            #contenedor-reporte table th,
            #contenedor-reporte table td
            {
                border: 1px solid #000;
            }
        }

    </style>
    <script src="../js/vendor/modernizr.js"></script>
</head>
<body>
    <div class="overlay"><h3>Cargando...</h3></div>
    <nav id="top-bar-principal" class="top-bar no-print" data-topbar>
        <ul class="title-area">
            <li class="name">
                <h1><a href="#">Cronograma MCC</a></h1>
            </li>
            <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
        </ul>

        <section class="top-bar-section">
            <ul class="right">
                <li><a href="index.php">Inicio</a></li>
                <li><a href="graficas.php">Gráficas</a></li>
                <li class="active"><a href="reportes.php">Reportes</a></li>
                <!-- <li class="has-dropdown">
                    <a href="eventos.php">Eventos</a>
                    <ul class="dropdown">
                        <li><a class="evento" href="#">Crear Evento</a></li>
                        <li><a class="evento" href="#">Editar Evento</a></li>
                    </ul>
                </li> -->
                <li><a id="cerrar-sesion" href="#">Cerrar Sesión</a></li>
            </ul>

            <ul class="left hide-for-small-only">
                <li><a href="#"><?php echo "Bienvenido <b>$usuario_nombre</b>"; ?></a></li>
            </ul>
        </section>
    </nav>

    <header>
        <div class="row no-print">
            <div class="large-12 columns">
                <h2>Cronograma MCC | Reportes</h2>
            </div>

            <div class="large-12 column">
                <label for="tipo-reporte">Tipo de Reporte</label>
                <select id="tipo-reporte">
                    <option value="1">RP01 - Completos y Vencidos</option>
                    <option value="2">RP02 - Informacion de Actividades por Comision</option>
                </select>
            </div>

            <div class="large-6 medium-6 small-12 column">
                <label for="comisiones">Comisiones</label>
                <select id="comisiones"></select>
            </div>

            <div class="large-6 medium-6 small-12 column">
                <label for="subcomisiones">Subcomisiones</label>
                <select id="subcomisiones"></select>
            </div>

            <div class="large-4 medium-4 small-12 columns">
                <label for="fecha-inicio">Fecha de Inicio</label>
                <input id="fecha-inicio" class="fechas" type="text" value="01/01/2015" placeholder="dd/mm/yyyy">
            </div>

            <div class="large-4 medium-4 small-12 columns">
                <label for="fecha-termino">Fecha de Termino</label>
                <input id="fecha-termino" class="fechas" type="text" value="12/07/2016" placeholder="dd/mm/yyyy">
            </div>

            <div class="large-4 medium-4 small-12 columns">
                <label for="generar-reporte">Da clic aquí para generar el reporte.</label>
                <a id="generar-reporte" href="#" class="small button expand">Generar</a>
            </div>
        </div>
    </header>

    <div class="row no-print">
        <div class="large-12 columns">
            <hr>
        </div>
    </div>

    <!-- Contenedor del Reporte -->
    <div class="row">
        <div class="large-offset-10 large-2 medium-offset-8 medium-4 small-12 columns no-print">
            <a id="imprimir-reporte" href="#" class="small button expand">Imprimir</a>
        </div>

        <div class="large-12 columns">
            <table>
                <tr>
                    <td style="width:15%;">
                        <img src="../images/mcc-logo.png" alt="Logo de MCC">
                    </td>
                    <td style="width:70%;">
                        <h3 class="text-center">
                            MCC Diocesis de Tampico
                            <small id="titulo-reporte" class="text-align subtitle"></small>
                            <small id="fechas-reporte" class="text-align subtitle"></small>
                        </h3>
                    </td>
                    <td style="width:15%;">
                        <img src="../images/diocesis-tampico-logo.png" class="radius" alt="Logo de MCC">
                    </td>
                </tr>

                <tr>
                    <td id="contenedor-reporte" colspan="3">
                        <!-- <table id="tabla-reporte">
                            <thead></thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table> -->
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row hide">
        <div class="large-12 columns">
            <h3>Presupuesto por Comisión</h3>
            <div id="contenedor-presupuesto-comision" class="row">

            </div>
        </div>
    </div>

    <div id="cargando-modal" class="tiny reveal-modal" data-reveal aria-hidden="true" role="dialog">
        <p class="text-center">Cargando... <img src="../css/images/cargando.gif"></p>
    </div>

    <script src="../js/vendor/jquery.js"></script>
    <script src="../js/vendor/jquery.mask.min.js"></script>
    <script src="../js/vendor/jquery.dataTables.min.js"></script>
    <script src="../js/vendor/dataTables.foundation.js"></script>
    <script src="../js/foundation.min.js"></script>
    <script src="../js/foundation/foundation.topbar.js"></script>
    <script src="../js/foundation/foundation.reveal.js"></script>
    <script>$(document).foundation({
        topbar :
        {
            custom_back_text: false,
            is_hover: false,
            mobile_show_parent_link: false
        },
        reveal :
        {
            animation_speed: 0,
            close_on_background_click: false
        }
    });</script>
    <script src="amcharts/amcharts.js"></script>
    <script src="amcharts/pie.js"></script>
    <script src="amcharts/themes/light.js"></script>

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
                    var actualizarSubcomisiones = function(comision, subcomision, generar)
                    {
                        $("#subcomisiones").empty();
                        for (var i = 0; i < globalData.subcomisiones.length; i++)
                        {
                            if (comision == globalData.subcomisiones[i].id_comision)
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

                        if (generar)
                        {
                            $('#generar-reporte').trigger('click');
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
                            globalData.subcomisionSeleccionada, false);

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

                            <?php
                                // Entrar a través de GET directamente a un reporte.
                                $rp = isset($_GET["rp"])
                                    ? $_GET["rp"] : 0;

                                $comision = isset($_GET["c"])
                                    ? $_GET["c"] : 0;

                                $subcomision = isset($_GET["sc"])
                                    ? $_GET["sc"] : 0;

                                if ($rp && $comision && $subcomision) {
                                    // Elige el tipo de reporte.
                                    print_r("$('#tipo-reporte').val($rp);");

                                    // Elige la comision.
                                    print_r("$('#comisiones').val($comision);");

                                    // Ejecuta funcion que elige la comision,
                                    // la subcomision y genera el reporte.
                                    print_r("actualizarSubcomisiones($comision,$subcomision, true);");
                                } else {
                                    print_r("actualizarSubcomisiones($('#comisiones').val(),undefined, false);");
                                }
                            ?>
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

            var date = new Date();
            var dia = date.getDate();
            var mes = date.getMonth() + 1;
            var temporada = date.getFullYear();
            var topBar =
            {
                cerrarSesion : document.getElementById("cerrar-sesion")
            };

            var modal =
            {
                evento : document.getElementById("eventos-dia-modal"),
                cargando : document.getElementById("cargando-modal")
            };

            topBar.cerrarSesion.onclick = function()
            {
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

            document.getElementById("generar-reporte").onclick = function()
            {
                mostrarOverlay();
                var comision = document.getElementById("comisiones").value;
                var subcomision = document.getElementById("subcomisiones").value;
                var inicio  = document.getElementById("fecha-inicio").value;
                var termino = document.getElementById("fecha-termino").value;

                var tipoReporte = parseInt(document.getElementById("tipo-reporte").value);
                $("#fechas-reporte").html("Del "+inicio+" al "+termino);

                switch(tipoReporte)
                {
                    case 1: // RP01
                        $.post( "../php/api.php",
                        {
                            accion: "obtener-rp01",
                            comision: comision,
                            subcomision: subcomision,
                            inicio: inicio,
                            termino: termino
                        }, function( data )
                        {
                            if ( data.status === "OK" )
                            {
                                $("#titulo-reporte").html("RP01");
                                // Obtener todo el resultado de la consulta.
                                var resultado = data.resultado;

                                // Asignar ambos sub-resultados a variables.
                                var fecha_corte = resultado.fecha_corte;
                                var global = resultado.global;

                                // Seleccionar el contenedor del reporte.
                                var contenedorReporte = $("#contenedor-reporte");
                                contenedorReporte.empty();
                                contenedorReporte.append("<h4 class='text-center'>"+
                                    $("#comisiones option:selected").text()+
                                "</h4>");
                                contenedorReporte.append("<h5 class='text-center'>"+
                                    $("#subcomisiones option:selected").text()+
                                "</h5>");

                                // Anexar la tabla para las acciones
                                // con avance completo.
                                contenedorReporte.append("<table id='completos'>"+
                                        "<caption>COMPLETOS</caption>"+
                                        "<thead>"+
                                            "<tr>"+
                                                "<th>#</th>"+
                                                "<th>Número</th>"+
                                                "<th>Acción</th>"+
                                                "<th>Tareas</th>"+
                                                "<th>Monto</th>"+
                                                "<th>Unidad Medida</th>"+
                                                "<th>Avance</th>"+
                                                "<th>Metas</th>"+
                                                "<th>Porcentaje Avance</th>"+
                                            "</tr>"+
                                        "</thead>"+
                                        "<tbody></tbody>"+
                                    "</table>"
                                );

                                // Anexar la tabla para las acciones
                                // con avance vencido o incompleto.
                                contenedorReporte.append("<table id='vencidos'>"+
                                        "<caption>VENCIDOS</caption>"+
                                        "<thead>"+
                                            "<tr>"+
                                                "<th>#</th>"+
                                                "<th>Número</th>"+
                                                "<th>Acción</th>"+
                                                "<th>Tareas</th>"+
                                                "<th>Monto</th>"+
                                                "<th>Unidad Medida</th>"+
                                                "<th>Avance</th>"+
                                                "<th>Metas</th>"+
                                                "<th>Porcentaje Avance</th>"+
                                                "<th>Pendientes</th>"+
                                            "</tr>"+
                                        "</thead>"+
                                        "<tbody></tbody>"+
                                    "</table>"
                                );

                                // Anexar la tabla para los resultados totales
                                // a la fecha de corte elegida.
                                contenedorReporte.append("<table id='resultado-fecha-corte'>"+
                                        "<caption>RESULTADO A LA FECHA DE CORTE</caption>"+
                                        "<thead>"+
                                            "<tr>"+
                                                "<th>Avance</th>"+
                                                "<th>Metas</th>"+
                                                "<th>Porcentaje Avance</th>"+
                                            "</tr>"+
                                        "</thead>"+
                                        "<tbody></tbody>"+
                                    "</table>"
                                );

                                // Anexar la tabla para los resultados totales
                                // al periodo global.
                                contenedorReporte.append("<table id='resultado-global'>"+
                                        "<caption>RESULTADO GLOBAL</caption>"+
                                        "<thead>"+
                                            "<tr>"+
                                                "<th>Avance</th>"+
                                                "<th>Metas</th>"+
                                                "<th>Porcentaje Avance</th>"+
                                            "</tr>"+
                                        "</thead>"+
                                        "<tbody></tbody>"+
                                    "</table>"
                                );

                                var total =
                                    {
                                        fecha_corte:
                                        {
                                            avance: 0,
                                            metas: 0,
                                            porcentaje: 0
                                        },
                                        global:
                                        {
                                            avance: 0,
                                            metas: 0,
                                            porcentaje: 0
                                        }
                                    },
                                    contCompletos = 0,
                                    contVencidos = 0;

                                for (var i = 0; i < fecha_corte.length; i++)
                                {
                                    fecha_corte[i].porcentaje = ( fecha_corte[i].avance / fecha_corte[i].metas ) * 100.00;
                                    fecha_corte[i].porcentaje = fecha_corte[i].porcentaje === 0
                                        || isNaN(fecha_corte[i].porcentaje)
                                            ? "0%"
                                            : fecha_corte[i].porcentaje.toFixed(2) + "%";

                                    // Si el avance y el total de metas es igual
                                    // entonces la acción se ha completado.
                                    if (fecha_corte[i].avance === fecha_corte[i].metas)
                                    {
                                        contCompletos += 1;
                                        contenedorReporte.find("#completos")
                                            .find("tbody")
                                            .append(
                                                "<tr>"+
                                                    "<td class='text-center'>"+contCompletos+"</td>"+
                                                    "<td class='text-center'>"+fecha_corte[i].num+"</td>"+
                                                    "<td>"+fecha_corte[i].accion+"</td>"+
                                                    "<td>"+fecha_corte[i].tareas+"</td>"+
                                                    "<td class='text-center'>"+fecha_corte[i].monto+"</td>"+
                                                    "<td>"+fecha_corte[i].unidad+"</td>"+
                                                    "<td class='text-center'>"+fecha_corte[i].avance+"</td>"+
                                                    "<td class='text-center'>"+fecha_corte[i].metas+"</td>"+
                                                    "<td class='text-center'>"+fecha_corte[i].porcentaje+"</td>"+
                                                "</tr>"
                                            );
                                    }
                                    else
                                    {
                                        // Caso contrario, esta vencida.
                                        contVencidos += 1;
                                        contenedorReporte.find("#vencidos")
                                            .find("tbody")
                                            .append(
                                                "<tr>"+
                                                    "<td class='text-center'>"+contVencidos+"</td>"+
                                                    "<td class='text-center'>"+fecha_corte[i].num+"</td>"+
                                                    "<td>"+fecha_corte[i].accion+"</td>"+
                                                    "<td>"+fecha_corte[i].tareas+"</td>"+
                                                    "<td class='text-center'>"+fecha_corte[i].monto+"</td>"+
                                                    "<td>"+fecha_corte[i].unidad+"</td>"+
                                                    "<td class='text-center'>"+fecha_corte[i].avance+"</td>"+
                                                    "<td class='text-center'>"+fecha_corte[i].metas+"</td>"+
                                                    "<td class='text-center'>"+fecha_corte[i].porcentaje+"</td>"+
                                                    "<td class='pendientes'>"+
                                                        fecha_corte[i].pendientes+
                                                    "</td>"+
                                                "</tr>"
                                            );
                                    }

                                    total.fecha_corte.avance += fecha_corte[i].avance;
                                    total.fecha_corte.metas += fecha_corte[i].metas;
                                }

                                // Remover las tablas que no tuvieron resultados.
                                if (contCompletos === 0)
                                {
                                    contenedorReporte.find("#completos").remove();
                                }

                                if (contVencidos === 0)
                                {
                                    contenedorReporte.find("#vencidos").remove();
                                }

                                total.fecha_corte.porcentaje = ( total.fecha_corte.avance / total.fecha_corte.metas ) * 100.00;
                                total.fecha_corte.porcentaje = total.fecha_corte.porcentaje === 0
                                    || isNaN(total.fecha_corte.porcentaje)
                                        ? "0%"
                                        : total.fecha_corte.porcentaje.toFixed(2) + "%";

                                total.global.avance = global.avance;
                                total.global.metas = global.metas;
                                total.global.porcentaje = ( total.global.avance / total.global.metas ) * 100.00;
                                total.global.porcentaje = total.global.porcentaje === 0
                                    || isNaN(total.global.porcentaje)
                                        ? "0%"
                                        : total.global.porcentaje.toFixed(2) + "%";

                                contenedorReporte.find("#resultado-fecha-corte")
                                    .find("tbody")
                                    .append("<tr>"+
                                        "<td class='text-center'>"+total.fecha_corte.avance+"</td>"+
                                        "<td class='text-center'>"+total.fecha_corte.metas+"</td>"+
                                        "<td class='text-center'>"+total.fecha_corte.porcentaje+"</td>"+
                                    "</tr>");

                                 contenedorReporte.find("#resultado-global")
                                    .find("tbody")
                                    .append("<tr>"+
                                        "<td class='text-center'>"+total.global.avance+"</td>"+
                                        "<td class='text-center'>"+total.global.metas+"</td>"+
                                        "<td class='text-center'>"+total.global.porcentaje+"</td>"+
                                    "</tr>");
                            }
                            else
                            {
                                alert("¡Oh no! Algo sucedió. Por favor,"+
                                    " inténtalo de nuevo.");
                            }

                            esconderOverlay();
                        }, "json");
                    break;

                    case 2: // RP02
                        $.post( "../php/api.php",
                        {
                            accion: "obtener-rp02",
                            comision: comision,
                            subcomision: subcomision,
                            inicio: inicio,
                            termino: termino
                        }, function( data )
                        {
                            if ( data.status === "OK" )
                            {
                                $("#titulo-reporte").html("Información de Actividades por Comisión");
                                // Obtener todo el resultado de la consulta.
                                var resultado = data.resultado;

                                // Asignar ambos sub-resultados a variables.
                                var fecha_corte = resultado.fecha_corte;
                                var global = resultado.global;

                                // Seleccionar el contenedor del reporte.
                                var contenedorReporte = $("#contenedor-reporte");
                                contenedorReporte.empty();
                                contenedorReporte.append("<h4 class='text-center'>"+
                                    $("#comisiones option:selected").text()+
                                "</h4>");
                                contenedorReporte.append("<h5 class='text-center'>"+
                                    $("#subcomisiones option:selected").text()+
                                "</h5>");

                                // Anexar la tabla para las acciones
                                // con avance completo.
                                contenedorReporte.append("<table id='completos'>"+
                                        "<caption>COMPLETOS</caption>"+
                                        "<thead>"+
                                            "<tr>"+
                                                "<th width='5%'>#</th>"+
                                                "<th width='10%'>Num. de Actividad</th>"+
                                                "<th width='15%'>Unidad Medida</th>"+
                                                "<th width='25%'>Acción</th>"+
                                                "<th width='35%'>Tareas</th>"+
                                                "<th width='10%'>Porcentaje Avance</th>"+
                                            "</tr>"+
                                        "</thead>"+
                                        "<tbody></tbody>"+
                                    "</table>"
                                );

                                // Anexar la tabla para las acciones
                                // con avance vencido o incompleto.
                                contenedorReporte.append("<table id='vencidos'>"+
                                        "<caption>VENCIDOS</caption>"+
                                        "<thead>"+
                                            "<tr>"+
                                                "<th width='5%'>#</th>"+
                                                "<th width='10%'>Num. de Actividad</th>"+
                                                "<th width='15%'>Unidad Medida</th>"+
                                                "<th width='25%'>Acción</th>"+
                                                "<th width='35%'>Tareas</th>"+
                                                "<th width='10%'>Porcentaje Avance</th>"+
                                            "</tr>"+
                                        "</thead>"+
                                        "<tbody></tbody>"+
                                    "</table>"
                                );

                                // Anexar la tabla para los resultados totales
                                // a la fecha de corte elegida.
                                contenedorReporte.append("<table id='resultado-fecha-corte'>"+
                                        "<caption>RESULTADO A LA FECHA DE CORTE</caption>"+
                                        "<thead>"+
                                            "<tr>"+
                                                "<th>Actividades Cumplidas</th>"+
                                                "<th>Actividades Pendientes</th>"+
                                                "<th>Total de Actividades</th>"+
                                                "<th>Porcentaje de Avance</th>"+
                                            "</tr>"+
                                        "</thead>"+
                                        "<tbody></tbody>"+
                                    "</table>"
                                );

                                // Anexar el contenedor para la grafica de pastel
                                // para los resultados a la fecha de corte.
                                contenedorReporte.append("<div id='grafica-resultado-fecha-corte'"+
                                    " data-graph></div>");

                                // Anexar la tabla para los resultados totales
                                // al periodo global.
                                contenedorReporte.append("<table id='resultado-global'>"+
                                        "<caption>RESULTADO GLOBAL</caption>"+
                                        "<thead>"+
                                            "<tr>"+
                                                "<th>Actividades Cumplidas</th>"+
                                                "<th>Actividades Pendientes</th>"+
                                                "<th>Total de Actividades</th>"+
                                                "<th>Porcentaje de Avance</th>"+
                                            "</tr>"+
                                        "</thead>"+
                                        "<tbody></tbody>"+
                                    "</table>"
                                );

                                // Anexar el contenedor para la grafica de pastel
                                // para los resultados globales.
                                contenedorReporte.append("<div id='grafica-resultado-global'"+
                                    " data-graph></div>");

                                var total =
                                    {
                                        fecha_corte:
                                        {
                                            avance: 0,
                                            metas: 0,
                                            porcentaje: 0
                                        },
                                        global:
                                        {
                                            avance: 0,
                                            metas: 0,
                                            porcentaje: 0
                                        }
                                    },
                                    contCompletos = 0,
                                    contVencidos = 0;

                                for (var i = 0; i < fecha_corte.length; i++)
                                {
                                    fecha_corte[i].porcentaje = ( fecha_corte[i].avance / fecha_corte[i].metas ) * 100.00;
                                    fecha_corte[i].porcentaje = fecha_corte[i].porcentaje === 0
                                        || isNaN(fecha_corte[i].porcentaje)
                                            ? "0%"
                                            : fecha_corte[i].porcentaje.toFixed(2) + "%";

                                    // Si el avance y el total de metas es igual
                                    // entonces la acción se ha completado.
                                    if (fecha_corte[i].avance === fecha_corte[i].metas)
                                    {
                                        contCompletos += 1;
                                        contenedorReporte.find("#completos")
                                            .find("tbody")
                                            .append(
                                                "<tr>"+
                                                    "<td class='text-center'>"+contCompletos+"</td>"+
                                                    "<td class='text-center'>"+fecha_corte[i].num+"</td>"+
                                                    "<td>"+fecha_corte[i].unidad+"</td>"+
                                                    "<td>"+fecha_corte[i].accion+"</td>"+
                                                    "<td>"+fecha_corte[i].tareas+"</td>"+
                                                    "<td class='text-center'>"+fecha_corte[i].porcentaje+"</td>"+
                                                "</tr>"
                                            );
                                    }
                                    else
                                    {
                                        // Caso contrario, esta vencida.
                                        contVencidos += 1;
                                        contenedorReporte.find("#vencidos")
                                            .find("tbody")
                                            .append(
                                                "<tr>"+
                                                    "<td class='text-center'>"+contVencidos+"</td>"+
                                                    "<td class='text-center'>"+fecha_corte[i].num+"</td>"+
                                                    "<td>"+fecha_corte[i].unidad+"</td>"+
                                                    "<td>"+fecha_corte[i].accion+"</td>"+
                                                    "<td>"+fecha_corte[i].tareas+"</td>"+
                                                    "<td class='text-center'>"+fecha_corte[i].porcentaje+"</td>"+
                                                "</tr>"
                                            );
                                    }

                                    total.fecha_corte.avance += fecha_corte[i].avance;
                                    total.fecha_corte.metas += fecha_corte[i].metas;
                                }

                                // Remover las tablas que no tuvieron resultados.
                                if (contCompletos === 0)
                                {
                                    contenedorReporte.find("#completos").remove();
                                }

                                if (contVencidos === 0)
                                {
                                    contenedorReporte.find("#vencidos").remove();
                                }

                                total.fecha_corte.porcentaje = ( total.fecha_corte.avance / total.fecha_corte.metas ) * 100.00;
                                total.fecha_corte.porcentaje = total.fecha_corte.porcentaje === 0
                                    || isNaN(total.fecha_corte.porcentaje)
                                        ? "0%"
                                        : total.fecha_corte.porcentaje.toFixed(2) + "%";

                                total.global.avance = global.avance;
                                total.global.metas = global.metas;
                                total.global.porcentaje = ( total.global.avance / total.global.metas ) * 100.00;
                                total.global.porcentaje = total.global.porcentaje === 0
                                    || isNaN(total.global.porcentaje)
                                        ? "0%"
                                        : total.global.porcentaje.toFixed(2) + "%";

                                contenedorReporte.find("#resultado-fecha-corte")
                                    .find("tbody")
                                    .append("<tr>"+
                                        "<td class='text-center'>"+total.fecha_corte.avance+"</td>"+
                                        "<td class='text-center'>"+(total.fecha_corte.metas - total.fecha_corte.avance)+"</td>"+
                                        "<td class='text-center'>"+total.fecha_corte.metas+"</td>"+
                                        "<td class='text-center'>"+total.fecha_corte.porcentaje+"</td>"+
                                    "</tr>");

                                // Resultado a Fecha de Corte en tabla y grafica.
                                var dataProvider = [
                                    {"estatus": "CUMPLIDAS", "metas": total.fecha_corte.avance},
                                    {"estatus": "PENDIENTES", "metas": total.fecha_corte.metas-total.fecha_corte.avance},
                                ];

                                AmCharts.makeChart( "grafica-resultado-fecha-corte",
                                {
                                    "type": "pie",
                                    "theme": "light",
                                    "fontFamily": "inherit",
                                    "titles": [
                                        {
                                            "text": "",
                                            "size": 24
                                        }
                                    ],
                                    "colors": ["#009038","#DA241B"],
                                    "legend": {
                                        "position": "top",
                                        "useGraphSettings": true,
                                        "markerSize": 10,
                                    },
                                    "dataProvider": dataProvider,
                                    "valueField": "metas",
                                    "titleField": "estatus",
                                    "balloon":{
                                        "fixedPosition":true
                                    },
                                    "export": {
                                        "enabled": true,
                                        "menu": [
                                        {
                                            "format": "PNG",
                                            "label": "Descargar"
                                        },
                                        {
                                            "format": "PRINT",
                                            "label": "Imprimir"
                                        }
                                    ],
                                        "fileName": "AVANCE GENERAL"
                                    }
                                });

                                // Resultado Global en tabla y grafica.
                                contenedorReporte.find("#resultado-global")
                                    .find("tbody")
                                    .append("<tr>"+
                                        "<td class='text-center'>"+total.global.avance+"</td>"+
                                        "<td class='text-center'>"+(total.global.metas - total.global.avance)+"</td>"+
                                        "<td class='text-center'>"+total.global.metas+"</td>"+
                                        "<td class='text-center'>"+total.global.porcentaje+"</td>"+
                                    "</tr>");

                                dataProvider = [
                                    {"estatus": "CUMPLIDAS", "metas": total.fecha_corte.avance},
                                    {"estatus": "PENDIENTES", "metas": total.global.metas-total.fecha_corte.avance},
                                ];

                                AmCharts.makeChart( "grafica-resultado-global",
                                {
                                    "type": "pie",
                                    "theme": "light",
                                    "fontFamily": "inherit",
                                    "titles": [
                                        {
                                            "text": "",
                                            "size": 24
                                        }
                                    ],
                                    "colors": ["#009038","#DA241B"],
                                    "legend": {
                                        "position": "top",
                                        "useGraphSettings": true,
                                        "markerSize": 10,
                                    },
                                    "dataProvider": dataProvider,
                                    "valueField": "metas",
                                    "titleField": "estatus",
                                    "balloon":{
                                        "fixedPosition":true
                                    },
                                    "export": {
                                        "enabled": true,
                                        "menu": [
                                        {
                                            "format": "PNG",
                                            "label": "Descargar"
                                        },
                                        {
                                            "format": "PRINT",
                                            "label": "Imprimir"
                                        }
                                    ],
                                        "fileName": "AVANCE GENERAL"
                                    }
                                });
                            }
                            else
                            {
                                alert("¡Oh no! Algo sucedió. Por favor,"+
                                    " inténtalo de nuevo.");
                            }

                            esconderOverlay();
                        }, "json");
                    break;
                }
            }

            document.getElementById("imprimir-reporte").onclick = function()
            {
                window.print();
            };

            // Implementar Mask Plugin en los siguientes inputs.
            $(".fechas").mask("00/00/0000", { clearIfNotMatch: true });

            // Valor inicial para fecha termino.
            $("#fecha-termino").val(("00" + dia).slice(-2)+
                "/"+("00" + mes).slice(-2)+"/"+temporada);

            // RP02 => Presupuesto
            /*$.post( "../php/api.php",
            {
                accion: "obtener-presupuesto-por-comision"
            }, function( data )
            {
                if ( data.status === "OK" )
                {
                    var resultado = data.resultado;
                    var total = 0;

                    for (var i = 0; i < resultado.length; i++)
                    {
                        total += resultado[i].presupuesto;
                        var presupuesto = '$' + resultado[i].presupuesto.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");

                        $("#contenedor-presupuesto-comision").append("<div class='large-4 medium-4 small-6 columns end'>"+
                            "<label>"+resultado[i].comision+"</label>"+
                            "<h4>"+presupuesto+"</h4>"+
                        "</div>");
                    };

                    var totalEnDinero = '$' + total.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                    $("#contenedor-presupuesto-comision").append("<hr><div class='large-12 medium-12 small-12 columns text-center'>"+
                            "<label><strong>TOTAL</strong></label>"+
                            "<h4>"+totalEnDinero+"</h4>"+
                        "</div>");
                };
            }, "json");*/
        };
    </script>
</body>
</html>