<?php
include_once "library/inc.seslogin.php";
include_once "library/inc.connection.php";
include_once "library/inc.library.php";

# UNTUK PAGING (PEMBAGIAN HALAMAN)
$baris	= 50;
$hal 	= isset($_GET['hal']) ? $_GET['hal'] : 1;
$infoSql= "SELECT * FROM siswa";
$infoQry= mysql_query($infoSql, $koneksidb) or die ("error paging: ".mysql_error());
$jumlah	= mysql_num_rows($infoQry);
$maks	= ceil($jumlah/$baris);
$mulai	= $baris * ($hal-1); 
?><table width="900" border="0" cellpadding="2" cellspacing="1" class="table-border">
  <tr>
    <td colspan="2"><h1><b> DATA SISWA </b></h1></td>
  </tr>
  <tr>
    <td colspan="2" align="right"><a href="?open=Siswa-Add" target="_self"><img src="images/btn_add_data.png" height="30" border="0" /></a></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">
	<table class="table-list" width="100%" border="0" cellspacing="1" cellpadding="2">
      <tr>
        <th width="22" align="center">No</th>
        <th width="43">Kode</th>
        <th width="69">NISN</th>
        <th width="170">Nama Siswa</th>
        <th width="34">L/P </th>
        <th width="280">Alamat</th>
        <th width="104">No. Telepon </th>
        <td colspan="3" align="center" bgcolor="#CCCCCC"><strong>Tools</strong></td>
      </tr>
	<?php
	// Skrip menampilkan data Siswa ke layar
	$mySql = "SELECT * FROM siswa ORDER BY kd_siswa ASC LIMIT $mulai, $baris";
	$myQry = mysql_query($mySql, $koneksidb)  or die ("Query salah : ".mysql_error());
	$nomor  = $mulai; 
	while ($myData = mysql_fetch_array($myQry)) {
		$nomor++;
		$Kode = $myData['kd_siswa'];
	?>
      <tr>
        <td> <?php echo $nomor; ?> </td>
        <td> <?php echo $myData['kd_siswa']; ?> </td>
        <td> <?php echo $myData['nisn']; ?> </td>
        <td> <?php echo $myData['nm_siswa']; ?> </td>
        <td> <?php echo $myData['kelamin']; ?> </td>
        <td> <?php echo $myData['alamat']; ?> </td>
         <td> <?php echo $myData['no_telepon']; ?> </td>
        <td width="40" align="center"><a href="?open=Siswa-Delete&Kode=<?php echo $Kode; ?>" target="_self" alt="Delete Data" onclick="return confirm('ANDA YAKIN AKAN MENGHAPUS DATA SISWA INI ... ?')">Delete</a></td>
         <td width="41" align="center"><a href="?open=Siswa-Edit&Kode=<?php echo $Kode; ?>" target="_self" alt="Edit Data">Edit</a></td>
         <td width="40" align="center"><a href="cetak/siswa_cetak.php?Kode=<?php echo $Kode; ?>" target="_blank">Cetak</a></td>
      </tr>
      <?php } ?>
    </table></td>
  </tr>
  <tr class="selKecil">
    <td width="365"><b>Jumlah Data :</b> <?php echo $jumlah; ?> </td>
    <td width="424" align="right"><strong>Halaman ke :</strong>
    <?php
	for ($h = 1; $h <= $maks; $h++) {
		echo " <a href='?open=Siswa-Data&hal=$h'>$h</a> ";
	}
	?> </td>
  </tr>
</table>
