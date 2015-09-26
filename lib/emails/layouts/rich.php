<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title><?php 
		if (isset($subject)) {
			echo $subject;
		}
	?></title>
	<style type="text/css">
		#outlook a {padding:0;}
		body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}
		.ExternalClass {width:100%;}
		.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */
		#backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
		img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;}
		a img {border:none;}
		.image_fix {display:block;}
		p {margin: 1em 0;}
		h1, h2, h3, h4, h5, h6 {color: black !important;}
		h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {color: blue !important;}
		h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active {
			color: red !important;
		 }

		h1 a:visited, h2 a:visited,  h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited {
			color: purple !important;
		}
		table td {border-collapse: collapse;}
		table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
		a {color: orange;}
		@media only screen and (max-device-width: 480px) {
			a[href^="tel"], a[href^="sms"] {
						text-decoration: none;
						color: black;
						pointer-events: none;
						cursor: default;
					}

			.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
						text-decoration: default;
						color: orange !important;
						pointer-events: auto;
						cursor: default;
					}
		}
		@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
			a[href^="tel"], a[href^="sms"] {
						text-decoration: none;
						color: blue;
						pointer-events: none;
						cursor: default;
					}

			.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
						text-decoration: default;
						color: orange !important;
						pointer-events: auto;
						cursor: default;
					}
		}

		.list-group {
		    margin-bottom: 20px;
		    padding-left: 0;
		}
		.list-group-item {
		    background-color: #fff;
		    border: 1px solid #ddd;
		    display: block;
		    margin-bottom: -1px;
		    padding: 10px 15px;
		    position: relative;
		}
		.list-group-item:first-child {
		    border-top-left-radius: 4px;
		    border-top-right-radius: 4px;
		}
		.list-group-item:last-child {
		    border-bottom-left-radius: 4px;
		    border-bottom-right-radius: 4px;
		    margin-bottom: 0;
		}
		a.list-group-item, button.list-group-item {
		    color: #555;
		}
		a.list-group-item .list-group-item-heading, button.list-group-item .list-group-item-heading {
		    color: #333;
		}
		a.list-group-item:focus, a.list-group-item:hover, button.list-group-item:focus, button.list-group-item:hover {
		    background-color: #f5f5f5;
		    color: #555;
		    text-decoration: none;
		}
		button.list-group-item {
		    text-align: left;
		    width: 100%;
		}
		.list-group-item.disabled, .list-group-item.disabled:focus, .list-group-item.disabled:hover {
		    background-color: #eee;
		    color: #777;
		    cursor: not-allowed;
		}
		.list-group-item.disabled .list-group-item-heading, .list-group-item.disabled:focus .list-group-item-heading, .list-group-item.disabled:hover .list-group-item-heading {
		    color: inherit;
		}
		.list-group-item.disabled .list-group-item-text, .list-group-item.disabled:focus .list-group-item-text, .list-group-item.disabled:hover .list-group-item-text {
		    color: #777;
		}
		.list-group-item.active, .list-group-item.active:focus, .list-group-item.active:hover {
		    background-color: #337ab7;
		    border-color: #337ab7;
		    color: #fff;
		    z-index: 2;
		}
		.list-group-item.active .list-group-item-heading, .list-group-item.active .list-group-item-heading > .small, .list-group-item.active .list-group-item-heading > small, .list-group-item.active:focus .list-group-item-heading, .list-group-item.active:focus .list-group-item-heading > .small, .list-group-item.active:focus .list-group-item-heading > small, .list-group-item.active:hover .list-group-item-heading, .list-group-item.active:hover .list-group-item-heading > .small, .list-group-item.active:hover .list-group-item-heading > small {
		    color: inherit;
		}
		.list-group-item.active .list-group-item-text, .list-group-item.active:focus .list-group-item-text, .list-group-item.active:hover .list-group-item-text {
		    color: #c7ddef;
		}
		.list-group-item-success {
		    background-color: #dff0d8;
		    color: #3c763d;
		}
		a.list-group-item-success, button.list-group-item-success {
		    color: #3c763d;
		}
		a.list-group-item-success .list-group-item-heading, button.list-group-item-success .list-group-item-heading {
		    color: inherit;
		}
		a.list-group-item-success:focus, a.list-group-item-success:hover, button.list-group-item-success:focus, button.list-group-item-success:hover {
		    background-color: #d0e9c6;
		    color: #3c763d;
		}
		a.list-group-item-success.active, a.list-group-item-success.active:focus, a.list-group-item-success.active:hover, button.list-group-item-success.active, button.list-group-item-success.active:focus, button.list-group-item-success.active:hover {
		    background-color: #3c763d;
		    border-color: #3c763d;
		    color: #fff;
		}
		.list-group-item-info {
		    background-color: #d9edf7;
		    color: #31708f;
		}
		a.list-group-item-info, button.list-group-item-info {
		    color: #31708f;
		}
		a.list-group-item-info .list-group-item-heading, button.list-group-item-info .list-group-item-heading {
		    color: inherit;
		}
		a.list-group-item-info:focus, a.list-group-item-info:hover, button.list-group-item-info:focus, button.list-group-item-info:hover {
		    background-color: #c4e3f3;
		    color: #31708f;
		}
		a.list-group-item-info.active, a.list-group-item-info.active:focus, a.list-group-item-info.active:hover, button.list-group-item-info.active, button.list-group-item-info.active:focus, button.list-group-item-info.active:hover {
		    background-color: #31708f;
		    border-color: #31708f;
		    color: #fff;
		}
		.list-group-item-warning {
		    background-color: #fcf8e3;
		    color: #8a6d3b;
		}
		a.list-group-item-warning, button.list-group-item-warning {
		    color: #8a6d3b;
		}
		a.list-group-item-warning .list-group-item-heading, button.list-group-item-warning .list-group-item-heading {
		    color: inherit;
		}
		a.list-group-item-warning:focus, a.list-group-item-warning:hover, button.list-group-item-warning:focus, button.list-group-item-warning:hover {
		    background-color: #faf2cc;
		    color: #8a6d3b;
		}
		a.list-group-item-warning.active, a.list-group-item-warning.active:focus, a.list-group-item-warning.active:hover, button.list-group-item-warning.active, button.list-group-item-warning.active:focus, button.list-group-item-warning.active:hover {
		    background-color: #8a6d3b;
		    border-color: #8a6d3b;
		    color: #fff;
		}
		.list-group-item-danger {
		    background-color: #f2dede;
		    color: #a94442;
		}
		a.list-group-item-danger, button.list-group-item-danger {
		    color: #a94442;
		}
		a.list-group-item-danger .list-group-item-heading, button.list-group-item-danger .list-group-item-heading {
		    color: inherit;
		}
		a.list-group-item-danger:focus, a.list-group-item-danger:hover, button.list-group-item-danger:focus, button.list-group-item-danger:hover {
		    background-color: #ebcccc;
		    color: #a94442;
		}
		a.list-group-item-danger.active, a.list-group-item-danger.active:focus, a.list-group-item-danger.active:hover, button.list-group-item-danger.active, button.list-group-item-danger.active:focus, button.list-group-item-danger.active:hover {
		    background-color: #a94442;
		    border-color: #a94442;
		    color: #fff;
		}
		.list-group-item-heading {
		    margin-bottom: 5px;
		    margin-top: 0;
		}
		.list-group-item-text {
		    line-height: 1.3;
		    margin-bottom: 0;
		}

		@media only screen and (-webkit-min-device-pixel-ratio: 2) {
			/* Put your iPhone 4g styles in here */
		}
		@media only screen and (-webkit-device-pixel-ratio:.75){
			/* Put CSS for low density (ldpi) Android layouts in here */
		}
		@media only screen and (-webkit-device-pixel-ratio:1){
			/* Put CSS for medium density (mdpi) Android layouts in here */
		}
		@media only screen and (-webkit-device-pixel-ratio:1.5){
			/* Put CSS for high density (hdpi) Android layouts in here */
		}
	</style>

</head>
<body>
	<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
	<tr>
		<td><table cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td valign="top"><h1><?php if (isset($subject)) { echo $subject; } ?></h1></td>
			</tr>
			<tr>
				<td valign="top">
				<div class="list-group">
				<?php 
				echo $content; 
				?>
				</div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	</table>
</body>
</html>