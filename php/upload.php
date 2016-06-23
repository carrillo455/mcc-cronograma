<?php
	$dir_to_save = "uploads/";
	$file_basename = basename($_FILES["fileToUpload"]["name"]);
	$path_to_save = $dir_to_save . $file_basename;
	$file_size = $_FILES["fileToUpload"]["size"];
	$file = $_FILES['fileToUpload']['tmp_name'];
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
			// set_include_path(get_include_path() . PATH_SEPARATOR . '../../../Classes/');

			/** PHPExcel_IOFactory */
			include 'PHPExcel/IOFactory.php';

			$objPHPExcel = PHPExcel_IOFactory::load($file);

			$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			var_dump($sheetData);

			// CRONOGRAMA DE ACTIVIDADES
			// LA PRIMERA ACCION COMIENZA EN EL INDEX 8 DEL ARREGLO.
			// ASI ESTA DESGLOZADO:
				// $sheetData[8]["F"] -> ACCION
				// $sheetData[8]["G"] -> TAREAS A REALIZAR
				// $sheetData[8]["H"] -> UNIDAD DE MEDIDA
				// $sheetData[8]["I"] -> CANTIDAD
				// $sheetData[8]["J"] -> CON PRESUPUESTO (MARCADO CON UNA 'X')
				// $sheetData[8]["K"] -> SIN PRESUPUESTO (X)
				// $sheetData[8]["L"] -> MONTO EN PESOS
				// $sheetData[8]["M"]=> 2015-06-22 (MARCADO CON UN '0')
			    // $sheetData[8]["N"]=> 2015-06-29
			    // $sheetData[8]["O"]=> 2015-07-06
			    // $sheetData[8]["P"]=> 2015-07-13
			    // $sheetData[8]["Q"]=> 2015-07-20
			    // $sheetData[8]["R"]=> 2015-07-27
			    // $sheetData[8]["S"]=> 2015-08-03
			    // $sheetData[8]["T"]=> 2015-08-10
			    // $sheetData[8]["U"]=> 2015-08-17
			    // $sheetData[8]["V"]=> 2015-08-24
			    // $sheetData[8]["W"]=> 2015-08-31
			    // $sheetData[8]["X"]=> 2015-09-07
			    // $sheetData[8]["Y"]=> 2015-09-14
			    // $sheetData[8]["Z"]=> 2015-09-21
			    // $sheetData[8]["AA"]=> 2015-09-28
			    // $sheetData[8]["AB"]=> 2015-10-05
			    // $sheetData[8]["AC"]=> 2015-10-12
			    // $sheetData[8]["AD"]=> 2015-10-19
			    // $sheetData[8]["AE"]=> 2015-10-26
			    // $sheetData[8]["AF"]=> 2015-11-02
			    // $sheetData[8]["AG"]=> 2015-11-09
			    // $sheetData[8]["AH"]=> 2015-11-16
			    // $sheetData[8]["AI"]=> 2015-11-23
			    // $sheetData[8]["AJ"]=> 2015-11-30
			    // $sheetData[8]["AK"]=> 2015-12-07
			    // $sheetData[8]["AL"]=> 2015-12-14
			    // $sheetData[8]["AM"]=> 2015-12-21
			    // $sheetData[8]["AN"]=> 2015-12-28
			    // $sheetData[8]["AO"]=> 2016-01-04
			    // $sheetData[8]["AP"]=> 2016-01-11
			    // $sheetData[8]["AQ"]=> 2016-01-18
			    // $sheetData[8]["AR"]=> 2016-01-25
			    // $sheetData[8]["AS"]=> 2016-02-01
			    // $sheetData[8]["AT"]=> 2016-02-08
			    // $sheetData[8]["AU"]=> 2016-02-15
			    // $sheetData[8]["AV"]=> 2016-02-22
			    // $sheetData[8]["AW"]=> 2016-02-29
			    // $sheetData[8]["AX"]=> 2016-03-07
			    // $sheetData[8]["AY"]=> 2016-03-14
			    // $sheetData[8]["AZ"]=> 2016-03-21
			    // $sheetData[8]["BA"]=> 2016-03-28
			    // $sheetData[8]["BB"]=> 2016-04-04
			    // $sheetData[8]["BC"]=> 2016-04-11
			    // $sheetData[8]["BD"]=> 2016-04-18
			    // $sheetData[8]["BE"]=> 2016-04-25
			    // $sheetData[8]["BF"]=> 2016-05-02
			    // $sheetData[8]["BG"]=> 2016-05-09
			    // $sheetData[8]["BH"]=> 2016-05-16
			    // $sheetData[8]["BI"]=> 2016-05-23
			    // $sheetData[8]["BJ"]=> 2016-05-30
			    // $sheetData[8]["BK"]=> 2016-06-06
			    // $sheetData[8]["BL"]=> 2016-06-13
			    // $sheetData[8]["BM"]=> 2016-06-20
			    // $sheetData[8]["BN"]=> 2016-06-27
			    // $sheetData[8]["BO"]=> 2016-07-04
			    // $sheetData[8]["BP"]=> 2016-07-05
			    // $sheetData[8]["BQ"]=> 2016-07-06
			    // $sheetData[8]["BR"]=> 2016-07-07
			    // $sheetData[8]["BS"]=> 2016-07-08
			    // $sheetData[8]["BT"]=> 2016-07-09
			    // $sheetData[8]["BU"]=> 2016-07-10
			    // $sheetData[8]["BV"]=> 2016-07-11
			    // $sheetData[8]["BW"]=> 2016-07-12
			    // $sheetData[8]["BX"]=> NOMBRE RESPONSABLE string(32) "LAURA DEL ANGEL  Y LUCIA RAMIREZ"
			    // $sheetData[8]["BY"]=> EMAIL TELEFONO string(17) "LAURA@HOTMAIL.COM"
			    // $sheetData[8]["BZ"]=> NULL
		}
	}
	else
	{
		echo "El archivo no es Excel";
		exit;
	}
// $mime_type = $fi->buffer(file_get_contents($file));

// echo $file_ext;
// $uploadOk = 1;
// $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// // Check if image file is a actual image or fake image
// if(isset($_POST["submit"])) {
//     $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
//     if($check !== false) {
//         echo "File is an image - " . $check["mime"] . ".";
//         $uploadOk = 1;
//     } else {
//         echo "File is not an image.";
//         $uploadOk = 0;
//     }

	// if ( $file_size > 80)
	// {
	// 	echo "perate";
	// 	exit;
	// }

	// move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target_file);

	// // $finfo = new finfo(FILEINFO_MIME_TYPE);
 // //    if (false === $ext = array_search(
 // //        $finfo->file($_FILES['fileToUpload']['tmp_name']),
 // //        array(
 // //            "xlsx" => "application/vnd.ms-excel"
 // //        ),
 // //        true
 // //    )) {
 // //        throw new RuntimeException('Invalid file format.');
 // //    }

 //    var_dump($_FILES);
?>