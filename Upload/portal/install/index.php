<?php
/***************************************************************
 * ProPortal
 * Copyright © 2010 ProMyBB, All Rights Reserved
 *
 * Website: http://www.promybb.com
 *
 * MyBB Installation Wizard originally written by MyBB Group
 * Website: http://www.mybboard.net
 ***************************************************************/

if(function_exists("unicode_decode"))
{
    // Unicode extension introduced in 6.0
    error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_STRICT);
}
elseif(defined("E_DEPRECATED"))
{
    // E_DEPRECATED introduced in 5.3
    error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
}
else
{
    error_reporting(E_ALL & ~E_NOTICE);
}

@set_time_limit(0);

define('MYBB_ROOT', dirname(dirname(dirname(__FILE__)))."/");
define("INSTALL_ROOT", dirname(__FILE__)."/");
define("TIME_NOW", time());
define("IN_MYBB", 1);
define("IN_INSTALL", 1);

require_once MYBB_ROOT.'inc/class_core.php';
$mybb = new MyBB;

require_once MYBB_ROOT.'inc/class_error.php';
$error_handler = new errorHandler();

// Include the files necessary for installation
require_once MYBB_ROOT.'inc/class_timers.php';
require_once MYBB_ROOT.'inc/functions.php';

// Perform a check if MyBB is already installed or not
$mybb_installed = false;
if(file_exists(MYBB_ROOT."/inc/config.php"))
{
	require MYBB_ROOT."/inc/config.php";
	if(is_array($config))
	{
		$mybb_installed = true;
		$mybb->config = &$config;
	}
}
if(file_exists("lock"))
{
	$portal_installed = true;
}

require_once MYBB_ROOT.'inc/class_xml.php';
require_once MYBB_ROOT.'inc/functions_user.php';
require_once MYBB_ROOT.'inc/class_language.php';
$lang = new MyLanguage();
$lang->set_path(MYBB_ROOT.'portal/install/resources');
$lang->load('language');

// Include the installation resources
require_once INSTALL_ROOT.'resources/output.php';
$output = new installerOutput;

// Version information of ProPortal
$portal_version = array(
	"version" => "1.0 Beta",
	"version_code" => "10B",
);

$dboptions = array();

if(function_exists('mysqli_connect'))
{
	$dboptions['mysqli'] = array(
		'class' => 'DB_MySQLi',
		'title' => 'MySQL Improved',
		'short_title' => 'MySQLi',
		'structure_file' => 'mysql_db_tables.php',
		'population_file' => 'mysql_db_inserts.php'
	);
}

if(function_exists('mysql_connect'))
{
	$dboptions['mysql'] = array(
		'class' => 'DB_MySQL',
		'title' => 'MySQL',
		'short_title' => 'MySQL',
		'structure_file' => 'mysql_db_tables.php',
		'population_file' => 'mysql_db_inserts.php'
	);
}

if(file_exists('lock'))
{
	$output->print_error($lang->locked);
}
elseif($mybb_installed != true && !$mybb->input['action'])
{
	$output->print_header($lang->no_mybb, "errormsg", 0);
	echo $lang->mybb_not_installed;
	$output->print_footer();
}
elseif(file_exists('installed') && !$mybb->input['action'])
{
	$output->print_header($lang->already_installed, "errormsg");
	echo $lang->proportal_already_installed;
	$output->print_footer('uninstall');
}
else
{
	$output->steps = array(
		'intro' => $lang->welcome,
		'license' => $lang->license_agreement,
		'requirements_check' => $lang->req_check,
		'create_tables' => $lang->table_creation,
		'populate_tables' => $lang->data_insertion,
		'templates' => $lang->theme_install,
		'final' => $lang->finish_setup,
	);
	
	if(!isset($mybb->input['action']))
	{
		$mybb->input['action'] = 'intro';
	}
	
	switch($mybb->input['action'])
	{
		case 'license':
			license_agreement();
			break;
		case 'requirements_check':
			requirements_check();
			break;
		case 'create_tables':
			create_tables();
			break;
		case 'populate_tables':
			populate_tables();
			break;
		case 'templates':
			insert_templates();
			break;
		case 'final':
			install_done();
			break;
		case 'uninstall':
			uninstall();
			break;
		default:
			intro();
			break;
	}
}

