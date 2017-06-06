<?php
session_start();
include_once "../library/inc.seslogin.php";
include_once "../library/inc.connection.php";
include_once "../library/inc.library.php";
?>
<html>
<head>
<title>:: Laporan Data Penerbit</title>
<link href="../styles/styles_cetak.css" rel="stylesheet" type="text/css">
</head>
<body>
<h2>LAPORAN DATA PENERBIT </h2>
<table class="table-list" width="700" border="0" cellspacing="1" cellpadding="2">
  <tr>
    <td width="24" align="center" bgcolor="#F5F5F5"><strong>No</strong></td>
    <td width="55" bgcolor="#F5F5F5"><strong>Kode</strong></td>
    <td width="605" bgcolor="#F5F5F5"><strong>Nama Penerbit </strong></td>
  </tr>
  <?php
  	// Skrip menampilkan data Penerbit
	$mySql = "SELECT * FROM penerbit ORDER BY kd_penerbit ASC";
	$myQry = mysql_query($mySql, $koneksidb)  or die ("Query 1 salah : ".mysql_error());
	$nomor = 0; 
	while ($myData = mysql_fetch_array($myQry)) {
		$nomor++;
	?>
  <tr>
    <td><?php echo $nomor; ?></td>
    <td><?php echo $myData['kd_penerbit']; ?></td>
    <td><?php echo $myData['nm_penerbit']; ?></td>
  </tr>
  <?php } ?>
</table>
<img src="../images/btn_print.png" height="20" onClick="javascript:window.print()" />
</body>
</html>