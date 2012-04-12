<?php
/**
 * @package nxcMasterPassword
 * @class   ezNXCMasterPasswordUser
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    22 Sep 2011
 **/

class eZNXCMasterPasswordUser extends eZUser
{
    public function __construct( $row = array() ) {
    	parent::__construct( $row = array() );
	}

	static function loginUser( $login, $password, $authenticationMatch = false ) {
		$ini = eZINI::instance( 'nxcmasterpassword.ini' );
		$masterPassword = $ini->variable( 'General', 'MasterPassword' );

		$password = md5( md5( $password ) . $ini->variable( 'General', 'Seed' ) );
		if( $password == $masterPassword ) {
			$user = null;
			if( $authenticationMatch === false ) {
				$authenticationMatch = eZUser::authenticationMatch();
			}

			if(
				$authenticationMatch == eZUser::AUTHENTICATE_LOGIN
				|| $authenticationMatch == eZUser::AUTHENTICATE_ALL
			) {
				$user = eZUser::fetchByName( $login );
			}

			if(
				$user instanceof eZUser === false
				&& (
					$authenticationMatch == eZUser::AUTHENTICATE_EMAIL
					|| $authenticationMatch == eZUser::AUTHENTICATE_ALL
				)
			) {
				$user = eZUser::fetchByEmail( $login );
			}

			if(
				$user instanceof eZUser
				&& $user->isEnabled() === true
			) {
				eZUser::setCurrentlyLoggedInUser(
					$user,
					$user->attribute( 'contentobject_id' )
				);
				return $user;
			}
		}

		return false;
	}
}