function intro()
{
	global $output, $mybb, $lang, $portal_version;
	
	$output->print_header($lang->welcome, 'welcome');
	if(strpos(strtolower($_SERVER['PHP_SELF']), "upload/") !== false)
	{
		echo $lang->sprintf($lang->mybb_incorrect_folder);
	}
	echo $lang->sprintf($lang->welcome_step, $portal_version['version']);
	$output->print_footer('license');
}

function license_agreement()
{
	global $output, $lang, $mybb;
	
	$output->print_header($lang->license_agreement, 'license');

	$license = '
<pre>
<strong>Attribution-NonCommercial-ShareAlike 3.0 Unported</strong>
License

THE WORK (AS DEFINED BELOW) IS PROVIDED UNDER THE TERMS OF THIS CREATIVE COMMONS PUBLIC LICENSE ("CCPL" OR "LICENSE"). THE WORK IS PROTECTED BY COPYRIGHT AND/OR OTHER APPLICABLE LAW. ANY USE OF THE WORK OTHER THAN AS AUTHORIZED UNDER THIS LICENSE OR COPYRIGHT LAW IS PROHIBITED.

BY EXERCISING ANY RIGHTS TO THE WORK PROVIDED HERE, YOU ACCEPT AND AGREE TO BE BOUND BY THE TERMS OF THIS LICENSE. TO THE EXTENT THIS LICENSE MAY BE CONSIDERED TO BE A CONTRACT, THE LICENSOR GRANTS YOU THE RIGHTS CONTAINED HERE IN CONSIDERATION OF YOUR ACCEPTANCE OF SUCH TERMS AND CONDITIONS.

1. Definitions

   1. "Adaptation" means a work based upon the Work, or upon the Work and other pre-existing works, such as a translation, adaptation, derivative work, arrangement of music or other alterations of a literary or artistic work, or phonogram or performance and includes cinematographic adaptations or any other form in which the Work may be recast, transformed, or adapted including in any form recognizably derived from the original, except that a work that constitutes a Collection will not be considered an Adaptation for the purpose of this License. For the avoidance of doubt, where the Work is a musical work, performance or phonogram, the synchronization of the Work in timed-relation with a moving image ("synching") will be considered an Adaptation for the purpose of this License.
   2. "Collection" means a collection of literary or artistic works, such as encyclopedias and anthologies, or performances, phonograms or broadcasts, or other works or subject matter other than works listed in Section 1(g) below, which, by reason of the selection and arrangement of their contents, constitute intellectual creations, in which the Work is included in its entirety in unmodified form along with one or more other contributions, each constituting separate and independent works in themselves, which together are assembled into a collective whole. A work that constitutes a Collection will not be considered an Adaptation (as defined above) for the purposes of this License.
   3. "Distribute" means to make available to the public the original and copies of the Work or Adaptation, as appropriate, through sale or other transfer of ownership.
   4. "License Elements" means the following high-level license attributes as selected by Licensor and indicated in the title of this License: Attribution, Noncommercial, ShareAlike.
   5. "Licensor" means the individual, individuals, entity or entities that offer(s) the Work under the terms of this License.
   6. "Original Author" means, in the case of a literary or artistic work, the individual, individuals, entity or entities who created the Work or if no individual or entity can be identified, the publisher; and in addition (i) in the case of a performance the actors, singers, musicians, dancers, and other persons who act, sing, deliver, declaim, play in, interpret or otherwise perform literary or artistic works or expressions of folklore; (ii) in the case of a phonogram the producer being the person or legal entity who first fixes the sounds of a performance or other sounds; and, (iii) in the case of broadcasts, the organization that transmits the broadcast.
   7. "Work" means the literary and/or artistic work offered under the terms of this License including without limitation any production in the literary, scientific and artistic domain, whatever may be the mode or form of its expression including digital form, such as a book, pamphlet and other writing; a lecture, address, sermon or other work of the same nature; a dramatic or dramatico-musical work; a choreographic work or entertainment in dumb show; a musical composition with or without words; a cinematographic work to which are assimilated works expressed by a process analogous to cinematography; a work of drawing, painting, architecture, sculpture, engraving or lithography; a photographic work to which are assimilated works expressed by a process analogous to photography; a work of applied art; an illustration, map, plan, sketch or three-dimensional work relative to geography, topography, architecture or science; a performance; a broadcast; a phonogram; a compilation of data to the extent it is protected as a copyrightable work; or a work performed by a variety or circus performer to the extent it is not otherwise considered a literary or artistic work.
   8. "You" means an individual or entity exercising rights under this License who has not previously violated the terms of this License with respect to the Work, or who has received express permission from the Licensor to exercise rights under this License despite a previous violation.
   9. "Publicly Perform" means to perform public recitations of the Work and to communicate to the public those public recitations, by any means or process, including by wire or wireless means or public digital performances; to make available to the public Works in such a way that members of the public may access these Works from a place and at a place individually chosen by them; to perform the Work to the public by any means or process and the communication to the public of the performances of the Work, including by public digital performance; to broadcast and rebroadcast the Work by any means including signs, sounds or images.
  10. "Reproduce" means to make copies of the Work by any means including without limitation by sound or visual recordings and the right of fixation and reproducing fixations of the Work, including storage of a protected performance or phonogram in digital form or other electronic medium.

2. Fair Dealing Rights. Nothing in this License is intended to reduce, limit, or restrict any uses free from copyright or rights arising from limitations or exceptions that are provided for in connection with the copyright protection under copyright law or other applicable laws.

3. License Grant. Subject to the terms and conditions of this License, Licensor hereby grants You a worldwide, royalty-free, non-exclusive, perpetual (for the duration of the applicable copyright) license to exercise the rights in the Work as stated below:

   1. to Reproduce the Work, to incorporate the Work into one or more Collections, and to Reproduce the Work as incorporated in the Collections;
   2. to create and Reproduce Adaptations provided that any such Adaptation, including any translation in any medium, takes reasonable steps to clearly label, demarcate or otherwise identify that changes were made to the original Work. For example, a translation could be marked "The original work was translated from English to Spanish," or a modification could indicate "The original work has been modified.";
   3. to Distribute and Publicly Perform the Work including as incorporated in Collections; and,
   4. to Distribute and Publicly Perform Adaptations.

The above rights may be exercised in all media and formats whether now known or hereafter devised. The above rights include the right to make such modifications as are technically necessary to exercise the rights in other media and formats. Subject to Section 8(f), all rights not expressly granted by Licensor are hereby reserved, including but not limited to the rights described in Section 4(e).

4. Restrictions. The license granted in Section 3 above is expressly made subject to and limited by the following restrictions:

   1. You may Distribute or Publicly Perform the Work only under the terms of this License. You must include a copy of, or the Uniform Resource Identifier (URI) for, this License with every copy of the Work You Distribute or Publicly Perform. You may not offer or impose any terms on the Work that restrict the terms of this License or the ability of the recipient of the Work to exercise the rights granted to that recipient under the terms of the License. You may not sublicense the Work. You must keep intact all notices that refer to this License and to the disclaimer of warranties with every copy of the Work You Distribute or Publicly Perform. When You Distribute or Publicly Perform the Work, You may not impose any effective technological measures on the Work that restrict the ability of a recipient of the Work from You to exercise the rights granted to that recipient under the terms of the License. This Section 4(a) applies to the Work as incorporated in a Collection, but this does not require the Collection apart from the Work itself to be made subject to the terms of this License. If You create a Collection, upon notice from any Licensor You must, to the extent practicable, remove from the Collection any credit as required by Section 4(d), as requested. If You create an Adaptation, upon notice from any Licensor You must, to the extent practicable, remove from the Adaptation any credit as required by Section 4(d), as requested.
   2. You may Distribute or Publicly Perform an Adaptation only under: (i) the terms of this License; (ii) a later version of this License with the same License Elements as this License; (iii) a Creative Commons jurisdiction license (either this or a later license version) that contains the same License Elements as this License (e.g., Attribution-NonCommercial-ShareAlike 3.0 US) ("Applicable License"). You must include a copy of, or the URI, for Applicable License with every copy of each Adaptation You Distribute or Publicly Perform. You may not offer or impose any terms on the Adaptation that restrict the terms of the Applicable License or the ability of the recipient of the Adaptation to exercise the rights granted to that recipient under the terms of the Applicable License. You must keep intact all notices that refer to the Applicable License and to the disclaimer of warranties with every copy of the Work as included in the Adaptation You Distribute or Publicly Perform. When You Distribute or Publicly Perform the Adaptation, You may not impose any effective technological measures on the Adaptation that restrict the ability of a recipient of the Adaptation from You to exercise the rights granted to that recipient under the terms of the Applicable License. This Section 4(b) applies to the Adaptation as incorporated in a Collection, but this does not require the Collection apart from the Adaptation itself to be made subject to the terms of the Applicable License.
   3. You may not exercise any of the rights granted to You in Section 3 above in any manner that is primarily intended for or directed toward commercial advantage or private monetary compensation. The exchange of the Work for other copyrighted works by means of digital file-sharing or otherwise shall not be considered to be intended for or directed toward commercial advantage or private monetary compensation, provided there is no payment of any monetary compensation in con-nection with the exchange of copyrighted works.
   4. If You Distribute, or Publicly Perform the Work or any Adaptations or Collections, You must, unless a request has been made pursuant to Section 4(a), keep intact all copyright notices for the Work and provide, reasonable to the medium or means You are utilizing: (i) the name of the Original Author (or pseudonym, if applicable) if supplied, and/or if the Original Author and/or Licensor designate another party or parties (e.g., a sponsor institute, publishing entity, journal) for attribution ("Attribution Parties") in Licensor\'s copyright notice, terms of service or by other reasonable means, the name of such party or parties; (ii) the title of the Work if supplied; (iii) to the extent reasonably practicable, the URI, if any, that Licensor specifies to be associated with the Work, unless such URI does not refer to the copyright notice or licensing information for the Work; and, (iv) consistent with Section 3(b), in the case of an Adaptation, a credit identifying the use of the Work in the Adaptation (e.g., "French translation of the Work by Original Author," or "Screenplay based on original Work by Original Author"). The credit required by this Section 4(d) may be implemented in any reasonable manner; provided, however, that in the case of a Adaptation or Collection, at a minimum such credit will appear, if a credit for all contributing authors of the Adaptation or Collection appears, then as part of these credits and in a manner at least as prominent as the credits for the other contributing authors. For the avoidance of doubt, You may only use the credit required by this Section for the purpose of attribution in the manner set out above and, by exercising Your rights under this License, You may not implicitly or explicitly assert or imply any connection with, sponsorship or endorsement by the Original Author, Licensor and/or Attribution Parties, as appropriate, of You or Your use of the Work, without the separate, express prior written permission of the Original Author, Licensor and/or Attribution Parties.
   5.

      For the avoidance of doubt:
         1. Non-waivable Compulsory License Schemes. In those jurisdictions in which the right to collect royalties through any statutory or compulsory licensing scheme cannot be waived, the Licensor reserves the exclusive right to collect such royalties for any exercise by You of the rights granted under this License;
         2. Waivable Compulsory License Schemes. In those jurisdictions in which the right to collect royalties through any statutory or compulsory licensing scheme can be waived, the Licensor reserves the exclusive right to collect such royalties for any exercise by You of the rights granted under this License if Your exercise of such rights is for a purpose or use which is otherwise than noncommercial as permitted under Section 4(c) and otherwise waives the right to collect royalties through any statutory or compulsory licensing scheme; and,
         3. Voluntary License Schemes. The Licensor reserves the right to collect royalties, whether individually or, in the event that the Licensor is a member of a collecting society that administers voluntary licensing schemes, via that society, from any exercise by You of the rights granted under this License that is for a purpose or use which is otherwise than noncommercial as permitted under Section 4(c).
   6. Except as otherwise agreed in writing by the Licensor or as may be otherwise permitted by applicable law, if You Reproduce, Distribute or Publicly Perform the Work either by itself or as part of any Adaptations or Collections, You must not distort, mutilate, modify or take other derogatory action in relation to the Work which would be prejudicial to the Original Author\'s honor or reputation. Licensor agrees that in those jurisdictions (e.g. Japan), in which any exercise of the right granted in Section 3(b) of this License (the right to make Adaptations) would be deemed to be a distortion, mutilation, modification or other derogatory action prejudicial to the Original Author\'s honor and reputation, the Licensor will waive or not assert, as appropriate, this Section, to the fullest extent permitted by the applicable national law, to enable You to reasonably exercise Your right under Section 3(b) of this License (right to make Adaptations) but not otherwise.

5. Representations, Warranties and Disclaimer

UNLESS OTHERWISE MUTUALLY AGREED TO BY THE PARTIES IN WRITING AND TO THE FULLEST EXTENT PERMITTED BY APPLICABLE LAW, LICENSOR OFFERS THE WORK AS-IS AND MAKES NO REPRESENTATIONS OR WARRANTIES OF ANY KIND CONCERNING THE WORK, EXPRESS, IMPLIED, STATUTORY OR OTHERWISE, INCLUDING, WITHOUT LIMITATION, WARRANTIES OF TITLE, MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, NONINFRINGEMENT, OR THE ABSENCE OF LATENT OR OTHER DEFECTS, ACCURACY, OR THE PRESENCE OF ABSENCE OF ERRORS, WHETHER OR NOT DISCOVERABLE. SOME JURISDICTIONS DO NOT ALLOW THE EXCLUSION OF IMPLIED WARRANTIES, SO THIS EXCLUSION MAY NOT APPLY TO YOU.

6. Limitation on Liability. EXCEPT TO THE EXTENT REQUIRED BY APPLICABLE LAW, IN NO EVENT WILL LICENSOR BE LIABLE TO YOU ON ANY LEGAL THEORY FOR ANY SPECIAL, INCIDENTAL, CONSEQUENTIAL, PUNITIVE OR EXEMPLARY DAMAGES ARISING OUT OF THIS LICENSE OR THE USE OF THE WORK, EVEN IF LICENSOR HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.

7. Termination

   1. This License and the rights granted hereunder will terminate automatically upon any breach by You of the terms of this License. Individuals or entities who have received Adaptations or Collections from You under this License, however, will not have their licenses terminated provided such individuals or entities remain in full compliance with those licenses. Sections 1, 2, 5, 6, 7, and 8 will survive any termination of this License.
   2. Subject to the above terms and conditions, the license granted here is perpetual (for the duration of the applicable copyright in the Work). Notwithstanding the above, Licensor reserves the right to release the Work under different license terms or to stop distributing the Work at any time; provided, however that any such election will not serve to withdraw this License (or any other license that has been, or is required to be, granted under the terms of this License), and this License will continue in full force and effect unless terminated as stated above.

8. Miscellaneous

   1. Each time You Distribute or Publicly Perform the Work or a Collection, the Licensor offers to the recipient a license to the Work on the same terms and conditions as the license granted to You under this License.
   2. Each time You Distribute or Publicly Perform an Adaptation, Licensor offers to the recipient a license to the original Work on the same terms and conditions as the license granted to You under this License.
   3. If any provision of this License is invalid or unenforceable under applicable law, it shall not affect the validity or enforceability of the remainder of the terms of this License, and without further action by the parties to this agreement, such provision shall be reformed to the minimum extent necessary to make such provision valid and enforceable.
   4. No term or provision of this License shall be deemed waived and no breach consented to unless such waiver or consent shall be in writing and signed by the party to be charged with such waiver or consent.
   5. This License constitutes the entire agreement between the parties with respect to the Work licensed here. There are no understandings, agreements or representations with respect to the Work not specified here. Licensor shall not be bound by any additional provisions that may appear in any communication from You. This License may not be modified without the mutual written agreement of the Licensor and You.
   6. The rights granted under, and the subject matter referenced, in this License were drafted utilizing the terminology of the Berne Convention for the Protection of Literary and Artistic Works (as amended on September 28, 1979), the Rome Convention of 1961, the WIPO Copyright Treaty of 1996, the WIPO Performances and Phonograms Treaty of 1996 and the Universal Copyright Convention (as revised on July 24, 1971). These rights and subject matter take effect in the relevant jurisdiction in which the License terms are sought to be enforced according to the corresponding provisions of the implementation of those treaty provisions in the applicable national law. If the standard suite of rights granted under applicable copyright law includes additional rights not granted under this License, such additional rights are deemed to be included in the License; this License is not intended to restrict the license of any rights under applicable law.
</pre>
';
	$license = wordwrap($license, 75, "\n");

	echo $lang->sprintf($lang->license_step, $license);
	$output->print_footer('requirements_check');
}

