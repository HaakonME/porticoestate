<?php
$phpgw_info["flags"]["currentapp"] = "rbs";	
include "config.inc";
include "functions.inc";
include "connect.inc";
include "mrbs_auth.inc";
include "mrbs_sql.inc";

if(!getAuthorised(getUserName(), getUserPassword()))
{
?>
<HTML>
 <HEAD>
  <META HTTP-EQUIV="REFRESH" CONTENT="5; URL=index.php">
  <TITLE><?php echo $lang[mrbs]?></TITLE>
  <?php include "config.inc"?>
 <BODY>
  <H1><?php echo $lang[accessdenied]?></H1>
  <P>
   <?php echo $lang[unandpw]?>
  </P>
  <P>
  <?php
  	echo "<a href=".$phpgw->link($HTTP_REFERER).">$lang[returnprev]</a>"; 
  ?>
  </P>
</HTML>
<?php
	exit;
}

if(!getWritable($create_by, getUserName())) { ?>
<HTML>
<HEAD>
<TITLE><?php echo $lang[mrbs]?></TITLE>

<H1><?php echo $lang[accessdenied]?></H1>
<P>
  <?php echo $lang[norights]?>
</P>
<P>
  <?php
  	echo "<a href=".$phpgw->link($HTTP_REFERER).">$lang[returnprev]</a>"; 
  ?>
</P>
</BODY>
</HTML>
<?php exit; }

// Units start in seconds
$units = 1.0;

switch($dur_units)
{
	case "years":
		$units *= 52;
	case "weeks":
		$units *= 7;
	case "days":
		$units *= 24;
	case "hours":
		$units *= 60;
	case "minutes":
		$units *= 60;
	case "seconds":
		break;
}

// Units are now in "$dur_units" numbers of seconds

if($all_day == "yes")
{
	$starttime = mktime(0, 0, 0, $month, $day  , $year);
	$endtime   = mktime(0, 0, 0, $month, $day+1, $year);
}
else
{
	$starttime = mktime($hour, $minute, 0, $month, $day, $year);
	$endtime   = mktime($hour, $minute, 0, $month, $day, $year) + ($units * $duration);
	
	$round_up = 30 * 60;
	$diff     = $endtime - $starttime;
	
	if($tmp = $diff % $round_up)
		$endtime += $round_up - $tmp;
}

// Get the repeat entry settings
$rep_enddate = mktime(0, 0, 0, $rep_end_month, $rep_end_day, $rep_end_year);

switch($rep_type)
{
	case 2:
		$rep_opt  = $rep_day[0] ? "1" : "0";
		$rep_opt .= $rep_day[1] ? "1" : "0";
		$rep_opt .= $rep_day[2] ? "1" : "0";
		$rep_opt .= $rep_day[3] ? "1" : "0";
		$rep_opt .= $rep_day[4] ? "1" : "0";
		$rep_opt .= $rep_day[5] ? "1" : "0";
		$rep_opt .= $rep_day[6] ? "1" : "0";
		break;
	
	default:
		$rep_opt = "";
}

# first check for any schedule conflicts
# we ask the db if there is anything which
#   starts before this and ends after the start
#   or starts between the times this starts and ends
#   where the room is the same

$reps = mrbsGetRepeatEntryList($starttime, $rep_enddate, $rep_type, $rep_opt, $max_rep_entrys);
if(!empty($reps))
{
	if(count($reps) < $max_rep_entrys)
	{
		$diff = $endtime - $starttime;
		
		for($i = 0; $i < count($reps); $i++)
		{
			$tmp = mrbsCheckFree($room_id, $reps[$i], $reps[$i] + $diff, $id);
			
			if(!empty($tmp))
				$err = $err . $tmp;
		}
	}
	else
	{
		$err        = $lang[too_may_entrys] . "<P>";
		$hide_title = 1;
	}
}
else
	$err = mrbsCheckFree($room_id, $starttime, $endtime-1, $id);

if(empty($err))
{
	if($edit_type == "series")
	{
		mrbsCreateRepeatingEntrys($starttime, $endtime,   $rep_type, $rep_enddate, $rep_opt, 
		                          $room_id,   $create_by, $name,     $type,        $description);
	}
	else
	{
		$res = mysql_query("SELECT repeat_id FROM mrbs_entry WHERE id='$id'");
		if(mysql_num_rows($res) > 0)
		{
			$row = mysql_fetch_row($res);
			$repeat_id  = $row[0];
			$entry_type = 2;
		}
		else
			$repeat_id = $entry_type = 0;
		
		// Create the entrys, ignoring any errors at the moment
		if(mrbsCreateSingleEntry($starttime, $endtime, $entry_type, $repeat_id, $room_id,
		                         $create_by, $name, $type, $description))
		{
			
		}
	}
	
	# Delete the original entry
	if($id)
		mrbsDelEntry(getUserName(), $id, ($edit_type == "series"), 0);
	
	$area = mrbsGetRoomArea($room_id);
	
	# Now its all done go back to the day view
	 echo "<a href=".$phpgw->link("/rbs/day.php","year=$year&month=$month&day=$day&area=$area").">Back</a>";
        $phpgw->common->phpgw_footer();	
	exit;
}

?>
<HTML>
<HEAD><TITLE><?php echo $lang[mrbs]?></TITLE>
</HEAD>
<BODY>

<?php

if(strlen($err))
{
	echo "<H2>" . $lang[sched_conflict] . "</H2>";
	if(!$hide_title)
	{
		echo $lang[conflict];
		echo "<UL>";
	}
	
	echo $err;
	
	if(!$hide_title)
		echo "</UL>";
}

# What the FUCK is returl? (Stephan)
#echo "<a href=$returl>$lang[returncal]</a><p>";
# Well, I'm going to replace it with a standard Return Prev. Link
# (Bryan)
echo "<a href=".$phpgw->link($HTTP_REFERER).">$lang[returnprev]</a>"; 


include "trailer.inc"; ?>

</BODY>
</HTML>
<?php $phpgw->common->phpgw_footer(); ?>	
