<?php
include_once "library/inc.seslogin.php";
include_once "library/inc.connection.php";
?>
<table width="800" border="0" cellpadding="2" cellspacing="1" class="table-border">
  <tr>
    <td width="789" colspan="2"><h1><b>DATA  PENERBIT</b></h1></td>
  </tr>
  <tr>
    <td colspan="2" align="right"><a href="?open=Penerbit-Add" target="_self"><img src="images/btn_add_data.png" height="30" border="0"  /></a></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">
	<table class="table-list"  width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <th width="4%">No</th>
        <th width="7%">Kode</th>
        <th width="75%">Nama Penerbit</th>
        <td colspan="2" align="center" bgcolor="#CCCCCC"><strong>Tools</strong></td>
        </tr>
      <?php
	  // Menampilkan data Penerbit
	$mySql = "SELECT * FROM penerbit ORDER BY kd_penerbit ASC";
	$myQry = mysql_query($mySql, $koneksidb)  or die ("Query salah : ".mysql_error());
	$nomor = 0; 
	while ($myData = mysql_fetch_array($myQry)) {
		$nomor++;
		$Kode = $myData['kd_penerbit'];
	?>
      <tr>
        <td> <?php echo $nomor; ?> </td>
        <td> <?php echo $myData['kd_penerbit']; ?> </td>
        <td> <?php echo $myData['nm_penerbit']; ?> </td>
        <td width="7%" align="center"><a href="?open=Penerbit-Delete&Kode=<?php echo $Kode; ?>" target="_self" onclick="return confirm('YAKIN INGIN MENGHAPUS DATA PENERBIT INI ... ?')">Delete</a></td>
        <td width="7%" align="center"><a href="?open=Penerbit-Edit&Kode=<?php echo $Kode; ?>" target="_self">Edit</a></td>
      </tr>
	<?php } ?>
    </table></td>
  </tr>
</table>

