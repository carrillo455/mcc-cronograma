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

    if (!in_array("0001", $claves) && !in_array("1005", $claves)) {
        header("Location: index.php");
        exit;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Cronograma MCC | Gráficas</title>
        <link rel="shortcut icon" href="../favicon.ico">
        <link rel="stylesheet" href="../css/normalize.css">
        <link rel="stylesheet" href="../css/foundation.min.css" />
        <link rel="stylesheet" href="../css/foundation.calendar.css">
        <link rel="stylesheet" href="../css/dataTables.foundation.css">
        <link rel="stylesheet" href="amcharts/plugins/export/export.css">
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
                padding: 5px;
                margin-bottom: 5px;
                font-size: 1.25em;
                background-color: #979797;
                color: #FFF;
            }

            table caption span
            {
                font-size: 0.90em;
            }

            table thead th,
            table tbody td,
            table tfoot th
            {
                border: 1px solid #ddd;
                font-size: 0.75rem !important;
                line-height: 0.80rem !important;
            }

            table thead th,
            table tfoot th
            {
                text-transform: uppercase;
            }

            [data-graph]
            {
                width:100%;height:500px;
            }

            .banner
            {
                width: 100%;
                height: 200px;
                opacity: 0.65;
                margin-bottom: 10px;
                border-radius: 5px;
            }
        </style>
        <script src="../js/vendor/modernizr.js"></script>
