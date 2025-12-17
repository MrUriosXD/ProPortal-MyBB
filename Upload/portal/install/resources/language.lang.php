<?php
/* INSTALL LANGUAGE VARIABLES */
$l['none'] = 'None';
$l['not_installed'] = 'Not Installed';
$l['installed'] = 'Installed';
$l['not_writable'] = 'Not Writable';
$l['writable'] = 'Writable';
$l['done'] = 'done';
$l['next'] = 'Next';
$l['error'] = 'Error';
$l['multi_byte'] = 'Multi-Byte';
$l['recheck'] = 'Recheck';

$l['title'] = "ProPortal Installation Wizard";
$l['welcome'] = 'Welcome';
$l['license_agreement'] = 'License Agreement';
$l['req_check'] = 'Requirements Check';
$l['table_creation'] = 'Table Creation';
$l['data_insertion'] = 'Data Insertion';
$l['theme_install'] = 'Template Installation';
$l['finish_setup'] = 'Finish Setup';
$l['uninstall'] = 'Uninstall ProPortal';

$l['table_population'] = 'Table Population';
$l['theme_installation'] = 'Theme Insertion';

$l['no_mybb'] = "MyBB isn't installed";
$l['mybb_not_installed'] = "<p>Welcome to the installation wizard for ProPortal. Installation wizard has detected that MyBB isn't installed. MyBB must be installed on your server before starting to install ProPortal. Please, visit ProPortal installation wizard again after you have installed MyBB.</p>
";
$l['already_installed'] = "ProPortal is already installed";
$l['proportal_already_installed'] = '<p>ProPortal has found it\'s already installed on your forum, If you want to uninstall ProPortal from your server please click the button "Next".</p>
<p>Please note that if this wizard fails, you may need to remove the software manually.</p>

<p>This option will delete any existing copy of ProPortal you may have set up. Your ProPortal database tables will be backed up into install folder. Please note that, <span style="color: red;">THIS PROCESS CANNOT BE UNDONE.</span></p>
<p>Click "Next" to completely remove your copy of ProPortal.</p>';

$l['mybb_incorrect_folder'] = "<div class=\"border_wrapper upgrade_note\" style=\"padding: 4px;\">
	<h3>MyBB has detected that it is running from the \"Upload\" directory.</h3>
	<p>While there is nothing wrong with this, it is recommended that your upload the contents of the \"Upload\" directory and not the directory itself.<br /><br />For more information see our <a href=\"http://wiki.mybboard.net/index.php/Help:Upload_Directory\" target=\"_blank\">wiki page</a>.</p>
</div>";

$l['welcome_step'] = '<p>Welcome to the installation wizard for ProPortal {1}. This wizard will install and configure a copy of ProPortal on your server.</p>
<p>Now that you\'ve uploaded the ProPortal files the database and settings need to be created and imported. Below is an outline of what is going to be completed during installation.</p>
<ul>
	<li>ProPortal requirements checked</li>
	<li>Creation of database tables</li>
	<li>Default data inserted</li>
	<li>ProPortal templates imported</li>
</ul>
<p>After each step has successfully been completed, click Next to move on to the next step.</p>
<p>Click "Next" to view the ProPortal license agreement.</p>';

$l['license_step'] = '<div class="license_agreement">
{1}
</div>
<p><strong>By clicking Next, you agree to the terms stated in the ProPortal License Agreement above.</strong></p>';


$l['req_step_top'] = '<p>Before you can install ProPortal, we must check that you meet the minimum requirements for installation.</p>';
$l['req_step_reqtable'] = '<div class="border_wrapper">
			<div class="title">Requirements Check</div>
		<table class="general" cellspacing="0">
		<thead>
			<tr>
				<th colspan="2" class="first last">Requirements</th>
			</tr>
		</thead>
		<tbody>
		<tr class="first">
			<td class="first">PHP Version:</td>
			<td class="last alt_col">{1}</td>
		</tr>
		<tr class="alt_row">
			<td class="first">Supported DB Extensions:</td>
			<td class="last alt_col">{2}</td>
		<tr>
		<tr>
			<td class="first">ProPortal Class:</td>
			<td class="last alt_col">{3}</td>
		</tr>
		</tbody>
		</table>
		</div>';
