<!-- BEGIN list -->
<p>
 <div align="center">
  <table border="0" width="70%">
   <tr bgcolor="{th_bg}">
    <td>{lang_loginid}</td>
    <td>{lang_lastname}</td>
    <td>{lang_firstname}</td>
    <td>{lang_edit}</td>
<!--    <td>{lang_view}</td> -->
   </tr>

   {rows}

  </table>
 </div>
<!-- END list -->

<!-- BEGIN row -->
   <tr bgcolor="{tr_color}">
    <td>{row_loginid}</td>
    <td>{row_lastname}</td>
    <td>{row_firstname}</td>
    <td width="5%">{row_edit}</td>
<!--    <td width="5%">{row_view}</td> -->
   </tr>
<!-- END row -->


<!-- BEGIN row_empty -->
   <tr>
    <td colspan="5" align="center">{message}</td>
   </tr>
<!-- END row_empty -->
