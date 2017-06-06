<?php
include_once "library/inc.connection.php";
?>
<h2> LAPORAN DATA KATEGORI </h2>
<table class="table-list" width="700" border="0" cellspacing="1" cellpadding="2">
  <tr>
    <td width="20" align="center" bgcolor="#CCCCCC"><b>No</b></td>
    <td width="42" bgcolor="#CCCCCC"><strong>Kode</strong></td>
    <td width="543" bgcolor="#CCCCCC"><b>Nama Kategori </b></td>
    <td width="74" bgcolor="#CCCCCC"><strong>Koleksi</strong></td>
  </tr>
  <?php
    // Skrip menampilkan data Kategori
	$mySql 	= "SELECT * FROM kategori ORDER BY kd_kategori";
	$myQry 	= mysql_query($mySql, $koneksidb)  or die ("Query salah : ".mysql_error());
	$nomor  = 0; 
	while ($myData = mysql_fetch_array($myQry)) {
		$nomor++;
		
		// Menghitung jumlah Koleksi Buku per Kategori
		$Kode	 = $myData['kd_kategori'];
		$infoSql = "SELECT COUNT(*) As koleksi FROM buku WHERE kd_kategori='$Kode'";
		$infoQry = mysql_query($infoSql, $koneksidb);
		$infoData= mysql_fetch_array($infoQry);
	?>
  <tr>
    <td> <?php echo $nomor; ?> </td>
    <td> <?php echo $myData['kd_kategori']; ?> </td>
    <td> <?php echo $myData['nm_kategori']; ?> </td>
    <td> <?php echo $infoData['koleksi']; ?> </td>
  </tr>
  <?php } ?>
</table>
<a href="cetak/kategori.php" target="_blank"><img src="images/btn_print2.png" height="18" border="0" title="Cetak ke Format HTML"/></a>
