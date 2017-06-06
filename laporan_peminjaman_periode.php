<?php
include_once "library/inc.seslogin.php";
include_once "library/inc.connection.php";

# Membaca tanggal dari form, jika belum di-POST formnya, maka diisi dengan tanggal sekarang
$tanggal_1 	= isset($_GET['tanggal_1']) ? $_GET['tanggal_1'] : "01-".date('m-Y');
$tanggal_1 	= isset($_POST['cmbTanggal_1']) ? $_POST['cmbTanggal_1'] : $tanggal_1;
$tanggal_11 	= InggrisTgl($tanggal_1);

$tanggal_2 	= isset($_GET['tanggal_2']) ? $_GET['tanggal_2'] : date('d-m-Y'); 
$tanggal_2 	= isset($_POST['cmbTanggal_2']) ? $_POST['cmbTanggal_2'] : $tanggal_2;
$tanggal_22 	= InggrisTgl($tanggal_2);

// Membuat sub SQL filter data berdasarkan 2 tanggal (periode)
$filterSQL = " WHERE ( peminjaman.tgl_pinjam BETWEEN '$tanggal_11' AND '$tanggal_22') ";

# UNTUK PAGING (PEMBAGIAN HALAMAN)
$baris	= 50;
$hal 	= isset($_GET['hal']) ? $_GET['hal'] : 1;
$infoSql= "SELECT * FROM peminjaman $filterSQL";
$infoQry= mysql_query($infoSql, $koneksidb) or die ("error paging: ".mysql_error());
$jumlah	= mysql_num_rows($infoQry);
$maks	= ceil($jumlah/$baris);
$mulai	= $baris * ($hal-1); 
?>
<h2>LAPORAN  PEMINJAMAN PER PERIODE </h2>
<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" name="form1" target="_self">
  <table width="550" border="0"  class="table-list">
    <tr>
      <td colspan="3" bgcolor="#CCCCCC"><strong>FILTER DATA </strong></td>
    </tr>
    <tr>
      <td width="87"><strong>Periode </strong></td>
      <td width="5"><strong>:</strong></td>
      <td width="444"><input name="cmbTanggal_1" type="text" class="tcal" value="<?php echo $tanggal_1; ?>" />
        s/d
      <input name="cmbTanggal_2" type="text" class="tcal" value="<?php echo $tanggal_2; ?>" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input name="btnTampil" type="submit" value=" Tampilkan " /></td>
    </tr>
  </table>
</form>

<table class="table-list" width="800" border="0" cellspacing="1" cellpadding="2">
  <tr>
    <td width="23" bgcolor="#CCCCCC"><strong>No</strong></td>
    <td width="75" bgcolor="#CCCCCC"><strong>No. Pinjam </strong></td>
    <td width="75" bgcolor="#CCCCCC"><strong>Tgl. Pinjam </strong></td>
    <td width="75" bgcolor="#CCCCCC"><strong>NIS</strong></td>
    <td width="182" bgcolor="#CCCCCC"><strong>Nama Siswa </strong></td>
    <td width="70" bgcolor="#CCCCCC"><strong>Lama Pjm </strong></td>
    <td width="217" bgcolor="#CCCCCC"><strong>Keterangan</strong></td>
    <td width="42" bgcolor="#CCCCCC"><strong>Status</strong></td>
  </tr>
  <?php 
	// Skrip menampilkan data Peminjaman dengan filter Periode
	$mySql = "SELECT peminjaman.*, siswa.nisn, siswa.nm_siswa, user.nm_user 
				FROM peminjaman 
				LEFT JOIN siswa ON peminjaman.kd_siswa = siswa.kd_siswa
				LEFT JOIN user ON peminjaman.kd_user = user.kd_user
				$filterSQL
				ORDER BY peminjaman.no_pinjam ASC LIMIT $mulai, $baris";
	$myQry = mysql_query($mySql, $koneksidb)  or die ("Query salah : ".mysql_error());
	$nomor = $mulai;  
	while ($myData = mysql_fetch_array($myQry)) {
		$nomor++;		
		$noPinjam = $myData['no_pinjam']; 
	?>
  <tr>
    <td><?php echo $nomor; ?></td>
    <td><?php echo $myData['no_pinjam']; ?></td>
    <td><?php echo IndonesiaTgl($myData['tgl_pinjam']); ?></td>
    <td><?php echo $myData['nisn']; ?></td>
    <td><?php echo $myData['nm_siswa']; ?></td>
    <td><?php echo $myData['lama_pinjam']; ?></td>
    <td><?php echo $myData['keterangan']; ?></td>
    <td><?php echo $myData['status']; ?></td>
  </tr>
  <?php } ?>
  <tr>
    <td colspan="3"><strong>Jumlah Data : </strong><?php echo $jumlah; ?></td>
    <td colspan="5" align="right"><strong>Halaman ke :</strong>
    <?php
	for ($h = 1; $h <= $maks; $h++) {
		echo " <a href='?open=Laporan-Peminjaman-Periode&hal=$h&tanggal_1=$tanggal_1&tanggal_2=$tanggal_2'>$h</a> ";
	}
	?></td>
  </tr>
</table>
