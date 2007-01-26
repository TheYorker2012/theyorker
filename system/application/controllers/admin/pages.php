<?php

/// Pages admin controller.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * This will allow management of the following database tables:
 *	- pages
 *	- page_properties
 *	- property_types
 *
 * And will have special editors for special property types:
 *	- plain text
 *	- wikitext (option to update or clear the cache)
 *	- images
 */
class Pages extends Controller
{
	function __construct()
	{
	
	}
	
	function index()
	{
		?>
<BODY>
<BODY>
This will allow management of the following database tables:<br />
<ul>
	<li>pages</li>
	<li>page_properties</li>
	<li>property_types</li>
</ul>

And will have special editors for special property types:<br />
<ul>
	<li>plain text</li>
	<li>wikitext (option to update or clear the cache)</li>
	<li>images</li>
</ul>
</BODY>
</HTML>
		<?php
	}
}

?>