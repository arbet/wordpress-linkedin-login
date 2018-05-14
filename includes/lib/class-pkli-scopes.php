<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Scope defines list of available permissions
 */

class Pkli_Scopes {
    /**
     * Allows to read basic information about profile, such as name
     */
    const READ_BASIC_PROFILE = 'r_basicprofile';

    /**
     * Enables access to email address field
     */
    const READ_EMAIL_ADDRESS = 'r_emailaddress';

    /**
     * Enables  to manage business company, retrieve analytics
     */
    const MANAGE_COMPANY = 'rw_company_admin';

    /**
     * Enables ability to share content on LinkedIn
     */
    const SHARING = 'w_share';
}