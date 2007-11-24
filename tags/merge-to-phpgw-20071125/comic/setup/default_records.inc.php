<?php
  /**************************************************************************\
  * phpGroupWare - Setup                                                     *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: default_records.inc.php,v 1.4 2004/07/20 23:51:55 skwashd Exp $ */

	$oProc->query("INSERT INTO phpgw_comic_data VALUES (1,'T','userfriendly','Illiad','User Friendly','',0,0,'http://www.userfriendly.org/static/','http://www.userfriendly.org','/cartoons/archives/{y}{Ml}/{Ymd}.html','/cartoons/archives/{y}{Ml}/uf[0-9]*.gif','','Su:Mo:Tu:We:Th:Fr:Sa','None','Geek',1,'Remote',0,720,720)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (2,'T','dilbert','Scott Adams','Dilbert','',0,0,'http://www.dilbert.com/','http://www.comics.com','/comics/dilbert/index.html','/comics/dilbert/archive/images/dilbert[0-9]*.gif','','Su:Mo:Tu:We:Th:Fr:Sa','None','Geek',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (3,'T','garfield','Jim Davis','Garfield','ga',0,0,'http://www.garfield.com/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (4,'T','foxtrot','Bill Amend','FoxTrot','ft',0,0,'http://www.foxtrot.com/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (5,'T','Babyblues','Jerry Scott & Rick Kirkman','Baby Blues','baby',0,144,'http://www.babyblues.com/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Toonville','General',0,'Remote',0,585,585)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (6,'T','tumblewd','Tom K. Ryan','Tumbleweeds','',0,0,'http://www.tumbleweeds.com/','http://www.tumbleweeds.com/images/{day}.gif','','','','Su:Mo:Tu:We:Th:Fr:Sa','None','General',0,'Static',0,525,525)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (7,'T','jerkcity','TBD','JerkCity','',0,0,'http://www.jerkcity.com/','http://www.jerkcity.com/today.gif','','','','Su:Mo:Tu:We:Th:Fr:Sa','None','Geek',2,'Static',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (8,'T','slagoon','Jim Toomey','Shermans Lagoon','',0,0,'http://www.slagoon.com/','http://www.slagoon.com/dailies/SL{y}.{m}.{d}.gif','','','','Su:Mo:Tu:We:Th:Fr:Sa','None','General',0,'Static',0,576,650)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (9,'T','blondie','Dean Young and Denis Lebrun','Blondie','',0,0,'http://www.blondie.com/','http://www.blondie.com/dailies/blondie.{Ymd}.gif','','','','Su:Mo:Tu:We:Th:Fr:Sa','None','General',0,'Static',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (10,'T','sluggy','Pete Abrams','Sluggy Freelance','',0,0,'http://www.sluggy.com/','http://pics.sluggy.com/comics/{ymd}a.gif','','','','Su:Mo:Tu:We:Th:Fr:Sa','None','Geek',0,'Static',0,700,700)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (11,'T','ripleys','Don Wimmer and Karen Kemlo','Ripleys Believe It Or Not','',0,0,'','','','','','Mo:Tu:We:Th:Fr:Sa','United','General',0,'Remote',0,300,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (12,'T','buckets','Scott Stantis','Buckets','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','United','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (13,'T','fatcats','Charlie Podrebarac','Fat Cats','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','United','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (14,'T','getfuzzy','Darby Conley','Get Fuzzy','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','United','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (15,'T','topofworld','Mark Tonra','Top Of This World','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','United','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (16,'T','meatloaf','Mark Buford','Meatloaf Night','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','United','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (17,'T','tarzan','Gray Morrow','Tarzan','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','United','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (18,'F','spidermn','Stan Lee','The Amazing Spider-Man','Spiderman',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (19,'F','apt3g','Brian Kotzky & Lisa Trusiani','Apartment 3-G','Apartment_3-G',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (20,'F','bgoogle','Fred Lasswell','Barney Google and Snuffy Smith','Barney_Google',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (21,'F','bbailey','Mort Walker','Beetle Bailey','Beetle_Bailey',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (22,'F','bet_half','Randy Glasbergen','The Better Half','Better_Half',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,249,249)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (23,'F','bfriends','Sandra Bell-Lundy','Between Friends','Between_Friends',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (24,'F','bonerark','Frank Johnson','Boners Ark','bot',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (25,'F','bfather','Frank Johnson','Bringing Up Father','brt',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (26,'F','buckles','David Gilbert','Buckles','Buckles',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (27,'F','claire','Doug Shannon','Claire and Weber','cwt',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (28,'F','crock','Bill Rechin & Don Wilder','Crock','Crock',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (29,'T','curtis','Ray Billingsley','Curtis','curt',0,104,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Toonville','General',0,'Remote',0,585,585)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (30,'F','dennis','Hank Ketcham','Dennis The Menace','Dennis_The_Menace',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,251,251)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (31,'F','dinette','Julie Larson','Dinette Set','Dinette_Set',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,251,251)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (32,'T','familycircus','Bil Keane','The Family Circus','fam',0,109,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Toonville','General',0,'Remote',0,250,250)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (33,'F','fgordon','Jim Keefe','Flash Gordon','fg',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (34,'T','Funky','Tom Batiuk','Funky Winkerbean','funk',0,124,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Toonville','General',0,'Remote',0,585,585)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (35,'F','grinbear','Fred Wagner & Ralph Dunagin','Grin and Bear It','Grin_and_Bear_It',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,250,250)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (36,'F','hagar','Chris Browne','Hagar the Horrible','Hagar_The_Horrible',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (37,'F','hazel','Ted Key','Hazel','hat',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,243,243)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (38,'F','henry','TBD','Henry','het',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (39,'F','horrscpe','Susan Kelso & Carol Kemp','Horrorscope','hrt',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,240,240)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (40,'F','needhelp','Vic Lee','I Need Help','I_Need_Help',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,263,263)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (41,'F','jparker','Woody Wilson & Harold LeDoux','Judge Parker','Judge_Parker',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (42,'F','katzkids','Hy Eisman','Katzenjammer Kids','kk',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (43,'F','lockhorn','Bunny Hoest & John Reiner','The Lockhorns','Lockhorns',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,249,249)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (44,'F','mallard','Bruce Tinsley','Mallard Fillmore','Mallard_Fillmore',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','Political',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (45,'F','mandrake','Lee Falk & Fred Fredericks','Mandrake the Magician','mmt',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (46,'F','mtrail','Jack Elrod','Mark Trail','Mark_Trail',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (47,'F','marvin','Tom Armstrong','Marvin','Marvin',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (48,'F','mmiller','Bob Weber Sr','Moose Miller','mot',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (49,'F','mutts','Patrick McDonnell','Mutts','Mutts',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (50,'F','fastrack','Bill Holbrook','On The Fastrack','Fast_Track',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (51,'F','ernie','Bud Grace','The Piranha Club','Piranha',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (52,'F','popeye','TBD','Popeye','Popeye',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (53,'F','popplace','Sam C Rawls','Pops Place','ppt',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (54,'F','pvaliant','John Cullen Murphy & Cullen Murphy','Prince Valiant','val',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,750,750)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (55,'F','ralph','Wayne Stayskal','Ralph','Ralph',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,248,248)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (56,'F','redeye','Bill Yates & Mel Casson','Red Eye','Redeye',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (57,'F','orange','Hilary Price','Rhymes With Orange','Rhymes_with_Orange',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (58,'F','safehavn','Bill Holbrook','Safe Havens','Safe_Havens',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (59,'F','sforth','Greg Howard & Craig MacIntosh','Sally Forth','Sally_Forth',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,525,525)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (60,'F','sam_silo','Jerry Dumas','Sam and Silo','sst',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (61,'F','sixchix','Isabella Bannerman, Margaret Shuloc k, Rina Piccolo, Ann C. Telnaes, Kathryn LeMieux, and Stephanie Piro','Six Chix','6Chix',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (62,'F','sroper','John Saunders & Fran Matera','Steve Roper & Mike Nomad','Steve_Roper',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (63,'F','theydoit','Al Scaduto','They Do It Every Time','TDIE',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,246,246)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (64,'F','tiger','Bud Blake','Tiger','Tiger',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (65,'F','trudy','Jerry Marcus','Trudy','trt',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,247,247)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (66,'F','willy','Joe Martin','Willy and Ethel','Willy_n_Ethel',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (67,'F','zippy','Bill Griffith','Zippy','Zippy_the_Pinhead',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','King','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (68,'T','Zits','Jerry Scott & Jim Borgman','Zits','zits',0,145,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Toonville','General',0,'Remote',0,585,585)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (69,'T','fred','Alex Graham','Fred Basset','csfbt',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','ComicsPage','General',0,'Remote',7,580,580)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (70,'T','bound','Dana Summers','Bound & Gagged','csbgg',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','ComicsPage','General',0,'Remote',0,580,580)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (71,'T','herb','Stephen Bentley','Herb & Jamaal','cshjl',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','ComicsPage','General',0,'Remote',0,580,580)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (72,'T','middletons','Ralph Dunagin & Dana Summers','The Middletons','csmid',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','ComicsPage','General',0,'Remote',0,580,580)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (73,'T','pluggers','Brookins (Submissions)','Pluggers','cpplg',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','ComicsPage','General',0,'Remote',0,300,580)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (74,'T','alleyoop','Dave Gaue & Jack Bender','Alley Oop','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (75,'T','arlonjanis','Jimmy Johnson','Arlo and Janis','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (76,'T','betty','Gary Delainey & Gerry Rasmussen','Betty','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (77,'T','bornloser','Chip Sansom','The Born Loser','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (78,'T','committed','Michael Fry','Committed','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,300,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (79,'T','drabble','Kevin Fagan','Drabble','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (80,'T','forbetter','Lynn Johnston','For Better or For Worse','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (81,'T','grizzwells','Bill Schorr','The Grizzwells','',0,0,'','','','','','Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,0)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (82,'T','herman','Jim Unger','Herman','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,300,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (83,'T','jumpstart','Robb Armstrong','JumpStart','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (84,'T','kitncarlyle','Larry Wright','Kit-n-Carlyle','',0,0,'','','','','','Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,300,0)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (85,'T','liberty','Frank Cho','Liberty Meadows','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Creators','General',0,'Remote',0,625,650)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (86,'T','luann','Greg Evans','Luann','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (87,'T','marmaduke','Brad Anderson','Marmaduke','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,300,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (88,'T','meg','Greg Curfman','Meg!','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (89,'T','nancy','Guy and Brad Gilchrist','Nancy','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (90,'T','hedge','Michael Fry and T. Lewis','Over The Hedge','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (91,'T','peanuts','Charles Schulz','Peanuts','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (92,'T','robotman','Jim Meddick','Robotman','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (93,'T','roseisrose','Pat Brady','Rose is Rose','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (94,'T','warped','Mike Cavna','Warped','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,725)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (95,'T','adam','Brian Basset','Adam at Home','ad',0,0,'http://www.ucomics.com/adamathome/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (96,'T','bizaro','Dan Piraro','Bizaro','bz',0,0,'http://www.ucomics.com/bizarro/viewbz.htm','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,300,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (97,'T','boondocks','Aaron McGruder','The Boondocks','bo',0,0,'http://www.ucomics.com/boondocks/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (98,'T','cathy','Cathy Guisewite','Cathy','ca',0,0,'http://www.ucomics.com/cathy/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (99,'T','citizendog','".$oProc->m_odb->db_addslashes('Mark O\'Hare')."','Citizen Dog','cd',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (100,'T','closehome','John McPherson','Close to Home','cl',0,0,'http://www.ucomics.com/closetohome/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,300,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (101,'T','cornered','Mike Baldwin','Cornered','co',0,0,'http://www.ucomics.com/cornered/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,300,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (102,'T','crabbyrd','John Wagner','Crabby Road','cr',0,0,'http://www.ucomics.com/crabbyroad/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,300,300)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (103,'T','crankshaft','Tom Batiuk & Chuck Ayers','Crankshaft','cs',0,0,'http://www.ucomics.com/crankshaft/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (104,'T','duplex','Glenn McCoy','The Duplex','dp',0,0,'http://www.ucomics.com/duplex/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (105,'T','fifthwave','Rich Tennant','Fifth Wave','fw',0,0,'http://www.ucomics.com/thefifthwave/','','','','','Su','Ucomics','General',0,'Remote',0,0,500)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (106,'T','geech','Jerry Bittle','Geech','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (107,'T','heartcity','Mark Tatulli','Heart of the City','hc',0,0,'http://www.ucomics.com/heartofthecity/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (108,'T','bleachers','Steve Moore','In the Bleachers','bl',0,0,'http://www.ucomics.com/inthebleachers/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,300,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (109,'T','mrboffo','Joe Martin','Mister Boffo','mb',0,0,'http://www.ucomics.com/misterboffo/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (110,'T','overboard','Chip Dunham','Overboard','ob',0,0,'http://www.ucomics.com/overboard/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (111,'T','hth','George Gately','Heathcliff','hth',0,101,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Toonville','General',0,'Remote',0,300,300)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (112,'T','stonesoup','Jan Eliot','Stone Soup','ss',0,0,'http://www.ucomics.com/stonesoup/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (113,'T','reallife','Gary Wise & Lance Aldrich','Real Life Adventures','rl',0,0,'http://www.ucomics.com/reallifeadventures/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,300,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (114,'T','tmcnamara','Jeff Millar & Bill Hinds','Tank McNamara','tm',0,0,'http://www.ucomics.com/tankmcnamara/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (115,'T','baldo','Geof Brooks','Baldo','ba',0,0,'http://www.ucomics.com/baldo/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (116,'T','ziggy','Tom Wilson','Ziggy','',0,0,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Comiczone7','General',0,'Remote',0,300,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (117,'T','fuscobros','Joe Duffy','The Fusco Brothers','fu',0,0,'http://www.ucomics.com/thefuscobrothers/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (118,'T','doonesbury','G. B. Trudeau','Doonesbury','db',0,0,'http://www.doonesbury.com/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','Political',0,'Remote',0,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (119,'T','shoe','Jeff McNelly','Shoe','',0,0,'http://macnelly.com/shoe_page.html','http://macnelly.com/shoe_images{m}{y}/shoe_daily{m}{d}{y}.jpg','','','','Su:Mo:Tu:We:Th:Fr:Sa','None','General',0,'Static',0,612,1047)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (120,'T','calvinandhobbes','Bill Watterson','Calvin and Hobbes','ch',0,0,'http://www.calvinandhobbes.com/','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Ucomics','General',0,'Remote',4018,600,600)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (121,'T','boll','Ruben Bolling','Tom The Dancing Bug','td',0,0,'http://www.ucomics.com/tomthedancingbug/','','','','','Sa','Ucomics','General',0,'Remote',0,480,480)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (122,'T','frankernest','Bob Thaves','Frank and Ernest','frank',0,126,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Toonville','General',0,'Remote',0,585,585)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (124,'T','KevKel','Bill Holbrook','Kevin & Kell','Kev',0,149,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Toonville','General',0,'Remote',0,585,585)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (123,'T','Hilois','Browne, Walker, and Walker','Hi and Lois','Hi',0,105,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Toonville','General',0,'Remote',0,585,585)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (125,'T','Grimmy','Mike Peters','Mother Goose & Grimm','Grimm',0,148,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Toonville','General',0,'Remote',0,585,585)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (126,'T','sherman','Jim Toomey','Shermans Lagoon','sher',0,108,'','','','','','Su:Mo:Tu:We:Th:Fr:Sa','Toonville','General',0,'Remote',0,585,585)");
	$oProc->query("INSERT INTO phpgw_comic_data VALUES (127,'T','babyblue','Jerry Scott & Rick Kirkman','Baby Blues','',0,0,'http://www.babyblues.com/','http://www.babyblues.com/images/{Y}/{ymd}.gif','','','','Su:Mo:Tu:We:Th:Fr:Sa','None','General',0,'Static',14,585,585)");
?>
