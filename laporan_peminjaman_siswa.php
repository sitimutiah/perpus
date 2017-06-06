<?php
include_once "library/inc.seslogin.php";
include_once "library/inc.connection.php";

# Membaca form filter Siswa
$kodeSiswa	= isset($_GET['kodeSiswa']) ? $_GET['kodeSiswa'] : 'Semua'; // Baca dari URL 
$kodeSiswa 	= isset($_POST['cmbSiswa']) ? $_POST['cmbSiswa'] : $kodeSiswa; // Baca dari form Submit 

# Membuat Filter Siswa
if($kodeSiswa=="Semua") {
	// Jika memilih siswa
	$filterSQL = "";
}
else {
	$filterSQL = "WHERE kd_siswa ='$kodeSiswa'";
}

# UNTUK PAGING (PEMBAGIAN HALAMAN)
$baris	= 50;
$hal 	= isset($_GET['hal']) ? $_GET['hal'] : 1;
$pageSql= "SELECT * FROM peminjaman $filterSQL";
$pageQry= mysql_query($pageSql, $koneksidb) or die ("error paging: ".mysql_error());
$jumlah	= mysql_num_rows($pageQry);
$maks	= ceil($jumlah/$baris);
$mulai	= $baris * ($hal-1); 
?>
<h2>LAPORAN  PEMINJAMAN PER SISWA </h2>
<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" name="form1" target="_self">
  <table width="500" border="0"  class="table-list">
    
    <tr>
      <td colspan="3" bgcolor="#CCCCCC"><strong>FILTER DATA </strong></td>
    </tr>
    <tr>
      <td width="113"><strong>Siswa</strong></td>
      <td width="5"><strong>:</strong></td>
      <td width="368"><select name="cmbSiswa">
          <option value="Semua">....</option>
          <?php
		  // Skrip menampilkan data Siswa ke ComboBo (ListMenu)
	  $bacaSql = "SELECT * FROM siswa ORDER BY kd_siswa";
	  $bacaQry = mysql_query($bacaSql, $koneksidb) or die ("Gagal Query".mysql_error());
	  while ($bacaData = mysql_fetch_array($bacaQry)) {
		if ($bacaData['kd_siswa'] == $kodeSiswa) {
			$cek = " selected";
		} else { $cek=""; }
		
		echo "<option value='$bacaData[kd_siswa]' $cek> $bacaData[nisn] - $bacaData[nm_siswa]</option>";
	  }
	  ?>
        </select>
        <input name="btnTampil" type="submit" value=" Tampilkan " /></td>
    </tr>
  </table>
</form>

<table class="table-list" width="800" border="0" cellspacing="1" cellpadding="2">
  <tr>
    <td width="22" bgcolor="#CCCCCC"><strong>No</strong></td>
    <td width="75" bgcolor="#CCCCCC"><strong>No. Pinjam </strong></td>
    <td width="75" bgcolor="#CCCCCC"><strong>Tgl. Pinjam </strong></td>
    <td width="70" bgcolor="#CCCCCC"><strong>Lama Pjm </strong></td>
    <td width="201" bgcolor="#CCCCCC"><strong>Keterangan</strong></td>
    <td width="65" bgcolor="#CCCCCC"><strong>Jml Buku</strong> </td>
    <td width="42" bgcolor="#CCCCCC"><strong>Status</strong></td>
    <td width="104" bgcolor="#CCCCCC"><strong>Tgl Kembali </strong></td>
    <td width="100" align="right" bgcolor="#CCCCCC"><strong>Denda (Rp.) </strong></td>
  </tr>
  <?php 
	// Skrip menampilkan data Peminjaman dengan filter Siswa
	$mySql = "SELECT * FROM peminjaman $filterSQL ORDER BY no_pinjam ASC LIMIT $mulai, $baris";
	$myQry = mysql_query($mySql, $koneksidb)  or die ("Query 1 salah : ".mysql_error());
	$nomor = $mulai;  
	while ($myData = mysql_fetch_array($myQry)) {
		$nomor++;		
		$Kode = $myData['no_pinjam']; 
		
		// Informasi pengembalian
		$infoSql = "SELECT * FROM pengembalian WHERE no_pinjam='$Kode'";
		$infoQry = mysql_query($infoSql, $koneksidb)  or die ("Query 2 salah : ".mysql_error());
		$infoData= mysql_fetch_array($infoQry);
		if(mysql_num_rows($infoQry) >= 1) {
			$tanggalKembali	= IndonesiaTgl($infoData['tgl_kembali']);
			$totalDenda		= format_angka($infoData['denda']);
		}
		else {
			$tanggalKembali	= "-";
			$totalDenda		= "0";
		}

		// Informasi jumlah buku
		$info2Sql = "SELECT SUM(jumlah) As jumlah FROM peminjaman_detil WHERE no_pinjam='$Kode'";
		$info2Qry = mysql_query($info2Sql, $koneksidb)  or die ("Query 3 salah : ".mysql_error());
		$info2Data= mysql_fetch_array($info2Qry);
	?>
  <tr>
    <td><?php echo $nomor; ?></td>
    <td><?php echo $myData['no_pinjam']; ?></td>
    <td><?php echo IndonesiaTgl($myData['tgl_pinjam']); ?></td>
    <td><?php echo $myData['lama_pinjam']; ?> hr</td>
    <td><?php echo $myData['keterangan']; ?></td>
    <td align="right"><?php echo $info2Data['jumlah']; ?></td>
    <td><?php echo $myData['status']; ?></td>
    <td><?php echo $tanggalKembali; ?></td>
    <td align="right"><?php echo $totalDenda; ?></td>
  </tr>
  <?php } ?>
  <tr>
    <td colspan="3"><strong>Jumlah Data : </strong><?php echo $jumlah; ?></td>
    <td colspan="6" align="right"><strong>Halaman ke :</strong>
	<?php
	for ($h = 1; $h <= $maks; $h++) {
		echo " <a href='?open=Laporan-Peminjaman-Siswa&hal=$h&kodeSiswa=$kodeSiswa'>$h</a> ";
	}
	?></td>
  </tr>
</table>
