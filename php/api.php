<?php
    date_default_timezone_set('America/Mexico_City');
    session_name("cronograma_mcc_2015");
    session_start();

    // CONEXION A LA BASE DE DATOS
    $pdo = new PDO("mysql:host=mysql.mccdiocesistampico.org;dbname=cronograma;charset=utf8", "mcctampico", "jesusconnosotros");

    // if ( !isset($_SESSION["usuario"]) )
    // {
    //  echo json_encode(array("accion" => "relogear"));
    //  exit;
    // }

    $accion = $_POST["accion"];

    switch ($accion)
    {
        case "iniciar-sesion":
            $usuario = $_POST["usuario"];
            $password = $_POST["password"];

            $query = $pdo->prepare("SELECT id_usuario AS id, u.id_nivel_usuario AS id_nivel,
                nu.nombre AS nivel, u.id_responsable,
                u.bloqueado AS u_bloqueado, nu.bloqueado AS nu_bloqueado
                FROM usuarios u
                INNER JOIN niveles_usuarios nu ON nu.id_nivel_usuario = u.id_nivel_usuario
                WHERE usuario = :usuario AND password = :password AND u.borrado = 0 AND nu.borrado = 0");
            $query->execute(array("usuario" => $usuario, "password" => $password));
            $info_usuario = $query->fetch(PDO::FETCH_ASSOC);

            if ( !$info_usuario )
            {
                header("Location: ../index.php?e=1");
                exit;
            }
            else if ( $info_usuario["u_bloqueado"] === "1" || $info_usuario["nu_bloqueado"] === "1" )
            {
                header("Location: ../index.php?e=2");
                exit;
            }

            $id_usuario = $info_usuario["id"];
            $id_nivel_usuario = $info_usuario["id_nivel"];
            $nivel = $info_usuario["nivel"];
            $id_responsable = $info_usuario["id_responsable"];

            // COMISIONES
            $query = $pdo->query("SELECT DISTINCT id_comision AS comision
                FROM responsables_comisiones
                WHERE id_responsable = $id_responsable");
            $responsables_comisiones = $query->fetchAll(PDO::FETCH_ASSOC);

            $comisiones = array();
            for ($i = 0; $i < count($responsables_comisiones); $i++)
            {
                array_push($comisiones, $responsables_comisiones[$i]["comision"]);
            }

            // SUBCOMISIONES
            $query = $pdo->query("SELECT DISTINCT id_subcomision AS subcomision
                FROM responsables_comisiones
                WHERE id_responsable = $id_responsable");
            $responsables_subcomisiones = $query->fetchAll(PDO::FETCH_ASSOC);

            $subcomisiones = array();
            for ($i = 0; $i < count($responsables_subcomisiones); $i++)
            {
                array_push($subcomisiones, $responsables_subcomisiones[$i]["subcomision"]);
            }

            // CLAVES
            $query = $pdo->query("SELECT au.clave AS clave FROM permisos_usuarios pu
                INNER JOIN accesos_usuarios au ON au.id_acceso_usuario = pu.id_acceso_usuario
                WHERE pu.id_nivel_usuario = $id_nivel_usuario AND pu.borrado = 0");
            $permisos_usuarios = $query->fetchAll(PDO::FETCH_ASSOC);

            $claves = array();
            for ($i = 0; $i < count($permisos_usuarios); $i++)
            {
                array_push($claves, $permisos_usuarios[$i]["clave"]);
            }

            // session_name("agenda_sia_2015");
            // session_start();
            $_SESSION["usuario"]["id"] = $id_usuario;
            $_SESSION["usuario"]["nombre"] = $usuario;
            $_SESSION["usuario"]["nivel"] = $nivel;
            $_SESSION["usuario"]["claves"] = $claves;
            $_SESSION["usuario"]["comisiones"] = $comisiones;
            $_SESSION["usuario"]["subcomisiones"] = $subcomisiones;

            header("Location: ../web/");
            exit;
        break;

        case "cerrar-sesion":
            unset($_SESSION['usuario']);
            session_unset();
            session_destroy();
            session_write_close();

            echo json_encode(array("status" => "OK"));
        break;

        case "checar-num-accion":
            $id_comision = $_POST["comision"];
            $id_subcomision = $_POST["subcomision"];

            $query = $pdo->query("SELECT count(*) FROM acciones
                WHERE num_accion = $num_accion
                    AND id_comision = $id_comision
                    AND id_subcomision = $id_subcomision");
            $existe = $query->fetchColumn();

            echo json_encode(array("status" => "OK", "resultado" => $existe));
        break;

        case "obtener-accion":
            $id_accion = $_POST["id"];

            $query = $pdo->query("SELECT id_accion AS id, num_accion AS num,
                id_comision AS comision, id_subcomision AS subcomision,
                accion, tareas_realizar AS tareas, unidad_medida AS unidad,
                cantidad_medida AS cantidad, uso_presupuesto AS presupuesto,
                monto_presupuesto AS monto, nombre_responsable AS responsable,
                email, telefono
                FROM acciones WHERE id_accion = $id_accion");

            $resultado = $query->fetch(PDO::FETCH_ASSOC);

            echo json_encode(array("status" => "OK", "resultado" => $resultado), JSON_NUMERIC_CHECK);
        break;

        case "obtener-comisiones":
            $claves = $_SESSION["usuario"]["claves"];
            $comisiones = $_SESSION["usuario"]["comisiones"];
            $where = "";

            if (!in_array("0001", $claves)
                && !in_array("1001", $claves))
            {
                $where = "WHERE";
                for ($i = 0; $i < count($comisiones); $i++)
                {
                    $id_comision = $comisiones[$i];
                    $where .= " id_comision = $id_comision";
                    if ( $i < (count($comisiones)-1) )
                    {
                        $where .= " OR";
                    }
                }
            }

            $query = $pdo->query("SELECT id_comision AS id, nombre
                FROM comisiones $where");
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(array("status" => "OK", "resultado" => $resultado));
        break;

        case "obtener-subcomisiones":
            $claves = $_SESSION["usuario"]["claves"];
            $subcomisiones = $_SESSION["usuario"]["subcomisiones"];
            $where = "";

            if (!in_array("0001", $claves)
                && !in_array("1001", $claves))
            {
                if (in_array("1002", $claves))
                {
                    $comisiones = $_SESSION["usuario"]["comisiones"];
                    $where = "WHERE";
                    for ($i = 0; $i < count($comisiones); $i++)
                    {
                        $id_comision = $comisiones[$i];
                        $where .= " id_comision = $id_comision";
                        if ( $i < (count($comisiones)-1) )
                        {
                            $where .= " OR";
                        }
                    }
                }
                else
                {
                    $subcomisiones = $_SESSION["usuario"]["subcomisiones"];
                    $where = "WHERE";
                    for ($i = 0; $i < count($subcomisiones); $i++)
                    {
                        $id_subcomision = $subcomisiones[$i];
                        $where .= " id_subcomision = $id_subcomision";
                        if ( $i < (count($subcomisiones)-1) )
                        {
                            $where .= " OR";
                        }
                    }
                }

            }


            $query = $pdo->query("SELECT id_subcomision AS id, nombre, id_comision
                FROM subcomisiones $where");
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(array("status" => "OK", "resultado" => $resultado));
        break;

        case "importar-acciones-excel":
            // $dir_to_save = "uploads/";
            $file_basename = basename($_FILES["file"]["name"]);
            // $path_to_save = $dir_to_save . $file_basename;
            $file_size = $_FILES["file"]["size"];
            $file = $_FILES["file"]["tmp_name"];
            $file_ext = pathinfo($file_basename, PATHINFO_EXTENSION);

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_mime = finfo_file($finfo, $file);

            if ( $file_mime === "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" && ( $file_ext === "xlsx" || $file_ext === "xls") )
            {
                if ( $file_size > 10000000) // 10 MB
                {
                    echo "El archivo no puede pesar más de 10 MB.";
                    exit;
                }
                else
                {
                    // move_uploaded_file($file, $path_to_save);
                    /** Include path **/
                    // set_include_path(get_include_path() . PATH_SEPARATOR . "../../../Classes/");

                    /** PHPExcel_IOFactory */
                    include "PHPExcel/IOFactory.php";

                    $objPHPExcel = PHPExcel_IOFactory::load($file);

                    $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
                    // var_dump($sheetData);
                    // exit;
                    $strikes = 0;
                    // $num_accion = 1;

                    //$formatter = new NumberFormatter("en_US", NumberFormatter::CURRENCY);
                    //$comision = "PROMOCION Y COMUNICACION";
                    //$subcomision = mb_strtoupper(trim($sheetData[8]["E"]), "UTF-8");
                    $semana_unix = (6*24*60*60) + (23*60*60) + (59*60) + 59;

                    for ($i = 8; $i < count($sheetData); $i++)
                    {
                        $num_accion = $sheetData[$i]["D"];
                        $comision = 5;
                        $subcomision = 11;
                        $accion = mb_strtoupper(trim($sheetData[$i]["F"]), "UTF-8");
                        $tareas_realizar = mb_strtoupper(trim($sheetData[$i]["G"]), "UTF-8");

                        if ($accion === NULL && $tareas_realizar === NULL)
                        {
                            $strikes += 1;
                            if ($strikes === 3)
                            {
                                break 1;
                            }

                            continue;
                        }

                        $unidad_medida = mb_strtoupper(trim($sheetData[$i]["H"]), "UTF-8");
                        $cantidad = $sheetData[$i]["I"] === NULL ? 0 : $sheetData[$i]["I"];
                        $con_presupuesto = $sheetData[$i]["J"] == "X" || $sheetData[$i]["J"] == "x" || $sheetData[$i]["J"] == "1" ? 1 : 0;
                        // $sin_presupuesto = $sheetData[$i]["K"] === "X" || $sheetData[$i]["J"] === "x" || $sheetData[$i]["J"] === "1" ? 1 : 0;
                        $uso_presupuesto = $con_presupuesto ? 1 : 0;

                        //$monto_pesos = $formatter->parseCurrency($sheetData[$i]["L"], "USD");
                        $monto_pesos = $sheetData[$i]["L"] === NULL ? 0 : $sheetData[$i]["L"];
                        $responsable = mb_strtoupper(trim($sheetData[$i]["BX"]), "UTF-8");
                        $email_telefono = mb_strtoupper(trim($sheetData[$i]["BY"]), "UTF-8");

                        $insert = $pdo->query("INSERT INTO acciones VALUES (NULL, $num_accion, $comision, $subcomision, '$accion',
                            '$tareas_realizar', '$unidad_medida', $cantidad, $uso_presupuesto, $monto_pesos, '$responsable', '', '', '$email_telefono', (SELECT NOW()), 0)");
                        if (!$insert)
                        {
                            //header("Location: ../web/index.php?e=1&na=".$num_accion);
                            //exit;
                            var_dump($pdo->errorInfo());
                            exit;
                        }
                        else
                        {
                            $id_accion = $pdo->lastInsertId();
                            $fecha_inicio = "";
                            $fecha_termino = "";

                            $fecha_inicio = $sheetData[$i]["M"] == -1 ? "2015-06-22 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["M"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-06-22 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["N"] == -1 ? "2015-06-29 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["N"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-06-29 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["O"] == -1 ? "2015-07-06 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["O"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-07-06 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["P"] == -1 ? "2015-07-13 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["P"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-07-13 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["Q"] == -1 ? "2015-07-20 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["Q"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-07-20 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["R"] == -1 ? "2015-07-27 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["R"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-07-27 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["S"] == -1 ? "2015-08-03 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["S"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-08-03 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["T"] == -1 ? "2015-08-10 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["T"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-08-10 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["U"] == -1 ? "2015-08-17 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["U"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-08-17 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["V"] == -1 ? "2015-08-24 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["V"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-08-24 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["W"] == -1 ? "2015-08-31 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["W"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-08-31 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["X"] == -1 ? "2015-09-07 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["X"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-09-07 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["Y"] == -1 ? "2015-09-14 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["Y"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-09-14 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["Z"] == -1 ? "2015-09-21 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["Z"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-09-21 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AA"] == -1 ? "2015-09-28 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AA"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-09-28 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AB"] == -1 ? "2015-10-05 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AB"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-10-05 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AC"] == -1 ? "2015-10-12 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AC"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-10-12 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AD"] == -1 ? "2015-10-19 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AD"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-10-19 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AE"] == -1 ? "2015-10-26 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AE"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-10-26 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AF"] == -1 ? "2015-11-02 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AF"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-11-02 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AG"] == -1 ? "2015-11-09 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AG"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-11-09 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AH"] == -1 ? "2015-11-16 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AH"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-11-16 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AI"] == -1 ? "2015-11-23 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AI"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-11-23 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AJ"] == -1 ? "2015-11-30 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AJ"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-11-30 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AK"] == -1 ? "2015-12-07 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AK"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-12-07 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AL"] == -1 ? "2015-12-14 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AL"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-12-14 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AM"] == -1 ? "2015-12-21 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AM"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-12-21 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AN"] == -1 ? "2015-12-28 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AN"] == -1 ? date("Y-m-d H:i:s", strtotime("2015-12-28 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AO"] == -1 ? "2016-01-04 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AO"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-01-04 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AP"] == -1 ? "2016-01-11 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AP"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-01-11 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AQ"] == -1 ? "2016-01-18 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AQ"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-01-18 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AR"] == -1 ? "2016-01-25 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AR"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-01-25 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AS"] == -1 ? "2016-02-01 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AS"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-02-01 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AT"] == -1 ? "2016-02-08 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AT"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-02-08 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AU"] == -1 ? "2016-02-15 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AU"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-02-15 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AV"] == -1 ? "2016-02-22 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AV"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-02-22 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AW"] == -1 ? "2016-02-29 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AW"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-02-29 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AX"] == -1 ? "2016-03-07 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AX"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-03-07 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AY"] == -1 ? "2016-03-14 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AY"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-03-14 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["AZ"] == -1 ? "2016-03-21 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["AZ"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-03-21 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BA"] == -1 ? "2016-03-28 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BA"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-03-28 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BB"] == -1 ? "2016-04-04 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BB"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-04-04 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BC"] == -1 ? "2016-04-11 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BC"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-04-11 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BD"] == -1 ? "2016-04-18 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BD"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-04-18 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BE"] == -1 ? "2016-04-25 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BE"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-04-25 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BF"] == -1 ? "2016-05-02 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BF"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-05-02 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BG"] == -1 ? "2016-05-09 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BG"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-05-09 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BH"] == -1 ? "2016-05-16 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BH"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-05-16 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BI"] == -1 ? "2016-05-23 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BI"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-05-23 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BJ"] == -1 ? "2016-05-30 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BJ"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-05-30 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BK"] == -1 ? "2016-06-06 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BK"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-06-06 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BL"] == -1 ? "2016-06-13 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BL"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-06-13 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BM"] == -1 ? "2016-06-20 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BM"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-06-20 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BN"] == -1 ? "2016-06-27 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BN"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-06-27 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BO"] == -1 ? "2016-07-04 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BO"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-07-04 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BP"] == -1 ? "2016-07-05 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BP"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-07-05 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BQ"] == -1 ? "2016-07-06 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BQ"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-07-06 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BR"] == -1 ? "2016-07-07 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BR"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-07-07 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BS"] == -1 ? "2016-07-08 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BS"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-07-08 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BT"] == -1 ? "2016-07-09 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BT"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-07-09 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BU"] == -1 ? "2016-07-10 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BU"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-07-10 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BV"] == -1 ? "2016-07-11 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BV"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-07-11 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            $fecha_inicio  = $sheetData[$i]["BW"] == -1 ? "2016-07-12 00:00:00" : $fecha_inicio;
                            $fecha_termino = $sheetData[$i]["BW"] == -1 ? date("Y-m-d H:i:s", strtotime("2016-07-12 00:00:00")+$semana_unix) : $fecha_termino;
                            if (!empty($fecha_inicio) && !empty($fecha_termino))
                            {
                                $insert = $pdo->query("INSERT INTO calendario VALUES(NULL, $id_accion, '$fecha_inicio', '$fecha_termino', 1, 0)");
                                $fecha_inicio = "";
                                $fecha_termino = "";
                            }
                            // $num_accion += 1;
                        }
                    }
                }
            }
            else
            {
                echo "El archivo no es Excel";
                exit;
            }
        break;

        case "obtener-presupuesto":
            $query = $pdo->query("SELECT SUM(monto_presupuesto) FROM acciones");
            $resultado = $query->fetchColumn();

            echo json_encode(array("status" => "OK", "resultado" => $resultado), JSON_NUMERIC_CHECK);
        break;

        case "obtener-presupuesto-por-comision":
            $query = $pdo->query("SELECT comision, SUM(monto_presupuesto) AS presupuesto FROM acciones GROUP BY comision");
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(array("status" => "OK", "resultado" => $resultado), JSON_NUMERIC_CHECK);
        break;

        case "obtener-avance-por-comision-fecha-corte":
            $fecha_inicio = $_POST["inicio"];
            $fecha_termino = $_POST["termino"];

            $fi = preg_split("/\//", $fecha_inicio, -1);
            $day = $fi[0];
            $month = $fi[1];
            $year = $fi[2];

            $fecha_inicio_mysql = $year . "-" . $month . "-" . $day;

            $ft = preg_split("/\//", $fecha_termino, -1);
            $day = $ft[0];
            $month = $ft[1];
            $year = $ft[2];

            $fecha_termino_mysql = $year . "-" . $month . "-" . $day;

            // Solo su comision o subcomision
            $claves = $_SESSION["usuario"]["claves"];
            $where_comision = "";

            if (!in_array("0001", $claves)
                && !in_array("1001", $claves))
            {
                if (in_array("1002", $claves))
                {
                    $comisiones = $_SESSION["usuario"]["comisiones"];
                    $where_comision = "AND (";
                    for ($i = 0; $i < count($comisiones); $i++)
                    {
                        $id_comision = $comisiones[$i];
                        $where_comision .= "a.id_comision = $id_comision";
                        if ( $i < (count($comisiones)-1) )
                        {
                            $where_comision .= " OR";
                        }
                    }
                    $where_comision .= ")";
                }
                else
                {
                    $subcomisiones = $_SESSION["usuario"]["subcomisiones"];
                    $where_comision = "AND (";
                    for ($i = 0; $i < count($subcomisiones); $i++)
                    {
                        $id_subcomision = $subcomisiones[$i];
                        $where_comision .= "a.id_subcomision = $id_subcomision";
                        if ( $i < (count($subcomisiones)-1) )
                        {
                            $where_comision .= " OR";
                        }
                    }
                    $where_comision .= ")";
                }
            }

            $pdo->exec("CREATE TEMPORARY TABLE avance_comision_fecha_corte AS
                SELECT a.id_comision, SUM(IF(c.id_estatus = 4, 1, 0)) AS avance, count(*) AS metas
                FROM acciones a
                INNER JOIN calendario c ON c.id_accion = a.id_accion
                WHERE c.id_estatus != 5 AND c.borrado = 0 AND a.borrado = 0 AND
                    (c.fecha_inicio BETWEEN '$fecha_inicio_mysql' AND '$fecha_termino_mysql' OR c.fecha_termino BETWEEN '$fecha_inicio_mysql' AND '$fecha_termino_mysql')
                    $where_comision
                GROUP BY a.id_comision;");

            $query = $pdo->query("SELECT com.nombre AS comision, IF(mpc.avance IS NULL, 0, mpc.avance) AS avance,
                IF(mpc.metas IS NULL, 0, mpc.metas) AS metas
                FROM comisiones com
                INNER JOIN avance_comision_fecha_corte mpc ON mpc.id_comision = com.id_comision");
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(array("status" => "OK", "resultado" => $resultado));
        break;

        case "obtener-avance-por-comision-global":
            // Solo su comision o subcomision
            $claves = $_SESSION["usuario"]["claves"];
            $where_comision = "";

            if (!in_array("0001", $claves)
                && !in_array("1001", $claves))
            {
                if (in_array("1002", $claves))
                {
                    $comisiones = $_SESSION["usuario"]["comisiones"];
                    $where_comision = "AND (";
                    for ($i = 0; $i < count($comisiones); $i++)
                    {
                        $id_comision = $comisiones[$i];
                        $where_comision .= "a.id_comision = $id_comision";
                        if ( $i < (count($comisiones)-1) )
                        {
                            $where_comision .= " OR";
                        }
                    }
                    $where_comision .= ")";
                }
                else
                {
                    $subcomisiones = $_SESSION["usuario"]["subcomisiones"];
                    $where_comision = "AND (";
                    for ($i = 0; $i < count($subcomisiones); $i++)
                    {
                        $id_subcomision = $subcomisiones[$i];
                        $where_comision .= "a.id_subcomision = $id_subcomision";
                        if ( $i < (count($subcomisiones)-1) )
                        {
                            $where_comision .= " OR";
                        }
                    }
                    $where_comision .= ")";
                }
            }

            $pdo->exec("CREATE TEMPORARY TABLE avance_comision_global AS
                SELECT a.id_comision, SUM(IF(c.id_estatus = 4, 1, 0)) AS avance, count(*) AS metas
                FROM acciones a
                INNER JOIN calendario c ON c.id_accion = a.id_accion
                WHERE c.id_estatus != 5 AND c.borrado = 0 AND a.borrado = 0 $where_comision
                GROUP BY a.id_comision;");

            $query = $pdo->query("SELECT com.nombre AS comision, IF(mpc.avance IS NULL, 0, mpc.avance) AS avance,
                IF(mpc.metas IS NULL, 0, mpc.metas) AS metas
                FROM comisiones com
                INNER JOIN avance_comision_global mpc ON mpc.id_comision = com.id_comision");
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(array("status" => "OK", "resultado" => $resultado));
        break;

        case "obtener-avance-global":
            $fecha_inicio = $_POST["inicio"];
            $fecha_termino = $_POST["termino"];

            $fi = preg_split("/\//", $fecha_inicio, -1);
            $day = $fi[0];
            $month = $fi[1];
            $year = $fi[2];

            $fecha_inicio_mysql = $year . "-" . $month . "-" . $day;

            $ft = preg_split("/\//", $fecha_termino, -1);
            $day = $ft[0];
            $month = $ft[1];
            $year = $ft[2];

            $fecha_termino_mysql = $year . "-" . $month . "-" . $day;

            // Solo su comision o subcomision
            $claves = $_SESSION["usuario"]["claves"];
            $where_comision = "";

            if (!in_array("0001", $claves)
                && !in_array("1001", $claves))
            {
                if (in_array("1002", $claves))
                {
                    $comisiones = $_SESSION["usuario"]["comisiones"];
                    $where_comision = "AND (";
                    for ($i = 0; $i < count($comisiones); $i++)
                    {
                        $id_comision = $comisiones[$i];
                        $where_comision .= "a.id_comision = $id_comision";
                        if ( $i < (count($comisiones)-1) )
                        {
                            $where_comision .= " OR";
                        }
                    }
                    $where_comision .= ")";
                }
                else
                {
                    $subcomisiones = $_SESSION["usuario"]["subcomisiones"];
                    $where_comision = "AND (";
                    for ($i = 0; $i < count($subcomisiones); $i++)
                    {
                        $id_subcomision = $subcomisiones[$i];
                        $where_comision .= "a.id_subcomision = $id_subcomision";
                        if ( $i < (count($subcomisiones)-1) )
                        {
                            $where_comision .= " OR";
                        }
                    }
                    $where_comision .= ")";
                }
            }

            $pdo->exec("CREATE TEMPORARY TABLE avance_comision_fecha_corte AS
                SELECT a.id_comision, SUM(IF(c.id_estatus = 4, 1, 0)) AS avance, count(*) AS metas
                FROM acciones a
                INNER JOIN calendario c ON c.id_accion = a.id_accion
                WHERE c.id_estatus != 5 AND c.borrado = 0 AND a.borrado = 0 AND
                    (c.fecha_inicio BETWEEN '$fecha_inicio_mysql' AND '$fecha_termino_mysql' OR c.fecha_termino BETWEEN '$fecha_inicio_mysql' AND '$fecha_termino_mysql')
                    $where_comision
                GROUP BY a.id_comision
                ORDER BY a.id_comision;");

            $query = $pdo->query("SELECT com.nombre AS comision, IF(mpc.avance IS NULL, 0, mpc.avance) AS avance,
                IF(mpc.metas IS NULL, 0, mpc.metas) AS metas
                FROM comisiones com
                INNER JOIN avance_comision_fecha_corte mpc ON mpc.id_comision = com.id_comision");
            $resultado_fecha_corte = $query->fetchAll(PDO::FETCH_ASSOC);

            $pdo->exec("CREATE TEMPORARY TABLE avance_comision_global AS
                SELECT a.id_comision, SUM(IF(c.id_estatus = 4, 1, 0)) AS avance, count(*) AS metas
                FROM acciones a
                INNER JOIN calendario c ON c.id_accion = a.id_accion
                WHERE c.id_estatus != 5 AND c.borrado = 0 AND a.borrado = 0
                $where_comision
                GROUP BY a.id_comision
                ORDER BY a.id_comision;");

            $query = $pdo->query("SELECT com.nombre AS comision, IF(mpc.avance IS NULL, 0, mpc.avance) AS avance,
                IF(mpc.metas IS NULL, 0, mpc.metas) AS metas
                FROM comisiones com
                INNER JOIN avance_comision_global mpc ON mpc.id_comision = com.id_comision");
            $resultado_global = $query->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(array("status" => "OK",
                "resultado" => array("fecha_corte" => $resultado_fecha_corte,
                    "global" => $resultado_global)));
        break;

        case "obtener-calendario-por-accion":
            $id_accion = $_POST["id"];

            $query = $pdo->query("SELECT id_calendario AS id, DATE_FORMAT(fecha_inicio, '%d/%m/%Y') AS inicio,
                DATE_FORMAT(fecha_termino, '%d/%m/%Y') AS termino,
                IF(id_estatus=1,'',IF(id_estatus=2,'<span class=\"label\">Condicionado</span>',
                    IF(id_estatus=3,'<span class=\"warning label\">En Revisión</span>',
                        IF(id_estatus=4,'<span class=\"success label\">Completado</span>','<span class=\"alert label\">Cancelado</span>')))) AS estatus,
                IF(id_estatus=2,observaciones,'') AS observaciones,
                IF(id_estatus=4 || id_estatus=5, 1, 0) AS bloqueado
            FROM calendario WHERE id_accion = $id_accion AND borrado = 0");
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(array("status" => "OK", "resultado" => $resultado));
        break;

        case "obtener-calendario-por-accion-con-archivos":
            $id_accion = $_POST["id"];

            $query = $pdo->query("SELECT id_calendario AS id, id_accion, DATE_FORMAT(fecha_inicio, '%d/%m/%Y') AS inicio,
                DATE_FORMAT(fecha_termino, '%d/%m/%Y') AS termino,
                IF(id_estatus=1,'',IF(id_estatus=2,'<span class=\"label\">Condicionado</span>',
                    IF(id_estatus=3,'<span class=\"warning label\">En Revisión</span>',
                        IF(id_estatus=4,'<span class=\"success label\">Completado</span>','<span class=\"alert label\">Cancelado</span>')))) AS estatus,
                IF(id_estatus=2,observaciones,'') AS observaciones
            FROM calendario WHERE id_accion = $id_accion AND borrado = 0");
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

            for ($i = 0; $i < count($resultado); $i++)
            {
                $id_calendario = $resultado[$i]["id"];
                $id_accion = $resultado[$i]["id_accion"];
                $inicio = str_replace("/", "", $resultado[$i]["inicio"]);
                $termino = str_replace("/", "", $resultado[$i]["termino"]);

                $path = "../images/evidencias/".$id_calendario."_".$id_accion."/".$inicio."_".$termino."/";
                $archivos = array();
                // Buscar archivos.
                if (is_dir($path))
                {
                    if ($dh = opendir($path))
                    {
                        while (($file = readdir($dh)) !== false)
                        {
                            if ($file !== "." && $file !== ".." && !is_dir($path.$file))
                            {
                                array_push($archivos, $path.$file);
                            }
                        }
                    }
                }

                $resultado[$i]["archivos"] = $archivos;
            }

            echo json_encode(array("status" => "OK", "resultado" => $resultado));
        break;

        case "obtener-fechas-min-max-calendario":
            $query = $pdo->query("SELECT MIN(fecha_inicio), MAX(fecha_termino)
                FROM calendario WHERE borrado = 0");
            $resultado = $query->fetch(PDO::FETCH_ASSOC);

            echo json_encode(array("status" => "OK",
                "resultado" => $resultado));
        break;

        case "obtener-rp01":
            $id_comision = $_POST["comision"];
            $id_subcomision = $_POST["subcomision"];
            $fecha_inicio = $_POST["inicio"];
            $fecha_termino = $_POST["termino"];

            $fi = preg_split("/\//", $fecha_inicio, -1);
            $day = $fi[0];
            $month = $fi[1];
            $year = $fi[2];

            $fecha_inicio_mysql = $year . "-" . $month . "-" . $day;

            $ft = preg_split("/\//", $fecha_termino, -1);
            $day = $ft[0];
            $month = $ft[1];
            $year = $ft[2];

            $fecha_termino_mysql = $year . "-" . $month . "-" . $day;

            $query = $pdo->query("SELECT a.num_accion AS num,
                unidad_medida AS unidad, a.accion, a.tareas_realizar AS tareas,
                CONCAT('$', FORMAT(monto_presupuesto, 2)) AS monto,
                SUM(IF(id_estatus = 4, 1, 0)) AS avance, count(*) AS metas,
                GROUP_CONCAT( CONCAT('Del ',DATE_FORMAT(fecha_inicio, '%d/%m/%Y'),
                    ' al ', DATE_FORMAT(fecha_termino, '%d/%m/%Y'))
                    ORDER BY fecha_inicio ASC SEPARATOR '<hr>') AS pendientes
                FROM acciones a
                INNER JOIN calendario c ON c.id_accion = a.id_accion
                WHERE a.id_comision = $id_comision
                    AND a.id_subcomision = $id_subcomision
                    AND (c.fecha_inicio BETWEEN '$fecha_inicio_mysql'
                            AND '$fecha_termino_mysql'
                        OR c.fecha_termino BETWEEN '$fecha_inicio_mysql'
                            AND '$fecha_termino_mysql')
                    AND c.id_estatus != 5 AND c.borrado = 0 AND a.borrado = 0
                GROUP BY a.id_accion");
            $resultado_fecha_corte = $query->fetchAll(PDO::FETCH_ASSOC);

            $query = $pdo->query("SELECT SUM(IF(c.id_estatus = 4, 1, 0)) AS avance,
                count(*) AS metas
                FROM acciones a
                INNER JOIN calendario c ON c.id_accion = a.id_accion
                WHERE a.id_comision = $id_comision
                    AND a.id_subcomision = $id_subcomision
                    AND c.id_estatus != 5 AND c.borrado = 0 AND a.borrado = 0");
            $resultado_global = $query->fetch(PDO::FETCH_ASSOC);

            echo json_encode(array("status" => "OK",
                "resultado" => array("fecha_corte" => $resultado_fecha_corte,
                    "global" => $resultado_global)), JSON_NUMERIC_CHECK);
        break;

        case "obtener-rp02":
            $id_comision = $_POST["comision"];
            $id_subcomision = $_POST["subcomision"];
            $fecha_inicio = $_POST["inicio"];
            $fecha_termino = $_POST["termino"];

            $fi = preg_split("/\//", $fecha_inicio, -1);
            $day = $fi[0];
            $month = $fi[1];
            $year = $fi[2];

            $fecha_inicio_mysql = $year . "-" . $month . "-" . $day;

            $ft = preg_split("/\//", $fecha_termino, -1);
            $day = $ft[0];
            $month = $ft[1];
            $year = $ft[2];

            $fecha_termino_mysql = $year . "-" . $month . "-" . $day;

            $query = $pdo->query("SELECT a.num_accion AS num,
                unidad_medida AS unidad, a.accion, a.tareas_realizar AS tareas,
                SUM(IF(id_estatus = 4, 1, 0)) AS avance, count(*) AS metas
                FROM acciones a
                INNER JOIN calendario c ON c.id_accion = a.id_accion
                WHERE a.id_comision = $id_comision
                    AND a.id_subcomision = $id_subcomision
                    AND (c.fecha_inicio BETWEEN '$fecha_inicio_mysql'
                            AND '$fecha_termino_mysql'
                        OR c.fecha_termino BETWEEN '$fecha_inicio_mysql'
                            AND '$fecha_termino_mysql')
                    AND c.id_estatus != 5 AND c.borrado = 0 AND a.borrado = 0
                GROUP BY a.id_accion");
            $resultado_fecha_corte = $query->fetchAll(PDO::FETCH_ASSOC);

            $query = $pdo->query("SELECT SUM(IF(c.id_estatus = 4, 1, 0)) AS avance,
                count(*) AS metas
                FROM acciones a
                INNER JOIN calendario c ON c.id_accion = a.id_accion
                WHERE a.id_comision = $id_comision
                    AND a.id_subcomision = $id_subcomision
                    AND c.id_estatus != 5 AND c.borrado = 0 AND a.borrado = 0");
            $resultado_global = $query->fetch(PDO::FETCH_ASSOC);

            echo json_encode(array("status" => "OK",
                "resultado" => array("fecha_corte" => $resultado_fecha_corte,
                    "global" => $resultado_global)), JSON_NUMERIC_CHECK);
        break;

        case "agregar-accion":
            $num = $_POST["num"];
            $id_comision = $_POST["comision"];
            $id_subcomision = $_POST["subcomision"];
            $accion = mb_strtoupper($_POST["_accion"], "UTF-8");
            $tareas = mb_strtoupper($_POST["tareas"], "UTF-8");
            $unidad = mb_strtoupper($_POST["unidad"], "UTF-8");
            $cantidad = $_POST["cantidad"];
            $presupuesto = $_POST["presupuesto"] === "true" ? "1" : "0";
            $monto = $_POST["monto"];
            $responsable = mb_strtoupper($_POST["responsable"], "UTF-8");
            $email = mb_strtoupper($_POST["email"], "UTF-8");
            $telefono = $_POST["telefono"];

            $query = $pdo->prepare("INSERT INTO acciones (num_accion, id_comision,
                id_subcomision, accion, tareas_realizar, unidad_medida,
                cantidad_medida, uso_presupuesto, monto_presupuesto,
                nombre_responsable, email, telefono) VALUES (
                    :num, :id_comision, :id_subcomision, :accion, :tareas, :unidad,
                    :cantidad, :presupuesto, :monto, :responsable, :email, :telefono
                )");

            $exito = $query->execute(array
                (
                    "num" => $num,
                    "id_comision" => $id_comision,
                    "id_subcomision" => $id_subcomision,
                    "accion" => $accion,
                    "tareas" => $tareas,
                    "unidad" => $unidad,
                    "cantidad" => $cantidad,
                    "presupuesto" => $presupuesto,
                    "monto" => $monto,
                    "responsable" => $responsable,
                    "email" => $email,
                    "telefono" => $telefono
                )
            );

            if ($exito)
            {
                $id_accion = $pdo->lastInsertId();
                $id_usuario = $_SESSION["usuario"]["id"];
                $insert = $pdo->prepare("INSERT INTO bitacora
                    (descripcion, json, id_accion, id_usuario)
                    VALUES (:descripcion, :json, :id_accion, :id_usuario)");
                $insert->execute(array
                    (
                        "descripcion" => "Agregó una acción.",
                        "json" => json_encode($_POST),
                        "id_accion" => $id_accion,
                        "id_usuario" => $id_usuario
                    )
                );

                echo json_encode(array("status" => "OK",
                    "msg" => "¡Acción agregada con éxito!"));
            }
            else
            {
                echo json_encode(array("status" => "ERROR",
                    "msg" => "¡Oh no! Algo falló, inténtalo de nuevo porfavor."));
            }
        break;

        case "guardar-cambios-accion":
            $id_accion = $_POST["id"];
            $num = $_POST["num"];
            $id_comision = $_POST["comision"];
            $id_subcomision = $_POST["subcomision"];
            $accion = mb_strtoupper($_POST["_accion"], "UTF-8");
            $tareas = mb_strtoupper($_POST["tareas"], "UTF-8");
            $unidad = mb_strtoupper($_POST["unidad"], "UTF-8");
            $cantidad = $_POST["cantidad"];
            $presupuesto = $_POST["presupuesto"] === "true" ? "1" : "0";
            $monto = $_POST["monto"];
            $responsable = mb_strtoupper($_POST["responsable"], "UTF-8");
            $email = mb_strtoupper($_POST["email"], "UTF-8");
            $telefono = $_POST["telefono"];

            $query = $pdo->prepare("UPDATE acciones
                SET
                    num_accion = :num,
                    id_comision = :id_comision,
                    id_subcomision = :id_subcomision,
                    accion = :accion,
                    tareas_realizar = :tareas,
                    unidad_medida = :unidad,
                    cantidad_medida = :cantidad,
                    uso_presupuesto = :presupuesto,
                    monto_presupuesto = :monto,
                    nombre_responsable = :responsable,
                    email = :email,
                    telefono = :telefono
                WHERE id_accion = :id_accion");

            $exito = $query->execute(array
                (
                    "id_accion" => $id_accion,
                    "num" => $num,
                    "id_comision" => $id_comision,
                    "id_subcomision" => $id_subcomision,
                    "accion" => $accion,
                    "tareas" => $tareas,
                    "unidad" => $unidad,
                    "cantidad" => $cantidad,
                    "presupuesto" => $presupuesto,
                    "monto" => $monto,
                    "responsable" => $responsable,
                    "email" => $email,
                    "telefono" => $telefono
                )
            );

            if ($exito)
            {
                $id_usuario = $_SESSION["usuario"]["id"];
                $insert = $pdo->prepare("INSERT INTO bitacora
                    (descripcion, json, id_accion, id_usuario)
                    VALUES (:descripcion, :json, :id_accion, :id_usuario)");
                $insert->execute(array
                    (
                        "descripcion" => "Editó una acción.",
                        "json" => json_encode($_POST),
                        "id_accion" => $id_accion,
                        "id_usuario" => $id_usuario
                    )
                );

                echo json_encode(array("status" => "OK",
                    "msg" => "¡Cambios guardados con éxito!"));
            }
            else
            {
                echo json_encode(array("status" => "ERROR",
                    "msg" => "¡Oh no! Algo falló, inténtalo de nuevo porfavor."));
            }
        break;

        case "eliminar-accion":
            $id_accion = $_POST["id"];

            $update = $pdo->prepare("UPDATE acciones
                SET borrado = 1
                WHERE id_accion = :id_accion");

            $exito = $update->execute(array
                (
                    "id_accion" => $id_accion
                )
            );

            if ($exito)
            {
                $insert = $pdo->prepare("INSERT INTO bitacora
                    (descripcion, json, id_accion, id_usuario)
                    VALUES (:descripcion, :json, :id_accion, :id_usuario)");
                $insert->execute(array
                    (
                        "descripcion" => "Editó una acción.",
                        "json" => json_encode($_POST),
                        "id_accion" => $id_accion,
                        "id_usuario" => $id_usuario
                    )
                );

                echo json_encode(array("status" => "OK",
                    "msg" => "¡Acción eliminada con éxito!"));
            }
            else
            {
                echo json_encode(array("status" => "ERROR",
                    "msg" => "¡Oh no! Algo falló, inténtalo de nuevo porfavor."));
            }
        break;

        case "subir-evidencia":
            $id_calendario = $_POST["id"];
            $error = false;

            $query = $pdo->query("SELECT id_accion,
                DATE_FORMAT(fecha_inicio, '%d%m%Y') AS inicio,
                DATE_FORMAT(fecha_termino, '%d%m%Y') AS termino
                FROM calendario
                WHERE id_calendario = $id_calendario");
            $calendario = $query->fetch(PDO::FETCH_ASSOC);
            $id_accion = $calendario["id_accion"];
            $inicio = $calendario["inicio"];
            $termino = $calendario["termino"];

            $update = $pdo->query("UPDATE calendario
                SET id_estatus = 3
                WHERE id_calendario = $id_calendario");

            if ($update)
            {
                $uploaddir = "../images/evidencias/".$id_calendario."_".$id_accion."/".$inicio."_".$termino."/";

                foreach($_FILES as $file)
                {
                    // $filename = "del_" . $inicio . "_al_" . $termino . "." . pathinfo(basename($file["name"]), PATHINFO_EXTENSION);
                    // $filename = date("dmYHis").".".pathinfo(basename($file["name"]), PATHINFO_EXTENSION);
                    $filename = basename($file["name"]);

                    if (!is_dir($uploaddir))
                    {
                        mkdir($uploaddir, 0777, true);
                    }

                    if(!move_uploaded_file($file["tmp_name"], $uploaddir . $filename))
                    {
                        $error = true;
                    }
                }
            }
            else
            {
                $error = true;
            }

            if (!$error)
            {
                $id_usuario = $_SESSION["usuario"]["id"];
                $insert = $pdo->prepare("INSERT INTO bitacora
                    (descripcion, json, id_accion, id_usuario)
                    VALUES (:descripcion, :json, :id_accion, :id_usuario)");
                $insert->execute(array
                    (
                        "descripcion" => "Subió una evidencia.",
                        "json" => json_encode($_POST),
                        "id_accion" => $id_accion,
                        "id_usuario" => $id_usuario
                    )
                );

                echo json_encode(array("status" => "OK"));
            }
            else
            {
                echo json_encode(array("status" => "ERROR"));
            }
        break;

        case "aceptar-evidencia":
            $id_calendario = $_POST["id"];

            $update = $pdo->query("UPDATE calendario SET id_estatus = 4 WHERE id_calendario = $id_calendario");

            if ($update)
            {
                $query = $pdo->query("SELECT id_accion
                FROM calendario
                WHERE id_calendario = $id_calendario");
            $id_accion = $query->fetchColumn();

                $id_usuario = $_SESSION["usuario"]["id"];
                $insert = $pdo->prepare("INSERT INTO bitacora
                    (descripcion, json, id_accion, id_usuario)
                    VALUES (:descripcion, :json, :id_accion, :id_usuario)");
                $insert->execute(array
                    (
                        "descripcion" => "Aceptó una evidencia.",
                        "json" => json_encode($_POST),
                        "id_accion" => $id_accion,
                        "id_usuario" => $id_usuario
                    )
                );

                echo json_encode(array("status" => "OK"));
            }
            else
            {
                echo json_encode(array("status" => "ERROR"));
            }
        break;

        case "condicionar-evidencia":
            $id_calendario = $_POST["id"];
            $observaciones = $_POST["observaciones"];

            $update = $pdo->query("UPDATE calendario SET id_estatus = 2, observaciones = '$observaciones' WHERE id_calendario = $id_calendario");

            if ($update)
            {
                $query = $pdo->query("SELECT id_accion
                FROM calendario
                WHERE id_calendario = $id_calendario");
            $id_accion = $query->fetchColumn();

                $id_usuario = $_SESSION["usuario"]["id"];
                $insert = $pdo->prepare("INSERT INTO bitacora
                    (descripcion, json, id_accion, id_usuario)
                    VALUES (:descripcion, :json, :id_accion, :id_usuario)");
                $insert->execute(array
                    (
                        "descripcion" => "Condicionó una evidencia.",
                        "json" => json_encode($_POST),
                        "id_accion" => $id_accion,
                        "id_usuario" => $id_usuario
                    )
                );

                echo json_encode(array("status" => "OK"));
            }
            else
            {
                echo json_encode(array("status" => "ERROR"));
            }
        break;
    }
?>