<?php
include_once "library/inc.seslogin.php";
include_once "library/inc.connection.php";
include_once "library/inc.library.php";
include_once "library/inc.pilihan.php";

# Bulan terpilih dari Form dan URL
$bulan		= isset($_GET['bulan']) ? $_GET['bulan'] : date('m'); // Baca dari URL, jika tidak ada diisi bulan sekarang
$dataBulan 	= isset($_POST['cmbBulan']) ? $_POST['cmbBulan'] : $bulan; // Baca dari form Submit, jika tidak ada diisi dari $bulan

# Tahun terpilih dari Form dan URL
$tahun	   	= isset($_GET['tahun']) ? $_GET['tahun'] : date('Y'); // Baca dari URL, jika tidak ada diisi tahun sekarang
$dataTahun 	= isset($_POST['cmbTahun']) ? $_POST['cmbTahun'] : $tahun; // Baca dari form Submit, jika tidak ada diisi dari $tahun

# Membuat Filter Bulan
if($dataTahun and $dataBulan) {
	if($dataBulan == "00") {
		// Jika tidak memilih bulan
		$filterSQL = "WHERE LEFT(tgl_pinjam,4)='$dataTahun'";
	}
	else {
		// Jika memilih bulan dan tahun
		$filterSQL = "WHERE LEFT(tgl_pinjam,4)='$dataTahun' AND MID(tgl_pinjam,6,2)='$dataBulan'";
	}
}
else {
	$filterSQL = "";
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
<h2>LAPORAN  PEMINJAMAN PER BULAN </h2>
<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" name="form1" target="_self">
  <table width="500" border="0"  class="table-list">
    
    <tr>
      <td colspan="3" bgcolor="#CCCCCC"><strong>FILTER DATA </strong></td>
    </tr>
    <tr>
      <td><strong>Periode Bulan </strong></td>
      <td><strong>:</strong></td>
      <td><select name="cmbBulan">
        <?php
		// Membuat daftar bulan 1 -12, nama bulan membaca di file inc.pilihan.php
		for($bulan = 1; $bulan <= 12; $bulan++) {
			// Skrip membuat angka 2 digit (1-9)
			if($bulan < 10) { $bln = "0".$bulan; } else { $bln = $bulan; }
			
			if ($bln == $dataBulan) { $cek=" selected"; } else { $cek = ""; }
			
			echo "<option value='$bln' $cek> $listBulan[$bln] </option>";
		}
		?>
      </select>
        <select name="cmbTahun">
          <?php
		  $$awal_th	= date('Y') - 3;
		  for($tahun = $$awal_th; $tahun <= date('Y'); $tahun++) {
			// Skrip tahun terpilih
			if ($tahun == $dataTahun) {  $cek=" selected"; } else { $cek = ""; }
			
			echo "<option value='$tahun' $cek> $tahun </option>";
		  }
		  ?>
        </select></td>
    </tr>
    <tr>
      <td width="130">&nbsp;</td>
      <td width="5">&nbsp;</td>
      <td width="351"><input name="btnTampil" type="submit" value=" Tampilkan " /></td>
    </tr>
  </table>
</form>

<table class="table-list" width="800" border="0" cellspacing="1" cellpadding="2">
  <tr>
    <td width="23" bgcolor="#CCCCCC"><strong>No</strong></td>
    <td width="75" bgcolor="#CCCCCC"><strong>No. Pinjam </strong></td>
    <td width="75" bgcolor="#CCCCCC"><strong>Tgl. Pinjam </strong></td>
    <td width="75" bgcolor="#CCCCCC"><strong>NIS</strong></td>
    <td width="182" bgcolor="#CCCCCC"><strong>Nama Siswa  </strong></td>
    <td width="70" bgcolor="#CCCCCC"><strong>Lama Pjm </strong></td>
    <td width="217" bgcolor="#CCCCCC"><strong>Keterangan</strong></td>
    <td width="42" bgcolor="#CCCCCC"><strong>Status</strong></td>
  </tr>
  <?php 
	// Skrip menampilkan data Peminjaman dengan filter Bulan dan Tahun
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
		echo " <a href='?open=Laporan-Peminjaman-Bulan&hal=$h&bulan=$dataBulan&tahun=$dataTahun'>$h</a> ";
	}
	?></td>
  </tr>
</table>