$l['req_step_reqcomplete'] = '<p><strong>Congratulations, you meet the requirements to run ProPortal.</strong></p>
<p>Click Next to continue with the installation process.</p>';

$l['req_step_span_fail'] = '<span class="fail"><strong>{1}</strong></span>';
$l['req_step_span_pass'] = '<span class="pass">{1}</span>';

$l['req_step_error_box'] = '<p><strong>{1}</strong></p>';
$l['req_step_error_phpversion'] = 'ProPortal Requires PHP 4.1.0 or later to run. You currently have {1} installed.';
$l['req_step_error_dboptions'] = 'ProPortal requires one or more suitable database extensions to be installed. Your server reported that none were available.';
$l['req_step_error_class'] = 'You didn\'t upload ProPortal class file to its required location. Please make sure that all required files were uploaded.';
$l['req_step_error_tablelist'] = '<div class="error">
<h3>Error</h3>
<p>The ProPortal Requirements check failed due to the reasons below. ProPortal installation cannot continue because you did not meet the ProPortal requirements. Please correct the errors below and try again:</p>
{1}
</div>';

$l['db_step_error_invalidengine'] = 'You have selected an invalid database engine. Please make your selection from the list below.';
$l['db_step_error_noconnect'] = 'Could not connect to the database server at \'{1}\' with the supplied username and password. Are you sure the hostname and user details are correct?';
$l['db_step_error_nodbname'] = 'Could not select the database \'{1}\'. Are you sure it exists and the specified username and password have access to it?';
$l['db_step_error_missingencoding'] = 'You have not selected an encoding yet. Please make sure you selected an encoding before continuing. (Select \'UTF-8 Unicode\' if you are not sure)';
$l['db_step_error_sqlite_invalid_dbname'] = 'You may not use relative URLs for SQLite databases. Please use a file system path (ex: /home/user/database.db) for your SQLite database.';

$l['tablecreate_step_connected'] = '<p>Connection to the database server and table you specified was successful.</p>
<p>Database Engine: {1} {2}</p>
<p>The ProPortal database tables will now be created.</p>';
$l['tablecreate_step_created'] = 'Creating table {1}...';
$l['tablecreate_step_done'] = '<p>All tables have been created, click Next to populate them.</p>';

$l['populate_step_insert'] = '<p>Now that the basic tables have been created, it\'s time to insert the default data.</p>';
$l['populate_step_inserted'] = '<p>The default data has successfully been inserted into the database. Click Next to insert ProPortal templates.</p>';


$l['theme_step_importing'] = '<p>Loading and importing theme and template file...</p>';
$l['theme_step_imported'] = '<p>The default theme and template sets have been successfully inserted. Click Next to finish installation process.</p>';

$l['done_step_success'] = '<p class="success">Your copy of ProPortal has successfully been installed.</p>
<p>Thank you for choosing and installing ProPortal.</p>';
$l['done_step_locked'] = '<p>Your installer has been locked. To unlock the installer please delete the \'lock\' file in this directory.</p><p>You may now proceed to your copy of <a href="../../portal.php">ProPortal</a>.</p>';
$l['done_step_dirdelete'] = '<p><strong><span style="colour:red">Please remove this directory before exploring your copy of ProPortal.</span></strong></p>';
$l['done_subscribe_mailing'] = '<div class="error"><p><strong>Make sure you\'re registered to the ProMyBB!</strong></p><p>ProMyBB is the official community of ProPortal. If you\'re having problems with ProPortal, you can find a solution to your problem in our forums. Also, you can download new blocks and suggest one from our users.</p><p>Everytime we release a new version of ProPortal, be it a new feature release or security update, you\'ll be informed.</p><p>This helps keep you up to date with new security releases and ensures you\'re running the latest and greatest version of ProPortal!</p><p><a href="http://www.promybb.com" target="_blank">Click here to go to ProMyBB!</a></p>';

$l['uninstalling'] = 'Uninstalling ProPortal';
$l['uninstall_success'] = '<p>Uninstallation have been succesfully completed. You may now remove files of ProPortal from your server.</p>';
$l['uninstall_fail'] = '<p>Uninstallation haven\'t been completed.</p>';
$l['already_uninstalled'] = '<p>ProPortal is already uninstalled.</p>';

/* Error messages */
$l['locked'] = 'The installer is currently locked, please remove \'lock\' from the install directory to continue';
?>