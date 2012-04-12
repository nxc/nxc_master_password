<?php
/**
 * @package nxcMasterPassword
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    22 Sep 2011
 **/

require 'autoload.php';

$cli = eZCLI::instance();

$scriptSettings = array(
	'description'    => 'Creates transaltion files',
	'use-session'    => false,
	'use-modules'    => false,
	'use-extensions' => true
);
$script = eZScript::instance( $scriptSettings );

$script->startup();
$script->initialize();

$options = $script->getOptions(
	'',
	'[password]',
	array(
		'password' => 'New password'
	)
);

if(
	count( $options['arguments'] ) < 1
	|| strlen( $options['arguments'][0] ) === 0
) {
	$cli->error( 'Specify new password' );
	$script->shutdown( 1 );
}

$ini = eZINI::instance( 'nxcmasterpassword.ini' );
$password = $options['arguments'][0];
$password = md5( md5( $password ) . $ini->variable( 'General', 'Seed' ) );
$ini->setVariable( 'General', 'MasterPassword', $password );

$file   = 'nxcmasterpassword.ini.append.php';
$path   = eZExtension::baseDirectory() . '/nxc_master_password/settings';
$result = $ini->save(
	$file,
	false,
	false,
	false,
	$path,
	false,
	true
);

if( $result === false ) {
	$cli->error( 'Cannot update "' . $path . '/' . $file . '" settings file' );
	$script->shutdown( 1 );
}

eZCache::clearByTag( 'Ini' );
$cli->output( 'Password stored' );
$script->shutdown( 0 );
?>