function requirements_check()
{
	global $output, $mybb, $dboptions, $lang;

	$mybb->input['action'] = "requirements_check";
	$output->print_header($lang->req_check, 'requirements');
	echo $lang->req_step_top;
	$errors = array();
	$showerror = 0;

	// Check PHP Version
	$phpversion = @phpversion();
	if($phpversion < '4.1.0')
	{
		$errors[] = $lang->sprintf($lang->req_step_error_box, $lang->sprintf($lang->req_step_error_phpversion, $phpversion));
		$phpversion = $lang->sprintf($lang->req_step_span_fail, $phpversion);
		$showerror = 1;
	}
	else
	{
		$phpversion = $lang->sprintf($lang->req_step_span_pass, $phpversion);
	}
	
	// Check database engines
	if(count($dboptions) < 1)
	{
		$errors[] = $lang->sprintf($lang->req_step_error_box, $lang->req_step_error_dboptions);
		$dbsupportlist = $lang->sprintf($lang->req_step_span_fail, $lang->none);
		$showerror = 1;
	}
	else
	{
		foreach($dboptions as $dboption)
		{
			$dbsupportlist[] = $dboption['title'];
		}
		$dbsupportlist = implode(', ', $dbsupportlist);
	}
	
	if(!file_exists(MYBB_ROOT.'portal/inc/portal.class.php'))
	{
		$classstatus = $lang->sprintf($lang->req_step_span_fail, $lang->not_installed);
		$errors[] = $lang->sprintf($lang->req_step_error_box, $lang->req_step_error_class);
		$showerror = 1;
	}
	else
	{
		$classstatus = $lang->sprintf($lang->req_step_span_pass, $lang->installed);
	}

	// Output requirements page
	echo $lang->sprintf($lang->req_step_reqtable, $phpversion, $dbsupportlist, $classstatus);

	if($showerror == 1)
	{
		$error_list = error_list($errors);
		echo $lang->sprintf($lang->req_step_error_tablelist, $error_list);
		echo "\n			<input type=\"hidden\" name=\"action\" value=\"{$mybb->input['action']}\" />";
		echo "\n				<div id=\"next_button\"><input type=\"submit\" class=\"submit_button\" value=\"{$lang->recheck} &raquo;\" /></div><br style=\"clear: both;\" />\n";
		$output->print_footer();
	}
	else
	{
		echo $lang->req_step_reqcomplete;
		$output->print_footer('create_tables');
	}
}

