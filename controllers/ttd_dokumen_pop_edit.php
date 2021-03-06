<?php
	error_reporting(0);
	session_start();
	include_once "../models/autoloader.php";
	require_once "../twig-1x/lib/Twig/Autoloader.php";
	
	TUser::BelumLogin();
	
	if($_POST["save"] == "Save") {
		$db = new DBConnection();
		$db->perintahSQL = "
			UPDATE itbl_apps_ttd_dokumen SET
				tanggal=?, 
				judul_ttd=?, id_pegawai=?
			WHERE
				id=?
		";
		$db->add_parameter("s", $_POST["tanggal"]);
		$db->add_parameter("s", $_POST["judul_ttd"]);
		$db->add_parameter("i", $_POST["id_pegawai"]);
		$db->add_parameter("i", $_POST["id"]);
		$db->execute_non_query();
		
		echo "
			<script>
				window.opener.window.location.reload();
				window.close();
			</script>
		";
	}
	
	$combo_pegawai = PegawaiModel::GetPegawaiCombo01();
	
	$db_ttd = new DBConnection();
	$db_ttd->perintahSQL = "
		SELECT
			a.*, b.nama_pegawai, c.nama_dokumen
		FROM
			itbl_apps_ttd_dokumen a
			LEFT JOIN t_pegawai b ON a.id_pegawai = b.id
			LEFT JOIN itbl_apps_dokumen c ON a.id_dokumen = c.id
		WHERE
			a.id = ?
		ORDER BY
			a.tanggal ASC, a.kode_ttd ASC
	";
	$db_ttd->add_parameter("i", $_GET["id"]);
	$ds_ttd = $db_ttd->execute_reader();
	$db_ttd = null;
	
	/********************************************* Twig engine *********************************************/
	Twig_Autoloader::register();

	$loader = new Twig_Loader_Filesystem('../views');
	$twig = new Twig_Environment($loader);
	
	echo $twig->render('ttd_dokumen_pop_edit.php', array(
			'judul' => 'Assalamualaikum',
			'qs' => query_string_to_array($_SERVER["QUERY_STRING"]),
			'combo_pegawai' => $combo_pegawai,
			'ttd' => $ds_ttd[0]
		)
	);
	/********************************************* End Of : Twig Engine *********************************************/
?>