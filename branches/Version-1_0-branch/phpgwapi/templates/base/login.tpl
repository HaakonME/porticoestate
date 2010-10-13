<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<!-- BEGIN login_form -->
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="author" content="{system} http://www.phpgroupware.no">
	<meta name="description" content="{system} login screen, working environment powered by phpGroupWare">
	<meta name="keywords" content="{{system} login screen, phpgroupware, groupware, groupware suite">
	<meta name="robots" content="noindex,nofollow">
	
	<title>{system} - {lang_login}</title>

	<link rel="stylesheet" href="phpgwapi/templates/base/css/system.css" type="text/css">
	<link rel="stylesheet" href="{login_css}" type="text/css">

	<!--[if IE 7]>
	<link href="templates/khepri/css/ie7.css" rel="stylesheet" type="text/css" />
	<![endif]-->

	<!--[if lte IE 6]>
	<link href="templates/khepri/css/ie6.css" rel="stylesheet" type="text/css" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="phpgwapi/templates/base/css/rounded.css">

	<script language="javascript" type="text/javascript">
	<!--
		window.onload = function()
		{
			document.login.login.select();
			document.login.login.focus();
		}
	-->
	</script>
</head>

<body>
	<div id="border-top" class="h_green">
		<div>
			<div>
				<span class="title">{system}</span>
			</div>
		</div>
	</div>
	
	<div id="content-box">
		<div class="rawimages">
			<span><a href="login.php?lang=no"><img src="phpgwapi/templates/base/images/flag_no.gif" alt="Norsk (Norway)" title="Norsk (Norway)" ></a></span>
			<span><a href="login.php?lang=en"><img src="phpgwapi/templates/base/images/flag_en.gif" alt="English (United Kingdom)" title="English (United Kingdom)" ></a></span>
		</div>

		<div class="padding">
			<div id="element-box" class="login">
				<div class="t">
					<div class="t">
						<div class="t"></div>
					</div>
				</div>

				<div class="m">
					<h1>{system} {lang_login}</h1>

					<!-- BEGIN message_block -->
					<dl id="system-message">
						<dt class="{message_class}">{lang_message}</dt>
						<dd class="{message_class_item}">
							<ul>
								<li>{cd}</li>
							</ul>
						</dd>
					</dl>
					<!-- END message_block -->

					<div id="section-box">
						<div class="t">
							<div class="t">
								<div class="t"></div>
							</div>
						</div>

						<div class="m">

							<form name="login" method="post" action="{login_url}" {autocomplete} id="form-login" style="clear: both;">
								<input type="hidden" name="passwd_type" value="text">
								<!-- BEGIN loging_block -->
								<p id="form-login-username">
									<label for="modlgn_username">{lang_username}</label>
									<input type="text" value="{last_loginid}" name="login" id="modlgn_username" {login_read_only} class="inputbox" size="15" >
									<input type="hidden" name="skip_remote" value="{skip_remote}">
								</p>
								<!-- END loging_block -->
								<!-- BEGIN domain_from_host -->
									@{logindomain}<input type="hidden" name="logindomain" value="{logindomain}">
								<!-- END domain_from_host -->
								<br>
								<!-- BEGIN login_additional_info -->
								<label for="firstname">{lang_firstname}:</label>
								<input type="text" value="{firstname}" maxlength="100" name="firstname" id="firstname">
								<br>

								<label for="lastname">{lang_lastname}:</label>
								<input type="text" value="{lastname}" name="lastname" id="lastname" maxlength="100">
								<br>

								<!-- END login_additional_info -->
								<!-- BEGIN password_block -->
								<p id="form-login-password">
								<label for="passwd">{lang_password}</label>
								<input type="password" name="passwd" id="passwd" class="inputbox" size="15">
								</p>
								<!-- END password_block -->
								<!-- BEGIN login_check_passwd -->
								<label for="passwd_confirm">{lang_confirm_password}:</label>
								<input type="password" name="passwd_confirm" id="passwd_confirm" class="inputbox" size="15"><br>

								<!-- END login_check_passwd -->
								<!-- BEGIN domain_select -->
								<p id="form-login-domain" style="clear: both;">
								<label for="logindomain">{lang_domain}:</label>
								<select name="logindomain" id="logindomain" class="inputbox">
									<!-- BEGIN domain_option -->
									<option value="{domain_name}" {domain_selected}>{domain_name}</option>
									<!-- END domain_option -->
								</select>
								</p>
								<!-- END domain_select -->
								<!-- BEGIN button_block -->
									<div class="button_holder">
										<div class="button1">
											<div class="next">
												<a onclick="document.login.submit();">{lang_login}</a>
											</div>
										</div>
									</div>

									<div class="clr"></div>
		   							<input type="hidden" name="submitit" value="1">
									<input type="submit" style="border: 0; padding: 0; margin: 0; width: 0px; height: 0px;" value="{lang_login}"  name="submitit_">

									<p class="link_group"><a href="{return_sso_login_url}">{lang_return_sso_login}</a></p>

								<!-- END button_block -->
							
							</form>

							<div class="clr"></div>
						</div>

						<div class="b">
							<div class="b">
								<div class="b"></div>
							</div>
						</div>
					</div>

					<p>{instruction}</p>

					<div id="lock"></div>

					<div class="clr"></div>
				</div>

				<div class="b">
					<div class="b">
						<div class="b"></div>
					</div>
				</div>
			</div>
			<noscript>Warning! JavaScript must be enabled for proper operation of the Administrator back-end.</noscript>
			<div class="clr"></div>
		</div>
	</div>
	<div id="border-bottom">
	<div>
		<div>
		</div>
	</div>
</div>
<div id="footer">
	<p class="copyright">
		<a href="http://www.porticoestate.no" target="_blank">{system} {version}</a> is Free Software released under the <a href=
		"http://www.gnu.org/licenses/gpl-2.0.html">GNU/GPL License</a>.
	 </p>
	</div>
</body>
<!-- END login_form -->
</html>