function create_tables()
{
	global $config, $output, $dbinfo, $errors, $mybb, $dboptions, $lang;
	
	if(!file_exists(MYBB_ROOT."inc/db_{$config['database']['type']}.php"))
	{
		$errors[] = $lang->db_step_error_invalidengine;
	}

	// Attempt to connect to the db
	require_once MYBB_ROOT."inc/db_{$config['database']['type']}.php";
	switch($config['database']['type'])
	{
		case "mysqli":
			$db = new DB_MySQLi;
			break;
		default:
			$db = new DB_MySQL;
	}
 	$db->error_reporting = 0;

	$connect_array = array(
		"hostname" => $config['database']['hostname'],
		"username" => $config['database']['username'],
		"password" => $config['database']['password'],
		"database" => $config['database']['database'],
		"table_prefix" => $config['database']['table_prefix']
	);

	$connection = $db->connect($connect_array);
	if(!$connection)
	{
		$errors[] = $lang->sprintf($lang->db_step_error_noconnect, $connect_array['hostname']);
	}
	// double check if the DB exists for MySQL
	elseif(method_exists($db, 'select_db') && !$db->select_db($connect_array['database']))
	{
		$errors[] = $lang->sprintf($lang->db_step_error_nodbname, $connect_array['database']);
	}

	if(is_array($errors))
	{
		print_r($errors);
	}
	
	// Decide if we can use a database encoding or not
	if($db->fetch_db_charsets() != false)
	{
		$db_encoding = "\$config['database']['encoding'] = '{$config['encoding']}';";
	}
	else
	{
		$db_encoding = "// \$config['database']['encoding'] = '{$config['encoding']}';";
	}

	// Error reporting back on
 	$db->error_reporting = 1;

	$output->print_header($lang->table_creation, 'createtables');
	echo $lang->sprintf($lang->tablecreate_step_connected, $db->short_title, $db->get_version());
	
	$structure_file = "mysql_db_tables.php";

	require_once INSTALL_ROOT."resources/{$structure_file}";
	foreach($tables as $val)
	{
		$val = preg_replace('#mybb_(\S+?)([\s\.,\(]|$)#', $connect_array['table_prefix'].'\\1\\2', $val);
		$val = preg_replace('#;$#', $db->build_create_table_collation().";", $val);
		preg_match('#CREATE TABLE (\S+)(\s?|\(?)\(#i', $val, $match);
		if($match[1])
		{
			$db->drop_table($match[1], false, false);
			echo $lang->sprintf($lang->tablecreate_step_created, $match[1]);
		}
		$db->query($val);
		if($match[1])
		{
			echo $lang->done . "<br />\n";
		}
	}
	echo $lang->tablecreate_step_done;
	$output->print_footer('populate_tables');
}

