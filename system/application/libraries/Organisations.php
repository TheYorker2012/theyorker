<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file organisations.php
 * @brief Library for getting organisation information.
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
 
class Organisations
{
	/// Code igniter instance.
	private $CI;
	
	/// Default constructor
	function __construct()
	{
	}
	
	/// Get organisation types from organisations.
	/**
	 * @param $Organisations array Organisations as returned by _GetOrgs.
	 * @param $Sorted array Whether to sort the result by name.
	 * @return array of organisation types.
	 */
	function _GetOrganisationTypes($Organisations, $Sorted = FALSE)
	{
		$types = array();
		foreach ($Organisations as $organisation) {
			if (array_key_exists($organisation['type'], $types)) {
				++$types[$organisation['type']];
			} else {
				$types[$organisation['type']] = 1;
			}
		}
		if ($Sorted) {
			asort($types);
			$types = array_reverse($types,TRUE);
		}
		$result = array();
		foreach ($types as $type => $quantity) {
			$result[] = array(
				'id' => $type,
				'name' => $type,
				'quantity' => $quantity,
			);
		}
		return $result;
	}

	/// Temporary function get organisations.
	/**
	 * @param $Pattern string/bool Search pattern or FALSE if all.
	 * @return array of organisations matching pattern.
	 */
	function _GetOrgs($Pattern, $urlpath='directory/')
	{
		$org_description_words = $this->CI->pages_model->GetPropertyInteger('org_description_words', FALSE, 5);
		
		$orgs = $this->CI->directory_model->GetDirectoryOrganisations();
		$organisations = array();
		foreach ($orgs as $org) {
			$organisations[] = array(
				'name' => $org['organisation_name'],
				'shortname' => $org['organisation_directory_entry_name'],
				'link' => $urlpath.$org['organisation_directory_entry_name'],
				'description' => $org['organisation_description'],
				'shortdescription' => word_limiter(
					$org['organisation_description'], $org_description_words),
				'type' => $org['organisation_type_name'],
			);
		}
		if ($Pattern !== FALSE) {
			$organisations = array(
				array(
					'shortname'   => 'pole_dancing',
					'name'        => 'Pole Dancing',
					'description' => 'A fitness club',
					'type'        => 'Athletics Union',
				),
				array(
					'shortname'   => 'costcutter',
					'name'        => 'Costcutter',
					'description' => 'Campus shop',
					'type'        => 'College & Campus',
				),
			);
		}
		return $organisations;
	}

	/// Temporary function get organisation data.
	/**
	 * @param $OrganisationShortName Short name of organisation.
	 * @return Organisation data relating to specified organisation or FALSE.
	 */
	function _GetOrgData($OrganisationShortName)
	{
		$data = array();

		$orgs = $this->CI->directory_model->GetDirectoryOrganisationByEntryName($OrganisationShortName);
		if (1 === count($orgs)) {
			foreach ($orgs as $org) {
				$data['organisation'] = array(
					'id'          => $org['organisation_entity_id'],
					'name'        => $org['organisation_name'],
					'shortname'   => $org['organisation_directory_entry_name'],
					'description' => $org['organisation_description'],
					'type'        => $org['organisation_type_name'],
					'website'     => $org['organisation_url'],
					'location'    => $org['organisation_location'],
					'open_times'  => $org['organisation_opening_hours'],
					'email_address'   => $org['organisation_email_address'],
					'postal_address'  => $org['organisation_postal_address'],
					'postcode'    => $org['organisation_postcode'],
					'phone_internal'  => $org['organisation_phone_internal'],
					'phone_external'  => $org['organisation_phone_external'],
					'fax_number'  => $org['organisation_fax_number'],


					'blurb'       => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nulla lorem magna, tincidunt sed, feugiat nec, consectetuer vitae, nisl. Vestibulum gravida ipsum non justo. Vivamus sem. Quisque ut sem vitae elit luctus lobortis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
				);
				if (NULL === $org['organisation_yorkipedia_entry']) {
					$data['organisation']['yorkipedia'] = NULL;
				} else {
					$data['organisation']['yorkipedia'] = array(
							'url'   => WikiLink('yorkipedia', $org['organisation_yorkipedia_entry']),
							'title' => $org['organisation_yorkipedia_entry'],
						);
				}
			}
		} else {
			$data['organisation'] = array(
				'shortname'   => $OrganisationShortName,
				'name'        => 'FragSoc',
				'description' => 'The people who run this website',
				'type'        => 'Organisation',
				'blurb'       => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nulla lorem magna, tincidunt sed, feugiat nec, consectetuer vitae, nisl. Vestibulum gravida ipsum non justo. Vivamus sem. Quisque ut sem vitae elit luctus lobortis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
				'website'     => 'http://www.fragsoc.com',
				'location'    => 'Goodricke College',
				'open_times'  => 'Every Other Weekend',
			);
		}
		return $data;
	}
}

?>