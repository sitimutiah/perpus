<?php
include_once "library/inc.connection.php";
?>
<h2> LAPORAN DATA PENERBIT</h2>
<table class="table-list" width="700" border="0" cellspacing="1" cellpadding="2">
  <tr>
    <td width="20" align="center" bgcolor="#CCCCCC"><b>No</b></td>
    <td width="50" bgcolor="#CCCCCC"><strong>Kode</strong></td>
    <td width="614" bgcolor="#CCCCCC"><b>Nama Penerbit </b></td>
  </tr>
  <?php
    // Skrip menampilkan data Penerbit
	$mySql 	= "SELECT * FROM penerbit ORDER BY kd_penerbit";
	$myQry 	= mysql_query($mySql, $koneksidb)  or die ("Query salah : ".mysql_error());
	$nomor  = 0; 
	while ($myData = mysql_fetch_array($myQry)) {
		$nomor++;
	?>
  <tr>
    <td> <?php echo $nomor; ?> </td>
    <td> <?php echo $myData['kd_penerbit']; ?> </td>
    <td> <?php echo $myData['nm_penerbit']; ?> </td>
  </tr>
  <?php } ?>
</table>
<a href="cetak/penerbit.php" target="_blank"><img src="images/btn_print2.png" height="18" border="0" title="Cetak ke Format HTML"/></a>