function populate_tables()
{
	global $db, $config, $output, $lang, $cache;

	$db = db_connection($config);

	$output->print_header($lang->table_population, 'tablepopulate');
	echo $lang->sprintf($lang->populate_step_insert);

	if($dboptions[$db->type]['population_file'])
	{
		$population_file = $dboptions[$db->type]['population_file'];
	}
	else
	{
		$population_file = 'mysql_db_inserts.php';
	}
	
	require_once MYBB_ROOT.'inc/class_datacache.php';
	$cache = new datacache;
	$usergroups = $cache->read("usergroups");
	$groups = array();
	foreach($usergroups as $group)
	{
		$groups[] = $group['gid'];
	}
	$groups = implode(",", $groups);

	require_once INSTALL_ROOT."resources/{$population_file}";
	foreach($inserts as $val)
	{
		$val = preg_replace('#mybb_(\S+?)([\s\.,]|$)#', $config['database']['table_prefix'].'\\1\\2', $val);
		$val = str_replace("1,2,3,4,5,6,7", $groups, $val);
		$db->query($val);
	}

	echo $lang->populate_step_inserted;
	$output->print_footer('templates');
}

function insert_templates()
{
	global $config, $output, $cache, $db, $lang, $mybb;

	$db = db_connection($config);

	require_once MYBB_ROOT.'inc/class_datacache.php';
	$cache = new datacache;

	$output->print_header($lang->theme_installation, 'theme');

	echo $lang->theme_step_importing;

	$contents = @file_get_contents(INSTALL_ROOT.'resources/proportal_templates.xml');
	
	import_template($contents, array("templateset" => -1));

	echo $lang->theme_step_imported;
	$output->print_footer('final');
}

