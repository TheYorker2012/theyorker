function sidebar()
{

	document.write(

		'<div><a href="http://www.rapyd.com" title="rapyd.com">www.rapyd.com</a></div>' +
		'<div class="line"></div>' +
		'<div><a href="index.html" title="Index">Index</a></div>' +	
		'<div><a href="changelog.html" title="Changelog">Changelog</a></div>' +	
		'<div><a href="install.html" title="Installation">Installation</a></div>' +	
		'<div><a href="concepts.html" title="General Concepts">General Concepts</a></div>' +	
		'<div><a href="components.html" title="Inheritance and dependency">Inheritance and depen.</a></div>' +	    
		'<div class="line"></div>' +
		'<div><a href="themes.html" title="Theme">Themes</a></div>' +	   
		'<div class="line"></div>' +
		'<h3>data presentation</h3>' +	
		'<div><a href="dataset.html" title="DataSet">DataSet</a></div>' +	
		'<div><a href="datatable.html" title="DataTable">DataTable</a></div>' +	
		'<div><a href="datagrid.html" title="DataGrid">DataGrid</a></div>' +	
	
		'<div class="line"></div>' +
		
		'<h3>data editing</h3>' +	
		'<div><a href="fields.html" title="Fields">Fields</a></div>' +	
		'<div><a href="dataobject.html" title="DataObject">DataObject</a></div>' +	
		'<div><a href="dataform.html" title="DataForm">DataForm</a></div> ' +	
		'<div><a href="dataedit.html" title="DataEdit">DataEdit</a></div>' +	
		'<div><a href="datafilter.html" title="DataFilter">DataFilter</a></div><br/>' +	
		'<div><a href="crud.html" title="DataFilter/DataGrid/DataEdit">CRUD</a></div>' +	
		'<div class="line"></div>');

}

function header()
{

	document.write(
		'<h1>Rapyd Components - User Guide</h1>' +
		'<div>version 0.9 | based on CI 1.5.0 | posted 12/11/2006</div>' +
		'<div class="line"></div>');

}

function footer()
{

	document.write(
		'<div class="footer">' +
		' <p><a href="http://www.codeigniter.com">Code Igniter Home</a> | <a href="http://www.rapyd.com">Rapyd Home</a></p>' +
		'</div>');

}