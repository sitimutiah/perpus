<?php
include_once "library/inc.seslogin.php";
include_once "library/inc.connection.php";
?>
<table width="800" border="0" cellpadding="2" cellspacing="1" class="table-border">
  <tr>
    <td colspan="2"><h1><b> DATA USER </b></h1></td>
  </tr>
  <tr>
    <td colspan="2" align="right"><a href="?open=User-Add" target="_self"><img src="images/btn_add_data.png" height="30" border="0" /></a></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><table class="table-list" width="100%" border="0" cellspacing="1" cellpadding="2">
      <tr>
        <th width="23">No</th>
        <th width="50">Kode</th>
        <th width="480">Nama User </th>
        <th width="120">Username</th>
        <td colspan="2" align="center" bgcolor="#CCCCCC"><b>Tools</b><b></b></td>
        </tr>
      <?php
	  // Skrip menampilkan data User ke layar
	$mySql 	= "SELECT * FROM user ORDER BY kd_user ASC";
	$myQry 	= mysql_query($mySql, $koneksidb)  or die ("Query  salah : ".mysql_error());
	$nomor  = 0; 
	while ($myData = mysql_fetch_array($myQry)) {
		$nomor++;
		$Kode = $myData['kd_user'];
	?>
      <tr>
        <td> <?php echo $nomor; ?> </td>
        <td> <?php echo $myData['kd_user']; ?> </td>
        <td> <?php echo $myData['nm_user']; ?> </td>
        <td> <?php echo $myData['username']; ?> </td>
        <td width="45" align="center"><a href="?open=User-Delete&Kode=<?php echo $Kode; ?>" target="_self" alt="Delete Data" onclick="return confirm('ANDA YAKIN AKAN MENGHAPUS USER INI ... ?')">Delete</a></td>
        <td width="45" align="center"><a href="?open=User-Edit&Kode=<?php echo $Kode; ?>" target="_self" alt="Edit Data">Edit</a></td>
      </tr>
      <?php } ?>
    </table>    </td>
  </tr>
</table>

