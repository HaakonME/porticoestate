<!-- $Id: day.tpl,v 1.2 2006/12/28 04:28:01 skwashd Exp $ -->
<!-- BEGIN day -->
<table border="0" width="100%">
 <tr>
  <td valign="top" width="70%">
   <tr>
    <td>
     <table border="0" width=100%>
      <tr>
       <td align="middle">
        <font size="+2" color="{bg_text}"><b>{date}</b></font><br />
        <font size="+1" color="{bg_text}">{username}</font>
       </td>
      </tr>
     </table>
     <table border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="{bg_text}">
      {day_events}
     </table>
    </td>
    <td align="right">
     {small_calendar}
    </td>
   </tr>
  </td>
 </tr>
</table>
<!-- END day -->
<!-- BEGIN day_event -->
      <tr>
       <td>
        {daily_events}
       </td>
      </tr>
<!-- END day_event -->

