<?php

/**
 * @file libraries/Site_links.php
 * @author James Hogan (james_hogan@theyorker.co.uk)
 * @brief Library for generation of common link urls.
 */

class Site_links
{
	/// Main login url to the specified @a $destination.
	/**
	 * @param $desination string The optional destination after login
	 *                           (default is current url)
	 */
	function login($destination = null)
	{
		if (null === $destination) {
			$destination = get_instance()->uri->uri_string();
		}
		return site_url('login/main/'.$destination);
	}
	
	/// Main logout url to the specified @a $destination.
	/**
	 * @param $desination string The optional destination after login
	 *                           (default is current url)
	 */
	function logout($destination = null)
	{
		if (null === $destination) {
			$destination = get_instance()->uri->uri_string();
		}
		return site_url('logout/main/'.$destination);
	}
}

?>
