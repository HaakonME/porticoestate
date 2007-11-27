<?php
/*************************************************************************************
 * visualfoxpro.php
 * ----------------
 * Author: Roberto Armellin (r.armellin@tin.it)
 * Copyright: (c) 2004 Roberto Armellin, Nigel McNie (http://qbnz.com/highlighter/)
 * Release Version: 1.0.7.14
 * CVS Revision Version: $Revision: 1.1 $
 * Date Started: 2004/09/17
 * Last Modified: 2004/09/18
 *
 * Visual FoxPro language file for GeSHi.
 *
 * CHANGES
 * -------
 * 2004/11/27 (1.0.1)
 *  -  Added support for multiple object splitters
 * 2004/10/27 (1.0.0)
 *  -  First Release
 *
 * TODO (updated 2004/10/27)
 * -------------------------
 *
 *************************************************************************************
 *
 *     This file is part of GeSHi.
 *
 *   GeSHi is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   GeSHi is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with GeSHi; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ************************************************************************************/

$language_data = array (
	'LANG_NAME' => 'Visual Fox Pro',
	'COMMENT_SINGLE' => array(1 => "//", 2 => "\n*"),
	'COMMENT_MULTI' => array(),
	'CASE_KEYWORDS' => GESHI_CAPS_NO_CHANGE,
	'QUOTEMARKS' => array('"'),
	'ESCAPE_CHAR' => '\\',
	'KEYWORDS' => array(
		1 => array('Case', 'Else', '#Else', 'Then',
			'Endcase', 'Enddefine', 'Enddo', 'Endfor', 'Endfunc', 'Endif', 'Endprintjob',
			'Endproc', 'Endscan', 'Endtext', 'Endwith', '#Endif',
			'#Elif','#Else','#Endif','#Define','#If','#Include',
			'#Itsexpression','#Readclauses','#Region','#Section','#Undef','#Wname',
			'Case','Define','Do','Else','Endcase','Enddefine',
			'Enddo','Endfor','Endfunc','Endif','Endprintjob','Endproc',
			'Endscan','Endtext','Endwith','For','Function','Hidden',
			'If','Local','Lparameter','Lparameters','Next','Otherwise',
			'Parameters','Printjob','Procedure','Protected','Public','Scan',
			'Text','Then','While','With','?','??',
			'???','Abs','Accept','Access','Aclass','Acopy',
			'Acos','Adatabases','Adbobjects','Addbs','Addrelationtoenv','Addtabletoenv',
			'Adel','Adir','Aelement','Aerror','Afields','Afont',
			'Agetclass','Agetfileversion','Ains','Ainstance','Alen','Align',
			'Alines','Alltrim','Alter','Amembers','Amouseobj','Anetresources',
			'Ansitooem','Append','Aprinters','Ascan','Aselobj','Asin',
			'Asort','Assert','Asserts','Assist','Asubscript','Asynchronous',
			'At_c','Atan','Atc','Atcc','Atcline','Atline',
			'Atn2','Aused','Autoform','Autoreport','Avcxclasses','Average',
			'BarCount','BarPrompt','BatchMode','BatchUpdateCount','Begin','BellSound',
			'BinToC','Bintoc','Bitand','Bitclear','Bitlshift','Bitnot',
			'Bitor','Bitrshift','Bitset','Bittest','Bitxor','Bof',
			'Browse','BrowseRefresh','Buffering','BuilderLock','COMArray','COMReturnError',
			'CToBin','Calculate','Call','Capslock','Cd','Cdow',
			'Ceiling','Central','Change','Char','Chdir','Chr',
			'Chrsaw','Chrtran','Chrtranc','Close','Cmonth','Cntbar',
			'Cntpad','Col','Comclassinfo','CommandTargetQuery','Compile','Completed',
			'Compobj','Compute','Concat','ConnectBusy','ConnectHandle','ConnectName',
			'ConnectString','ConnectTimeOut','ContainerReleaseType','Continue','Copy','Cos',
			'Cot','Count','Coverage','Cpconvert','Cpcurrent','Cpdbf',
			'Cpnotrans','Create','CreateBinary','Createobject','Createobjectex','Createoffline',
			'CrsBuffering','CrsFetchMemo','CrsFetchSize','CrsMaxRows','CrsMethodUsed','CrsNumBatch',
			'CrsShareConnection','CrsUseMemoSize','CrsWhereClause','Ctobin','Ctod','Ctot',
			'Curdate','Curdir','CurrLeft','CurrSymbol','CursorGetProp','CursorSetProp',
			'Curtime','Curval','DBGetProp','DBSetProp','DB_BufLockRow','DB_BufLockTable',
			'DB_BufOff','DB_BufOptRow','DB_BufOptTable','DB_Complette','DB_DeleteInsert','DB_KeyAndModified',
			'DB_KeyAndTimestamp','DB_KeyAndUpdatable','DB_LocalSQL','DB_NoPrompt','DB_Prompt','DB_RemoteSQL',
			'DB_TransAuto','DB_TransManual','DB_TransNone','DB_Update','Datetime','Day',
			'Dayname','Dayofmonth','Dayofweek','Dayofyear','Dbalias','Dbused',
			'Ddeaborttrans','Ddeadvise','Ddeenabled','Ddeexecute','Ddeinitiate','Ddelasterror',
			'Ddepoke','Dderequest','Ddesetoption','Ddesetservice','Ddesettopic','Ddeterminate',
			'Debugout','Declare','DefOLELCid','DefaultValue','Defaultext','Degrees',
			'DeleteTrigger','Desc','Description','Difference','Dimension','Dir',
			'Directory','Diskspace','DispLogin','DispWarnings','Display','Dll',
			'Dmy','DoDefault','DoEvents','Doc','Doevents','Dow',
			'Drivetype','Drop','Dropoffline','Dtoc','Dtor','Dtos',
			'Dtot','DynamicInputMask','Each','Edit','Eject','Elif',
			'End','Eof','Erase','Evaluate','Event','Eventtracking',
			'Exclude','Exclusive','Exit','Exp','Export','External',
			'FDate','FTime','Fchsize','Fclose','Fcount','Fcreate',
			'Feof','Ferror','FetchMemo','FetchSize','Fflush','Fgets',
			'Filer','Filetostr','Find','Fklabel','Fkmax','Fldlist',
			'Flock','Floor','Flush','Fontmetric','Fopen','Forceext',
			'Forcepath','FormSetClass','FormSetLib','FormsClass','FormsLib','Found',
			'FoxPro','Foxcode','Foxdoc','Foxgen','Foxgraph','Foxview',
			'Fputs','Fread','French','Fseek','Fsize','Fv',
			'Fwrite','Gather','German','GetPem','Getbar','Getcolor',
			'Getcp','Getdir','Getenv','Getexpr','Getfile','Getfldstate',
			'Getfont','Gethost','Getnextmodified','Getobject','Getpad','Getpict',
			'Getprinter','Go','Gomonth','Goto','Graph','GridHorz',
			'GridShow','GridShowPos','GridSnap','GridVert','Help','HelpOn',
			'HelpTo','HighLightRow','Home','Hour','IMEStatus','IdleTimeOut',
			'Idxcollate','Ifdef','Ifndef','Iif','Import','Include',
			'Indbc','Index','Indexseek','Inkey','Inlist','Input',
			'Insert','InsertTrigger','Insmode','IsBlank','IsFLocked','IsLeadByte',
			'IsMouse','IsNull','IsRLocked','Isalpha','Iscolor','Isdigit',
			'Isexclusive','Isflocked','Ishosted','Islower','Isreadonly','Isrlocked',
			'Isupper','Italian','Japan','Join','Justdrive','Justext',
			'Justfname','Justpath','Juststem','KeyField','KeyFieldList','Keyboard'
			),
		2 => array('Keymatch','LastProject','Lastkey','Lcase','Leftc','Len',
			'Lenc','Length','Likec','Lineno','LoadPicture','Loadpicture',
			'Locate','Locfile','Log','Log10','Logout','Lookup',
			'Loop','Lower','Ltrim','Lupdate','Mail','MaxRecords',
			'Mcol','Md','Mdown','Mdx','Mdy','Memlines',
			'Menu','Messagebox','Minute','Mkdir','Mline','Modify',
			'Month','Monthname','Mouse','Mrkbar','Mrkpad','Mrow',
			'Mtdll','Mton','Mwindow','Native','Ndx','Network',
			'NoFilter','Nodefault','Normalize','Note','Now','Ntom',
			'NullString','Numlock','Nvl','ODBChdbc','ODBChstmt','OLEDropTextInsertion',
			'OLELCid','Objnum','Objref','Objtoclient','Objvar','Occurs',
			'Oemtoansi','Oldval','OlePublic','Olereturnerror','On','Open',
			'Oracle','Order','Os','Outer','PCount','Pack',
			'PacketSize','Padc','Padl','Padr','Payment','Pcol',
			'PemStatus','Pi','Pivot','Play','Pop','Popup',
			'Power','PrimaryKey','Printstatus','Private','Prmbar','Prmpad',
			'ProjectClick','Proper','Prow','Prtinfo','Push','Putfile',
			'Pv','Qpr','Quater','QueryTimeOut','Quit','Radians',
			'Rand','Rat','Ratc','Ratline','Rd','Rdlevel',
			'Read','Readkey','Recall','Reccount','RecentlyUsedFiles','Recno',
			'Recsize','Regional','Reindex','RelatedChild','RelatedTable','RelatedTag',
			'Remove','Rename','Repeat','Replace','Replicate','Report',
			'ResHeight','ResWidth','ResourceOn','ResourceTo','Resources','Restore',
			'Resume','Retry','Return','Revertoffline','Rgbscheme','Rightc',
			'Rlock','Rmdir','Rollback','Round','Rtod','Rtrim',
			'RuleExpression','RuleText','Run','Runscript','Rview','SQLAsynchronous',
			'SQLBatchMode','SQLCancel','SQLColumns','SQLConnect','SQLConnectTimeOut','SQLDisconnect',
			'SQLDispLogin','SQLDispWarnings','SQLExec','SQLGetProp','SQLIdleTimeOut','SQLMoreResults',
			'SQLPrepare','SQLQueryTimeOut','SQLSetProp','SQLTables','SQLTransactions','SQLWaitTime',
			'Save','SavePicture','Savepicture','ScaleUnits','Scatter','Scols',
			'Scroll','Sec','Second','Seek','Select','SendUpdates',
			'Set','SetDefault','Setfldstate','Setup','ShareConnection','ShowOLEControls',
			'ShowOLEInsertable','ShowVCXs','Sign','Sin','Size','SizeBox',
			'Skpbar','Skppad','Sort','Soundex','SourceName','Sqlcommit',
			'Sqll','Sqlrollback','Sqlstringconnect','Sqrt','Srows','StatusBar',
			'Store','Str','Strconv','Strtofile','Strtran','Stuff',
			'Stuffc','Substr','Substrc','Substring','Sum','Suspend',
			'Sys','Sysmetric','TabOrdering','Table','TableRefresh','Tablerevert',
			'Tableupdate','TagCount','TagNo','Tan','Target','This',
			'Thisform','Thisformset','Timestamp','Timestampdiff','Total','Transactions',
			'Transform','Trim','Truncate','Ttoc','Ttod','Txnlevel',
			'Txtwidth','Type','Ucase','Undefine','Unlock','Unpack',
			'Updatable','UpdatableFieldList','Update','UpdateName','UpdateNameList','UpdateTrigger',
			'UpdateType','Updated','Upper','Upsizing','Usa','Use',
			'UseMemoSize','Used','Val','Validate','Varread','Vartype',
			'Version','VersionLanguage','Wait','WaitTime','Wborder','Wchild',
			'Wcols','Week','Wexist','Wfont','WhereType','Windcmd',
			'Windhelp','Windmemo','Windmenu','Windmodify','Windquery','Windscreen',
			'Windsnip','Windstproc','WizardPrompt','Wlast','Wlcol','Wlrow',
			'Wmaximum','Wminimum','Wontop','Woutput','Wparent','Wread',
			'Wrows','Wtitle','Wvisible','Year','Zap','_Alignment',
			'_Asciicols','_Asciirows','_Assist','_Beautify','_Box','_Browser',
			'_Builder','_Calcmem','_Calcvalue','_Cliptext','_Converter','_Coverage',
			'_Curobj','_Dblclick','_Diarydate','_Dos','_Foxdoc','_Foxgraph',
			'_Gallery','_Gengraph','_Genhtml','_Genmenu','_Genpd','_Genscrn',
			'_Genxtab','_Getexpr','_Include','_Indent','_Lmargin','_Mac',
			'_Mbr_appnd','_Mbr_cpart','_Mbr_delet','_Mbr_font','_Mbr_goto','_Mbr_grid',
			'_Mbr_link','_Mbr_mode','_Mbr_mvfld','_Mbr_mvprt','_Mbr_seek','_Mbr_sp100',
			'_Mbr_sp200','_Mbr_szfld','_Mbrowse','_Mda_appnd','_Mda_avg','_Mda_brow',
			'_Mda_calc','_Mda_copy','_Mda_count','_Mda_label','_Mda_pack','_Mda_reprt',
			'_Mda_rindx','_Mda_setup','_Mda_sort','_Mda_sp100','_Mda_sp200','_Mda_sp300',
			'_Mda_sum','_Mda_total','_Mdata','_Mdiary','_Med_clear','_Med_copy',
			'_Med_cut','_Med_cvtst','_Med_find','_Med_finda','_Med_goto','_Med_insob',
			'_Med_link','_Med_obj','_Med_paste','_Med_pref','_Med_pstlk','_Med_redo',
			'_Med_repl','_Med_repla','_Med_slcta','_Med_sp100','_Med_sp200','_Med_sp300',
			'_Med_sp400','_Med_sp500','_Med_undo','_Medit','_Mfi_clall','_Mfi_close',
			'_Mfi_export','_Mfi_import','_Mfi_new','_Mfi_open','_Mfi_pgset','_Mfi_prevu',
			'_Mfi_print','_Mfi_quit','_Mfi_revrt','_Mfi_savas','_Mfi_save','_Mfi_send',
			'_Mfi_setup','_Mfi_sp100','_Mfi_sp200','_Mfi_sp300','_Mfi_sp400','_Mfile',
			'_Mfiler','_Mfirst','_Mlabel','_Mlast','_Mline','_Mmacro',
			'_Mmbldr','_Mpr_beaut','_Mpr_cancl','_Mpr_compl','_Mpr_do','_Mpr_docum',
			'_Mpr_formwz','_Mpr_gener','_Mpr_graph','_Mpr_resum','_Mpr_sp100','_Mpr_sp200',
			'_Mpr_sp300','_Mpr_suspend','_Mprog','_Mproj','_Mrc_appnd','_Mrc_chnge',
			'_Mrc_cont','_Mrc_delet','_Mrc_goto','_Mrc_locat','_Mrc_recal','_Mrc_repl',
			'_Mrc_seek','_Mrc_sp100','_Mrc_sp200','_Mrecord','_Mreport','_Mrqbe',
			'_Mscreen','_Msm_data','_Msm_edit','_Msm_file','_Msm_format','_Msm_prog',
			'_Msm_recrd','_Msm_systm','_Msm_text','_Msm_tools','_Msm_view','_Msm_windo',
			'_Mst_about','_Mst_ascii','_Mst_calcu','_Mst_captr','_Mst_dbase','_Mst_diary',
			'_Mst_filer','_Mst_help','_Mst_hphow','_Mst_hpsch','_Mst_macro','_Mst_office',
			'_Mst_puzzl','_Mst_sp100','_Mst_sp200','_Mst_sp300','_Mst_specl','_Msysmenu',
			'_Msystem','_Mtable','_Mtb_appnd','_Mtb_cpart','_Mtb_delet','_Mtb_delrc',
			'_Mtb_goto','_Mtb_link','_Mtb_mvfld','_Mtb_mvprt','_Mtb_props','_Mtb_recal',
			'_Mtb_sp100','_Mtb_sp200','_Mtb_sp300','_Mtb_sp400','_Mtb_szfld','_Mwi_arran',
			'_Mwi_clear','_Mwi_cmd','_Mwi_color','_Mwi_debug','_Mwi_hide','_Mwi_hidea',
			'_Mwi_min','_Mwi_move','_Mwi_rotat','_Mwi_showa','_Mwi_size','_Mwi_sp100',
			'_Mwi_sp200','_Mwi_toolb','_Mwi_trace','_Mwi_view','_Mwi_zoom','_Mwindow',
			'_Mwizards','_Mwz_all','_Mwz_form','_Mwz_foxdoc','_Mwz_import','_Mwz_label',
			'_Mwz_mail','_Mwz_pivot','_Mwz_query','_Mwz_reprt','_Mwz_setup','_Mwz_table',
			'_Mwz_upsizing','_Netware','_Oracle','_Padvance','_Pageno','_Pbpage',
			'_Pcolno','_Pcopies','_Pdparms','_Pdriver','_Pdsetup','_Pecode',
			'_Peject','_Pepage','_Pform','_Plength','_Plineno','_Ploffset',
			'_Ppitch','_Pquality','_Pretext','_Pscode','_Pspacing','_Pwait',
			'_Rmargin','_Runactivedoc','_Samples','_Screen','_Shell','_Spellchk',
			'_Sqlserver','_Startup','_Tabs','_Tally','_Text','_Throttle',
			'_Transport','_Triggerlevel','_Unix','_WebDevOnly','_WebMenu','_WebMsftHomePage',
			'_WebVFPHomePage','_WebVfpOnlineSupport','_Windows','_Wizard','_Wrap','_scctext',
			'_vfp','Additive','After','Again','Aindent','Alignright',
			'All','Alt','Alternate','And','Ansi','Any',
			'Aplabout','App','Array','As','Asc','Ascending',
			'Ascii','At','Attributes','Automatic','Autosave','Avg',
			'Bar','Before','Bell','Between','Bitmap','Blank',
			'Blink','Blocksize','Border','Bottom','Brstatus','Bucket',
			'Buffers','By','Candidate','Carry','Cascade','Catalog',
			'Cdx','Center','Century','Cga','Character','Check',
			'Classlib','Clock','Cnt','Codepage','Collate','Color',
			'Com1','Com2','Command','Compact','Compatible','Compress',
			'Confirm','Connection','Connections','Connstring','Console','Copies',
			'Cpcompile','Cpdialog','Csv','Currency','Cycle','Databases',
			'Datasource','Date','Db4','Dbc','Dbf','Dbmemo3',
			'Debug','Decimals','Defaultsource','Deletetables','Delimited','Delimiters',
			'Descending','Design','Development','Device','Dif','Disabled',
			'Distinct','Dlls','Dohistory','Dos','Dosmem','Double',
			'Driver','Duplex','Echo','Editwork','Ega25','Ega43',
			'Ems','Ems64','Encrypt','Encryption','Environment','Escape',
			'Events','Exact','Except','Exe','Exists','Expression',
			'Extended','F','Fdow','Fetch','Field','Fields',
			'File','Files','Fill','Fixed','Float','Foldconst',
			'Font','Footer','Force','Foreign','Fox2x','Foxplus',
			'Free','Freeze','From','Fullpath','Fw2','Fweek',
			'Get','Gets','Global','Group','Grow','Halfheight',
			'Having','Heading','Headings','Helpfilter','History','Hmemory',
			'Hours','Id','In','Indexes','Information','Instruct',
			'Int','Integer','Intensity','Intersect','Into','Is',
			'Isometric','Key','Keycolumns','Keycomp','Keyset','Last',
			'Ledit','Level','Library','Like','Linked','Lock',
			'Logerrors','Long','Lpartition','Mac','Macdesktop','Machelp',
			'Mackey','Macros','Mark','Master','Max','Maxmem',
			'Mdi','Memlimit','Memory','Memos','Memowidth','Memvar',
			'Menus','Messages','Middle','Min','Minimize','Minus',
			'Mod','Modal','Module','Mono43','Movers','Multilocks',
			'Mvarsiz','Mvcount','N','Near','Negotiate','Noalias',
			'Noappend','Noclear','Noclose','Noconsole','Nocptrans','Nodata',
			'Nodebug','Nodelete','Nodup','Noedit','Noeject','Noenvironment',
			'Nofloat','Nofollow','Nogrow','Noinit','Nolgrid','Nolink',
			'Nolock','Nomargin','Nomdi','Nomenu','Nominimize','Nomodify'
			),
		3 => array('Nomouse','None','Nooptimize','Nooverwrite','Noprojecthook','Noprompt',
			'Noread','Norefresh','Norequery','Norgrid','Norm','Normal',
			'Nosave','Noshadow','Noshow','Nospace','Not','Notab',
			'Notify','Noupdate','Novalidate','Noverify','Nowait','Nowindow',
			'Nowrap','Nozoom','Npv','Null','Number','Objects',
			'Odometer','Of','Off','Oleobjects','Only','Optimize',
			'Or','Orientation','Output','Outshow','Overlay','Overwrite',
			'Pad','Palette','Paperlength','Papersize','Paperwidth','Password',
			'Path','Pattern','Pause','Pdox','Pdsetup','Pen',
			'Pfs','Pixels','Plain','Popups','Precision','Preference',
			'Preview','Primary','Printer','Printquality','Procedures','Production',
			'Program','Progwork','Project','Prompt','Query','Random',
			'Range','Readborder','Readerror','Record','Recover','Redit',
			'Reference','References','Relative','Remote','Reprocess','Resource',
			'Rest','Restrict','Rgb','Right','Row','Rowset',
			'Rpd','Runtime','Safety','Same','Sample','Say',
			'Scale','Scheme','Scoreboard','Screen','Sdf','Seconds',
			'Selection','Shadows','Shared','Sheet','Shell','Shift',
			'Shutdown','Single','Some','Sortwork','Space','Sql',
			'Standalone','Status','Std','Step','Sticky','String',
			'Structure','Subclass','Summary','Sylk','Sysformats','Sysmenus',
			'System','T','Tab','Tables','Talk','Tedit',
			'Textmerge','Time','Timeout','Titles','Tmpfiles','To',
			'Topic','Transaction','Trap','Trbetween','Trigger','Ttoption',
			'Typeahead','Udfparms','Union','Unique','Userid','Users',
			'Values','Var','Verb','Vga25','Vga50','Views',
			'Volume','Where','Windows','Wk1','Wk3','Wks',
			'Workarea','Wp','Wr1','Wrap','Wrk','Xcmdfile',
			'Xl5','Xl8','Xls','Y','Yresolution','Zoom',
			'Activate','ActivateCell','Add','AddColumn','AddItem','AddListItem',
			'AddObject','AddProperty','AddToSCC','AfterBuild','AfterCloseTables','AfterDock',
			'AfterRowColChange','BeforeBuild','BeforeDock','BeforeOpenTables','BeforeRowColChange','Box',
			'Build','CheckIn','CheckOut','Circle','Clear','ClearData',
			'Cleanup','Click','CloneObject','CloseEditor','CloseTables','Cls',
			'CommandTargetExec','CommandTargetQueryStas','ContainerRelease','DataToClip','DblClick','Deactivate',
			'Delete','DeleteColumn','Deleted','Destroy','DoCmd','Dock',
			'DoScroll','DoVerb','DownClick','Drag','DragDrop','DragOver',
			'DropDown','Draw','EnterFocus','Error','ErrorMessage','Eval',
			'ExitFocus','FormatChange','GetData','GetFormat','GetLatestVersion','GoBack',
			'GotFocus','GoForward','GridHitTest','Hide','HideDoc','IndexToItemId',
			'Init','InteractiveChange','Item','ItemIdToIndex','KeyPress','Line',
			'Load','LostFocus','Message','MiddleClick','MouseDown','MouseMove',
			'MouseUp','MouseWheel','Move','Moved','NavigateTo','Newobject',
			'OLECompleteDrag','OLEDrag','OLEDragDrop','OLEDragOver','OLEGiveFeedback','OLESetData',
			'OLEStartDrag','OpenEditor','OpenTables','Paint','Point','Print',
			'ProgrammaticChange','PSet','QueryAddFile','QueryModifyFile','QueryRemoveFile','QueryRunFile',
			'QueryUnload','RangeHigh','RangeLow','ReadActivate','ReadExpression','ReadDeactivate',
			'ReadMethod','ReadShow','ReadValid','ReadWhen','Refresh','Release',
			'RemoveFromSCC','RemoveItem','RemoveListItem','RemoveObject','Requery','RequestData',
			'Reset','ResetToDefault','Resize','RightClick','SaveAs','SaveAsClass',
			'Scrolled','SetAll','SetData','SetFocus','SetFormat','SetMain',
			'SetVar','SetViewPort','ShowDoc','ShowWhatsThis','TextHeight','TextWidth',
			'Timer','UIEnable','UnDock','UndoCheckOut','Unload','UpClick',
			'Valid','WhatsThisMode','When','WriteExpression','WriteMethod','ZOrder',
			'ATGetColors','ATListColors','Accelerate','ActiveColumn','ActiveControl','ActiveForm',
			'ActiveObjectId','ActivePage','ActiveProject','ActiveRow','AddLineFeeds','Alias',
			'Alignment','AllowAddNew','AllowHeaderSizing','AllowResize','AllowRowSizing','AllowTabs',
			'AlwaysOnTop','Application','AutoActivate','AutoCenter','AutoCloseTables','AutoIncrement',
			'AutoOpenTables','AutoRelease','AutoSize','AutoVerbMenu','AutoYield','AvailNum',
			'BackColor','BackStyle','BaseClass','BorderColor','BorderStyle','BorderWidth',
			'Bound','BoundColumn','BoundTo','BrowseAlignment','BrowseCellMarg','BrowseDestWidth',
			'BufferMode','BufferModeOverride','BuildDateTime','ButtonCount','ButtonIndex','Buttons',
			'CLSID','CanAccelerate','CanGetFocus','CanLoseFocus','Cancel','Caption',
			'ChildAlias','ChildOrder','Class','ClassLibrary','ClipControls','ClipRect',
			'Closable','ColorScheme','ColorSource','ColumnCount','ColumnHeaders','ColumnLines',
			'ColumnOrder','ColumnWidths','Columns','Comment','ContinuousScroll','ControlBox',
			'ControlCount','ControlIndex','ControlSource','Controls','CurrentControl','CurrentX',
			'CurrentY','CursorSource','Curvature','DataSession','DataSessionId','DataSourceObj',
			'DataType','Database','DateFormat','DateMark','DefButton','DefButtonOrig',
			'DefHeight','DefLeft','DefTop','DefWidth','Default','DefaultFilePath',
			'DefineWindows','DeleteMark','Desktop','Dirty','DisabledBackColor','DisabledByEOF',
			'DisabledForeColor','DisabledItemBackColor','DisabledItemForeColor','DisabledPicture','DispPageHeight','DispPageWidth',
			'DisplayCount','DisplayValue','DoCreate','DockPosition','Docked','DocumentFile',
			'DownPicture','DragIcon','DragMode','DragState','DrawMode','DrawStyle',
			'DrawWidth','DynamicAlignment','DynamicBackColor','DynamicCurrentControl','DynamicFontBold','DynamicFontItalic',
			'DynamicFontName','DynamicFontOutline','DynamicFontShadow','DynamicFontSize','DynamicFontStrikethru','DynamicFontUnderline',
			'DynamicForeColor','EditFlags','Enabled','EnabledByReadLock','Encrypted','EnvLevel',
			'ErasePage','FileClass','FileClassLibrary','FillColor','FillStyle','Filter',
			'FirstElement','FontBold','FontItalic','FontName','FontOutline','FontShadow',
			'FontSize','FontStrikethru','FontUnderline','ForceFocus','ForeColor','FormCount',
			'FormIndex','FormPageCount','FormPageIndex','Format','Forms','FoxFont',
			'FullName','GoFirst','GoLast','GridLineColor','GridLineWidth','GridLines'
			),
		4 => array('HPROJ','HWnd','HalfHeightCaption','HasClip','HeaderGap','HeaderHeight',
			'Height','HelpContextID','HideSelection','Highlight','HomeDir','HostName',
			'HotKey','HscrollSmallChange','IMEMode','Icon','IgnoreInsert','InResize',
			'Increment','IncrementalSearch','InitialSelectedAlias','InputMask','Instancing','IntegralHeight',
			'Interval','ItemBackColor','ItemData','ItemForeColor','ItemIDData','ItemTips',
			'JustReadLocked','KeyPreview','KeyboardHighValue','KeyboardLowValue','LastModified','Left',
			'LeftColumn','LineSlant','LinkMaster','List','ListCount','ListIndex',
			'ListItem','ListItemId','LockDataSource','LockScreen','MDIForm','MainClass',
			'MainFile','Margin','MaxButton','MaxHeight','MaxLeft','MaxLength',
			'MaxTop','MaxWidth','MemoWindow','MinButton','MinHeight','MinWidth',
			'MouseIcon','MousePointer','Movable','MoverBars','MultiSelect','Name',
			'NapTime','NewIndex','NewItemId','NoDataOnLoad','NoDefine','NotifyContainer',
			'NullDisplay','NumberOfElements','OLEDragMode','OLEDragPicture','OLEDropEffects','OLEDropHasData',
			'OLEDropMode','OLERequestPendingTimeOut','OLEServerBusyRaiseError','OLEServerBusyTimeOut','OLETypeAllowed','OleClass',
			'OleClassId','OleControlContainer','OleIDispInValue','OleIDispOutValue','OleIDispatchIncoming','OleIDispatchOutgoing',
			'OnResize','OneToMany','OpenViews','OpenWindow','PageCount','PageHeight',
			'PageOrder','PageWidth','Pages','Panel','PanelLink','Parent',
			'ParentAlias','ParentClass','Partition','PasswordChar','Picture','ProcessID',
			'ProgID','ProjectHookClass','ProjectHookLibrary','Projects','ReadColors','ReadCycle',
			'ReadFiller','ReadLock','ReadMouse','ReadOnly','ReadSave','ReadSize',
			'ReadTimeout','RecordMark','RecordSource','RecordSourceType','Rect','RelationalExpr',
			'RelativeColumn','RelativeRow','ReleaseErase','ReleaseType','ReleaseWindows','Resizable',
			'RightToLeft','RowHeight','RowSource','RowSourceType','SCCProvider','SCCStatus',
			'SDIForm','ScaleMode','ScrollBars','SelLength','SelStart','SelText',
			'SelectOnEntry','Selected','SelectedBackColor','SelectedForeColor','SelectedID','SelectedItemBackColor',
			'SelectedItemForeColor','SelfEdit','ServerClass','ServerClassLibrary','ServerHelpFile','ServerName',
			'ServerProject','ShowTips','ShowWindow','Sizable','Size<height>','Size<maxlength>',
			'Size<width>','Skip','SkipForm','Sorted','SourceType','Sparse',
			'SpecialEffect','SpinnerHighValue','SpinnerLowValue','SplitBar','StartMode','StatusBarText',
			'Stretch','StrictDateEntry','Style','SystemRefCount','TabIndex','TabStop',
			'TabStretch','TabStyle','Tabhit','Tabs','Tag','TerminateRead',
			'ThreadID','TitleBar','ToolTipText','Top','TopIndex','TopItemId',
			'TypeLibCLSID','TypeLibDesc','TypeLibName','UnlockDataSource','Value','ValueDirty',
			'VersionComments','VersionCompany','VersionCopyright','VersionDescription','VersionNumber','VersionProduct',
			'VersionTrademarks','View','ViewPortHeight','ViewPortLeft','ViewPortTop','ViewPortWidth',
			'Visible','VscrollSmallChange','WasActive','WasOpen','WhatsThisButton','WhatsThisHelp',
			'WhatsThisHelpID','Width','WindowList','WindowNTIList','WindowState','WindowType',
			'WordWrap','ZOrderSet','ActiveDoc','Checkbox','Column','ComboBox',
			'CommandButton','CommandGroup','Container','Control','Cursor','Custom',
			'DataEnvironment','EditBox','Empty','FontClass','Form','Formset',
			'General','Grid','Header','HyperLink','Image','Label',
			'ListBox','Memo','OleBaseControl','OleBoundControl','OleClassIDispOut','OleControl',
			'OptionButton','OptionGroup','Page','PageFrame','ProjectHook','RectClass',
			'Relation','Session','Shape','Spinner','TextBox' ,'Toolbar'
			),
		),
	'SYMBOLS' => array("!", "@", "$", "%", "(", ")", "-", "+", "=", "/", "{", "}", "[", "]", ":", ";", ",", "	", ".", "*", "&"),
	'CASE_SENSITIVE' => array(
		GESHI_COMMENTS => true,
		1 => false,
		2 => false,
		3 => false,
		4 => false,
		),
	'STYLES' => array(
		'KEYWORDS' => array(
			1 => 'color: blue;',
			2 => 'color: blue;',
			3 => 'color: blue;',
			4 => 'color: blue;'
			),
		'COMMENTS' => array(
			1 => 'color: green; font-style: italic;',
			2 => 'color: green font-style: italic;',
			'MULTI' => 'color: #808080; font-style: italic;'
			),
		'ESCAPE_CHAR' => array(
			0 => 'color: #000099; font-weight: bold;'
			),
		'BRACKETS' => array(
			0 => 'color: blue;'
			),
		'STRINGS' => array(
			0 => 'color: #ff0000;'
			),
		'NUMBERS' => array(
			0 => 'color: #cc66cc;'
			),
		'METHODS' => array(
			1 => 'color: #006600;'
			),
		'SYMBOLS' => array(
			0 => 'color: blue;'
			),
		'REGEXPS' => array(
			),
		'SCRIPT' => array(
			)
		),
	'OOLANG' => true,
	'OBJECT_SPLITTERS' => array(
		1 => '.'
		),
	'REGEXPS' => array(
		),
	'STRICT_MODE_APPLIES' => GESHI_NEVER,
	'SCRIPT_DELIMITERS' => array(
		),
	'HIGHLIGHT_STRICT_BLOCK' => array(
		)
);

?>