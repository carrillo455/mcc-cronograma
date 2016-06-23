<?php

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'dt_acciones';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => 'id', 'dt' => 0 ),
	array( 'db' => 'num', 'dt' => 1 ),
	array( 'db' => 'comision', 'dt' => 2 ),
	array( 'db' => 'subcomision',  'dt' => 3 ),
	array( 'db' => 'accion',   'dt' => 4 ),
	array( 'db' => 'tareas', 'dt' => 5 ),
	array( 'db' => 'unidad', 'dt' => 6 ),
	array( 'db' => 'presupuesto', 'dt' => 7 ),
	array( 'db' => 'veces', 'dt' => 8 ),
	array( 'db' => 'pendientes', 'dt' => 9 ),
	array( 'db' => 'condicionados', 'dt' => 10 )
	// array(
	// 	'db'        => 'start_date',
	// 	'dt'        => 4,
	// 	'formatter' => function( $d, $row ) {
	// 		return date( 'jS M y', strtotime($d));
	// 	}
	// ),
	// array(
	// 	'db'        => 'salary',
	// 	'dt'        => 5,
	// 	'formatter' => function( $d, $row ) {
	// 		return '$'.number_format($d);
	// 	}
	// )
);

// SQL server connection information
$sql_details = array(
	'user' => 'mcctampico',
	'pass' => 'jesusconnosotros',
	'db'   => 'cronograma',
	'host' => 'mysql.mccdiocesistampico.org'
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require( 'ssp.class.php' );
session_name("cronograma_mcc_2015");
session_start();

$my_where = "";
$claves = $_SESSION["usuario"]["claves"];

if (in_array("0001", $claves) || in_array("1001", $claves)) {
	$my_where .= "1";
} else if (in_array("1002", $claves)) {
	$comisiones = $_SESSION["usuario"]["comisiones"];

	for ($i = 0; $i < count($comisiones); $i++)
	{
		$id_comision = $comisiones[$i];
		$my_where .= "id_comision = $id_comision";

		if ($i < (count($comisiones) - 1)) {
			$my_where .= " OR ";
		}
	}
	// $id_comision = id_comision = $id_comision;
	// $my_where .= "id_comision = $id_comision";
} else if (in_array("1003", $claves)) {
	$subcomisiones = $_SESSION["usuario"]["subcomisiones"];

	for ($i = 0; $i < count($subcomisiones); $i++)
	{
		$id_subcomision = $subcomisiones[$i];
		$my_where .= "id_subcomision = $id_subcomision";

		if ($i < (count($subcomisiones) - 1)) {
			$my_where .= " OR ";
		}
	}
	// $id_subcomision = $_SESSION["usuario"]["subcomision"];
	// $my_where .= "id_subcomision = $id_subcomision";
} else {
	$my_where .= "0";
}

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $my_where ),
	JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