function install_done()
{
	global $config, $output, $db, $mybb, $errors, $lang;

	$output->print_header($lang->finish_setup, 'finish');

	echo $lang->done_step_success;

	$written = 0;
	if(is_writable('./'))
	{
		$lock = @fopen('./lock', 'w');
		$written = @fwrite($lock, '1');
		@fclose($lock);
		if($written)
		{
			echo $lang->done_step_locked;
		}
	}
	$installed_written = 0;
	if(is_writable('./'))
	{
		$lock = @fopen('./installed', 'w');
		$installed_written = @fwrite($lock, '1');
		@fclose($lock);
	}
	if(!$written && !$installed_written)
	{
		echo $lang->done_step_dirdelete;
	}
	echo $lang->done_subscribe_mailing;
	$output->print_footer('');
}

function uninstall()
{
	global $config, $db, $mybb, $output, $lang;
	
	$output->steps = array(
		'uninstall' => $lang->uninstall,
	);

	require_once MYBB_ROOT."inc/db_{$config['database']['type']}.php";
	switch($config['database']['type'])
	{
		case "mysqli":
			$db = new DB_MySQLi;
			break;
		default:
			$db = new DB_MySQL;
	}
 	$db->error_reporting = 0;

	$connect_array = array(
		"hostname" => $config['database']['hostname'],
		"username" => $config['database']['username'],
		"password" => $config['database']['password'],
		"database" => $config['database']['database'],
		"table_prefix" => $config['database']['table_prefix']
	);

	$connection = $db->connect($connect_array);
	if(!$connection)
	{
		$errors[] = $lang->sprintf($lang->db_step_error_noconnect, $connect_array['hostname']);
	}
	// double check if the DB exists for MySQL
	elseif(method_exists($db, 'select_db') && !$db->select_db($connect_array['database']))
	{
		$errors[] = $lang->sprintf($lang->db_step_error_nodbname, $connect_array['database']);
	}

	if(is_array($errors))
	{
		print_r($errors);
	}
	
	$output->print_header($lang->uninstalling, 'welcome');
	$db->set_table_prefix($config['database']['table_prefix']);

	$tables = array("portal_blocks","portal_pages","portal_settings");

	$table_count = 0;
	foreach($tables as $table)
	{
		if($db->table_exists($table))
		{
			++$table_count;
		}
	}

	if($table_count > 0)
	{
		// First back them up
		backup_tables($tables);
		
		// And then delete...
		foreach($tables as $table)
		{
			if($db->table_exists($table))
			{
				$db->write_query("DROP TABLE ".$db->table_prefix."{$table}");
			}
		}
		
		$templates = array("pro_portal","pro_portal_announcement","pro_portal_block","pro_portal_left","pro_portal_page","pro_portal_right");
		foreach($templates as $template)
		{
			$db->delete_query("templates", "title='{$template}'");
		}
		
		$file_open = fopen("./installed", "w");
		if($file_open)
		{
			fclose($file_open);
		}
		@unlink("./installed");

		if(!$db->table_exists("portal_blocks") || !$db->table_exists("portal_pages") || !$db->table_exists("portal_settings"))
		{
			echo $lang->uninstall_success;
			$output->print_footer();
		}
		else
		{
			echo $lang->uninstall_fail;
		}
	}
	else
	{
		echo $lang->already_uninstalled;
		$output->print_footer();
	}
}