</head>
<body>
    <div class="overlay"><h3>Cargando...</h3></div>
    <nav id="top-bar-principal" class="top-bar" data-topbar>
        <ul class="title-area">
            <li class="name">
                <h1><a href="#">Cronograma MCC</a></h1>
            </li>
            <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
        </ul>

        <section class="top-bar-section">
            <ul class="right">
                <li><a href="index.php">Inicio</a></li>
                <li class="active"><a href="graficas.php">Gráficas</a></li>
                <li><a href="reportes.php">Reportes</a></li>
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
        <div class="row">
            <div class="large-12 columns">
                <h2>Cronograma MCC | Gráficas</h2>
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
                <label for="generar-grafica">Da clic aquí para generar la gráfica.</label>
                <a id="generar-grafica" href="#" class="small button expand">Generar</a>
            </div>

            <ul class="tabs small-block-grid-3" data-tab>
                <li class="tab-title active"><a href="#panel1">Fecha de Corte</a></li>
                <li class="tab-title"><a href="#panel2">Global por Comisión</a></li>
                <li class="tab-title"><a href="#panel3">Global</a></li>
            </ul>

            <div class="tabs-content">
                <div class="content active" id="panel1">
                    <p>Avance contra la fecha de corte por comisión.</p>
                    <div class="row">
                        <div class="large-12 columns">
                            <img class="banner" src="../images/ultreya-banner.jpg" alt="Banner de la Ultreya 2016">
                        </div>

                        <div class="large-12 columns">
                            <table id="tabla-avance-por-comision-fecha-corte">
                                <caption></caption>
                                <thead>
                                    <th>Concepto</th>
                                    <th>Avance Real</th>
                                    <th>Meta Fecha Corte</th>
                                    <th>Avance VS Fecha Corte</th>
                                </thead>

                                <tbody></tbody>

                                <tfoot></tfoot>
                            </table>
                        </div>

                        <div class="large-12 columns">
                            <div id="grafica-avance-por-comision-fecha-corte" data-graph></div>
                        </div>
                    </div>
                </div>

                <div class="content" id="panel2">
                    <p>Avance global por comisión.</p>
                    <div class="row">
                        <div class="large-12 columns">
                            <img class="banner" src="../images/ultreya-banner.jpg" alt="Banner de la Ultreya 2016">
                        </div>

                        <div class="large-12 columns">
                            <table id="tabla-avance-por-comision-global">
                                <caption></caption>
                                <thead>
                                    <th>Concepto</th>
                                    <th>Avance Real</th>
                                    <th>Meta Fecha Global</th>
                                    <th>Avance VS Global Ultreya</th>
                                </thead>

                                <tbody></tbody>

                                <tfoot></tfoot>
                            </table>
                        </div>

                        <div class="large-12 columns">
                            <div id="grafica-avance-por-comision-global" data-graph></div>
                        </div>
                    </div>
                </div>

                <div class="content" id="panel3">
                    <p>Avance global.</p>
                    <div class="row">
                        <div class="large-12 columns">
                            <img class="banner" src="../images/ultreya-banner.jpg" alt="Banner de la Ultreya 2016">
                        </div>

                        <div class="large-12 columns">
                            <table id="tabla-avance-global">
                                <caption></caption>
                                <thead>
                                    <th>Concepto</th>
                                    <th>Avance Real</th>
                                    <th>Meta Fecha Corte</th>
                                    <th>Avance VS Fecha Corte</th>
                                    <th>Meta Fecha Final</th>
                                    <th>Avance VS Global Ultreya</th>
                                </thead>

                                <tbody></tbody>

                                <tfoot></tfoot>
                            </table>
                        </div>

                        <div class="large-12 columns">
                            <div id="grafica-avance-global" data-graph></div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </header>

    <div class="row">
        <div class="large-12 columns">
            <hr>
        </div>
    </div>

    <footer class="row">
        <div class="columns">
            <br>
        </div>
    </footer>

    <div id="cargando-modal" class="tiny reveal-modal" data-reveal aria-hidden="true" role="dialog">
        <p class="text-center">Cargando... <img src="../css/images/cargando.gif"></p>
    </div>

    <script src="../js/vendor/jquery.js"></script>
    <script src="../js/vendor/jquery.dataTables.min.js"></script>
    <script src="../js/vendor/jquery.mask.min.js"></script>
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
    <script src="amcharts/serial.js"></script>
    <script src="amcharts/pie.js"></script>
    <script src="amcharts/themes/light.js"></script>
    <script src="amcharts/plugins/export/export.js"></script>

    <script>
        window.onload = function()
        {
            /*
            *
            * DECLARAR VARIABLES
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

            var date = new Date();
            var dia = date.getDate();
            var mes = date.getMonth() + 1;
            var temporada = date.getFullYear();
            var modal =
            {
                evento : document.getElementById("eventos-dia-modal"),
                cargando : document.getElementById("cargando-modal")
            };

            document.getElementById("cerrar-sesion").onclick = function()
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

            document.getElementById("generar-grafica").onclick = function()
            {
                mostrarOverlay();
                var fechaInicio  = document.getElementById("fecha-inicio").value;
                var fechaTermino = document.getElementById("fecha-termino").value;

                var activeTab = $(".tabs-content > .active");
                var graphId = activeTab.find("[data-graph]").get(0).id;

                switch(graphId) {
                    case "grafica-avance-por-comision-fecha-corte":
                        // Gráfica por Comisión - Fecha de Corte
                        $.post( "../php/api.php",
                        {
                            accion: "obtener-avance-por-comision-fecha-corte",
                            inicio: fechaInicio,
                            termino: fechaTermino
                        }, function( data )
                        {
                                if ( data.status === "OK" )
                                {
                                    var dataProvider = data.resultado;
                                    var total = {
                                        avance: 0,
                                        metas: 0,
                                        porcentaje: 0
                                    };

                                    // Actualizar contenido y/o limpiar.
                                    $("#tabla-avance-por-comision-fecha-corte caption").html("AVANCE CONTRA FECHA DE CORTE<br>"+
                                        "<span>Del "+$("#fecha-inicio").val()+" al "+$("#fecha-termino").val()+"</span>");

                                    $("#tabla-avance-por-comision-fecha-corte tbody").empty();
                                    $("#tabla-avance-por-comision-fecha-corte tfoot").empty();

                                    for (var i = 0; i < dataProvider.length; i++)
                                    {
                                        dataProvider[i].avance = parseInt(dataProvider[i].avance);
                                        dataProvider[i].metas = parseInt(dataProvider[i].metas);
                                        dataProvider[i].porcentaje = ( dataProvider[i].avance / dataProvider[i].metas ) * 100.00;
                                        dataProvider[i].porcentaje = dataProvider[i].porcentaje === 0 || isNaN(dataProvider[i].porcentaje)
                                            ? "0%"
                                            : dataProvider[i].porcentaje.toFixed(2) + "%";

                                        var newEntry = "<tr>"+
                                            "<td>"+dataProvider[i].comision+"</td>"+
                                            "<td class='text-right'>"+dataProvider[i].avance+"</td>"+
                                            "<td class='text-right'>"+dataProvider[i].metas+"</td>"+
                                            "<td class='text-right'>"+dataProvider[i].porcentaje+"</td>"+
                                        +"</tr>";

                                        total.avance += dataProvider[i].avance;
                                        total.metas += dataProvider[i].metas;

                                        $("#tabla-avance-por-comision-fecha-corte tbody").append(newEntry);
                                    }

                                    total.porcentaje = ( total.avance / total.metas ) * 100.00;
                                    total.porcentaje = total.porcentaje === 0 || isNaN(total.porcentaje)
                                        ? "0%"
                                        : total.porcentaje.toFixed(2) + "%";

                                    var lastEntry = "<tr>"+
                                        "<th>TOTAL</th>"+
                                        "<th class='text-right'>"+total.avance+"</th>"+
                                        "<th class='text-right'>"+total.metas+"</th>"+
                                        "<th class='text-right'>"+total.porcentaje+"</th>"+
                                    "</tr>";

                                    $("#tabla-avance-por-comision-fecha-corte tfoot").append(lastEntry);

                                    var chart = AmCharts.makeChart( "grafica-avance-por-comision-fecha-corte",
                                    {
                                        "type": "serial",
                                        "theme": "light",
                                        "fontFamily": "inherit",
                                            "titles": [
                                            {
                                                "text": "",
                                                "size": 24
                                            }
                                        ],
                                        "colors": ["#009038", "#29166F", "#DA241B"],
                                        "legend": {
                                            "position": "top",
                                            "useGraphSettings": true,
                                            "markerSize": 10,
                                        },
                                        "dataProvider": dataProvider,
                                        "valueAxes": [ {
                                            "gridColor": "#29166F",
                                            "gridAlpha": 0.2,
                                            "dashLength": 0,
                                            "title": "Metas"
                                        } ],
                                        "gridAboveGraphs": true,
                                        "startDuration": 1,
                                        "graphs": [ {
                                            "balloonText": "[[category]]: <b>[[value]]</b>",
                                            "fillAlphas": 0.8,
                                            "lineAlpha": 0.2,
                                            // "type": "column",
                                            "labelText": "[[value]]",
                                            "title": "Avance Real",
                                            "valueField": "avance"
                                        },
                                        {
                                            "balloonText": "[[category]]: <b>[[value]]</b>",
                                            "fillAlphas": 0.8,
                                            "lineAlpha": 0.2,
                                            // "type": "column",
                                            "labelText": "[[value]]",
                                            "title": "Metas Fecha Corte",
                                            "valueField": "metas"
                                        } ],
                                        "chartCursor": {
                                            "categoryBalloonEnabled": false,
                                            "cursorAlpha": 0,
                                            "zoomable": false
                                        },
                                        "categoryField": "comision",
                                        "categoryAxis": {
                                            "gridPosition": "start",
                                            "gridAlpha": 0,
                                            "tickPosition": "start",
                                            "tickLength": 20,
                                            // "labelRotation": 45,
                                            "autoWrap": true,
                                            "title": "Comisiones"
                                        },
                                        "rotate": false,
                                        "centerLabels": true,
                                        "fontSize": 9,
                                        "max": 100,
                                        "recalculateToPercents": true,
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
                                            "fileName": "AVANCE POR COMISION CONTRA LA FECHA DE CORTE"
                                        }
                                    });

                                    $(".amcharts-graph-stroke").attr("stroke-width", 10);
                                }

                                esconderOverlay();
                        }, "json");
                    break;

                    case "grafica-avance-por-comision-global":
                        // Gráfica por Comisión - Global
                        $.post( "../php/api.php",
                        {
                            accion: "obtener-avance-por-comision-global"
                        }, function( data )
                        {
                                if ( data.status === "OK" )
                                {
                                    var dataProvider = data.resultado;
                                    var total = {
                                        avance: 0,
                                        metas: 0,
                                        porcentaje: 0
                                    };

                                    // Actualizar contenido y/o limpiar.
                                    $("#tabla-avance-por-comision-global caption").html("AVANCE CONTRA LA FECHA FINAL");

                                    $("#tabla-avance-por-comision-global tbody").empty();
                                    $("#tabla-avance-por-comision-global tfoot").empty();

                                    for (var i = 0; i < dataProvider.length; i++)
                                    {
                                        dataProvider[i].avance = parseInt(dataProvider[i].avance);
                                        dataProvider[i].metas = parseInt(dataProvider[i].metas);
                                        dataProvider[i].porcentaje = ( dataProvider[i].avance / dataProvider[i].metas ) * 100.00;
                                        dataProvider[i].porcentaje = dataProvider[i].porcentaje === 0 || isNaN(dataProvider[i].porcentaje)
                                            ? "0%"
                                            : dataProvider[i].porcentaje.toFixed(2) + "%";

                                        var newEntry = "<tr>"+
                                            "<td>"+dataProvider[i].comision+"</td>"+
                                            "<td class='text-right'>"+dataProvider[i].avance+"</td>"+
                                            "<td class='text-right'>"+dataProvider[i].metas+"</td>"+
                                            "<td class='text-right'>"+dataProvider[i].porcentaje+"</td>"+
                                        +"</tr>";

                                        total.avance += dataProvider[i].avance;
                                        total.metas += dataProvider[i].metas;

                                        $("#tabla-avance-por-comision-global tbody").append(newEntry);
                                    }

                                    total.porcentaje = ( total.avance / total.metas ) * 100.00;
                                    total.porcentaje = total.porcentaje === 0 || isNaN(total.porcentaje)
                                        ? "0%"
                                        : total.porcentaje.toFixed(2) + "%";

                                    var lastEntry = "<tr>"+
                                        "<th>TOTAL</th>"+
                                        "<th class='text-right'>"+total.avance+"</th>"+
                                        "<th class='text-right'>"+total.metas+"</th>"+
                                        "<th class='text-right'>"+total.porcentaje+"</th>"+
                                    "</tr>";

                                    $("#tabla-avance-por-comision-global tfoot").append(lastEntry);

                                    var chart = AmCharts.makeChart( "grafica-avance-por-comision-global",
                                    {
                                        "type": "serial",
                                        "theme": "light",
                                        "fontFamily": "inherit",
                                            "titles": [
                                            {
                                                "text": "",
                                                "size": 24
                                            }
                                        ],
                                        "colors": ["#009038", "#29166F", "#DA241B"],
                                        "legend": {
                                            "position": "top",
                                            "useGraphSettings": true,
                                            "markerSize": 10,
                                        },
                                        "dataProvider": dataProvider,
                                        "valueAxes": [ {
                                            "gridColor": "#29166F",
                                            "gridAlpha": 0.2,
                                            "dashLength": 0,
                                            "title": "Metas"
                                        } ],
                                        "gridAboveGraphs": true,
                                        "startDuration": 1,
                                        "graphs": [ {
                                            "balloonText": "[[category]]: <b>[[value]]</b>",
                                            "fillAlphas": 0.8,
                                            "lineAlpha": 0.2,
                                            // "type": "column",
                                            "labelText": "[[value]]",
                                            "title": "Avance Real",
                                            "valueField": "avance"
                                        },
                                        {
                                            "balloonText": "[[category]]: <b>[[value]]</b>",
                                            "fillAlphas": 0.8,
                                            "lineAlpha": 0.2,
                                            // "type": "column",
                                            "labelText": "[[value]]",
                                            "title": "Metas Fecha Corte",
                                            "valueField": "metas"
                                        } ],
                                        "chartCursor": {
                                            "categoryBalloonEnabled": false,
                                            "cursorAlpha": 0,
                                            "zoomable": false
                                        },
                                        "categoryField": "comision",
                                        "categoryAxis": {
                                            "gridPosition": "start",
                                            "gridAlpha": 0,
                                            "tickPosition": "start",
                                            "tickLength": 20,
                                            // "labelRotation": 45,
                                            "autoWrap": true,
                                            "title": "Comisiones"
                                        },
                                        "rotate": false,
                                        "centerLabels": true,
                                        "fontSize": 9,
                                        "max": 100,
                                        "recalculateToPercents": true,
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
                                            "fileName": "AVANCE POR COMISION CONTRA LA FECHA FINAL"
                                        }
                                    });

                                    $(".amcharts-graph-stroke").attr("stroke-width", 10);
                                }

                                esconderOverlay();
                        }, "json");
                    break;

                    case "grafica-avance-global":
                        // Grafica - Global
                        $.post( "../php/api.php",
                        {
                            accion: "obtener-avance-global",
                            inicio: fechaInicio,
                            termino: fechaTermino
                        }, function( data )
                        {
                                if ( data.status === "OK" )
                                {
                                    var resultado = data.resultado;
                                    var avanceFechaCorte = resultado.fecha_corte;
                                    var avanceGlobal = resultado.global;
                                    var total = {
                                        fecha_corte: {
                                            avance: 0,
                                            metas: 0,
                                            porcentaje: 0
                                        },
                                        global: {
                                            avance: 0,
                                            metas: 0,
                                            porcentaje: 0
                                        }
                                    };

                                    // Actualizar contenido y/o limpiar.
                                    $("#tabla-avance-global caption").html("AVANCE GENERAL<br>"+
                                        "<span>Del "+$("#fecha-inicio").val()+" al "+$("#fecha-termino").val()+"</span>");

                                    $("#tabla-avance-global tbody").empty();
                                    $("#tabla-avance-global tfoot").empty();

                                    for (var i = 0; i < avanceFechaCorte.length; i++)
                                    {
                                        // Fecha de Corte
                                        avanceFechaCorte[i].avance = parseInt(avanceFechaCorte[i].avance);
                                        avanceFechaCorte[i].metas = parseInt(avanceFechaCorte[i].metas);
                                        avanceFechaCorte[i].porcentaje = ( avanceFechaCorte[i].avance / avanceFechaCorte[i].metas ) * 100.00;
                                        avanceFechaCorte[i].porcentaje = avanceFechaCorte[i].porcentaje === 0 || isNaN(avanceFechaCorte[i].porcentaje)
                                            ? "0%"
                                            : avanceFechaCorte[i].porcentaje.toFixed(2) + "%";

                                        // Global
                                        avanceGlobal[i].metas = parseInt(avanceGlobal[i].metas);
                                        avanceGlobal[i].porcentaje = ( avanceFechaCorte[i].avance / avanceGlobal[i].metas ) * 100.00;
                                        avanceGlobal[i].porcentaje = avanceGlobal[i].porcentaje === 0 || isNaN(avanceGlobal[i].porcentaje)
                                            ? "0%"
                                            : avanceGlobal[i].porcentaje.toFixed(2) + "%";

                                        var newEntry = "<tr>"+
                                            "<td>"+avanceFechaCorte[i].comision+"</td>"+
                                            "<td class='text-right'>"+avanceFechaCorte[i].avance+"</td>"+
                                            "<td class='text-right'>"+avanceFechaCorte[i].metas+"</td>"+
                                            "<td class='text-right'>"+avanceFechaCorte[i].porcentaje+"</td>"+
                                            "<td class='text-right'>"+avanceGlobal[i].metas+"</td>"+
                                            "<td class='text-right'>"+avanceGlobal[i].porcentaje+"</td>"+
                                        +"</tr>";

                                        total.fecha_corte.avance += avanceFechaCorte[i].avance;
                                        total.fecha_corte.metas += avanceFechaCorte[i].metas;
                                        total.global.metas += avanceFechaCorte[i].metas;

                                        $("#tabla-avance-global tbody").append(newEntry);
                                    }

                                    // Total Fecha de Corte
                                    total.fecha_corte.porcentaje = ( total.fecha_corte.avance / total.fecha_corte.metas ) * 100.00;
                                    total.fecha_corte.porcentaje = total.fecha_corte.porcentaje === 0 || isNaN(total.fecha_corte.porcentaje)
                                        ? "0%"
                                        : total.fecha_corte.porcentaje.toFixed(2) + "%";

                                    // Total Global
                                    total.global.porcentaje = ( total.fecha_corte.avance / total.global.metas ) * 100.00;
                                    total.global.porcentaje = total.global.porcentaje === 0 || isNaN(total.global.porcentaje)
                                        ? "0%"
                                        : total.global.porcentaje.toFixed(2) + "%";

                                    var lastEntry = "<tr>"+
                                        "<th>TOTAL</th>"+
                                        "<th class='text-right'>"+total.fecha_corte.avance+"</th>"+
                                        "<th class='text-right'>"+total.fecha_corte.metas+"</th>"+
                                        "<th class='text-right'>"+total.fecha_corte.porcentaje+"</th>"+
                                        "<th class='text-right'>"+total.global.metas+"</th>"+
                                        "<th class='text-right'>"+total.global.porcentaje+"</th>"+
                                    "</tr>";

                                    $("#tabla-avance-global tfoot").append(lastEntry);

                                    var dataProvider = [
                                        {"estatus": "AVANCE REAL", "metas": total.fecha_corte.avance},
                                        {"estatus": "PENDIENTE", "metas": total.global.metas-total.fecha_corte.avance},
                                    ];

                                    AmCharts.makeChart( "grafica-avance-global",
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

                                esconderOverlay();
                        }, "json");
                    break;
                }

            }

            // Implementar Mask Plugin en los siguientes inputs.
            $(".fechas").mask("00/00/0000", { clearIfNotMatch: true });

            // cargarGraficas.click();

            //http://jsfiddle.net/manishma/AVZJh/light/
        };
    </script>
</body>
</html>