function db_connection($config)
{
	require_once MYBB_ROOT."inc/db_{$config['database']['type']}.php";
	switch($config['database']['type'])
	{
		case "mysqli":
			$db = new DB_MySQLi;
			break;
		default:
			$db = new DB_MySQL;
	}
	
	// Connect to Database
	define('TABLE_PREFIX', $config['database']['table_prefix']);

	$db->connect($config['database']);
	$db->set_table_prefix(TABLE_PREFIX);
	$db->type = $config['database']['type'];
	
	return $db;
}

function error_list($array)
{
	$string = "<ul>\n";
	foreach($array as $error)
	{
		$string .= "<li>{$error}</li>\n";
	}
	$string .= "</ul>\n";
	return $string;
}

function import_template($xml, $options=array())
{
	global $mybb, $db;
	
	require_once MYBB_ROOT."inc/class_xml.php";

	$parser = new XMLParser($xml);
	$tree = $parser->get_tree();

	if(!is_array($tree) || !is_array($tree['theme']))
	{
		return -1;
	}
	
	$theme = $tree['theme'];
	$sid = $options['templateset'];
	
	$templates = $theme['templates']['template'];
	if(is_array($templates))
	{
		// Theme only has one custom template
		if(array_key_exists("attributes", $templates))
		{
			$templates = array($templates);
		}
	}

	foreach($templates as $template)
	{
		// PostgreSQL causes apache to stop sending content sometimes and 
		// causes the page to stop loading during many queries all at one time
		if($db->engine == "pgsql")
		{
			echo " ";
			flush();
		}
		
		$db->delete_query("templates", "title='{$template['attributes']['name']}'");
		
		$new_template = array(
			"title" => $db->escape_string($template['attributes']['name']),
			"template" => $db->escape_string($template['value']),
			"sid" => $db->escape_string($sid),
			"version" => $db->escape_string($template['attributes']['version']),
			"dateline" => TIME_NOW
		);
		$db->insert_query("templates", $new_template);
	}
}

function backup_tables($tables)
{
	global $db, $config;
	
	$db = db_connection($config);
	
	foreach($tables as $table)
	{
		$field_list = array();
		$fields_array = $db->show_fields_from($table);
		foreach($fields_array as $field)
		{
			$field_list[] = $field['Field'];
		}

		$fields = implode(",", $field_list);
	
		$structure = $db->show_create_table($table).";\n\n";
		$contents .= $structure;
		
		$query = $db->simple_select($table);
		while($row = $db->fetch_array($query))
		{
			$insert = "INSERT INTO {$table} ($fields) VALUES (";
			$comma = '';
			foreach($field_list as $field)
			{
				if(!isset($row[$field]) || trim($row[$field]) == "")
				{
					$insert .= $comma."''";
				}
				else
				{
					$insert .= $comma."'".$db->escape_string($row[$field])."'";
				}
				$comma = ',';
			}
			$insert .= ");\n";
			$contents .= $insert;
		}
		$contents .= "\n";
	}
	
	$handle = fopen('db-backup-'.time().'.sql','w+');
	fwrite($handle,$contents);
	fclose($handle);
}
?>