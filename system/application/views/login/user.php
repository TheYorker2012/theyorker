<form id='' action='' method='post' class='form'>
  <fieldset>
    <legend>Edit User Details</legend>
    <label for='e_image'>Image Id:</label>
    <input type='text' name='e_image' id='e_image' value='' size='30' />
    <br />
    <label for='e_firstname'>Firstname:</label>
    <input type='text' name='e_firstname' id='e_firstname' value='' size='30' />
    <br />
    <label for='e_surname'>Surname:</label>
    <input type='text' name='e_surname' id='e_surname' value='' size='30' />
    <br />
    <label for='e_email'>Email:</label>
    <input type='text' name='e_email' id='e_email' value='' size='30' />
    <br />
    <label for='e_nickname'>Nickname:</label>
    <input type='text' name='e_nickname' id='e_nickname' value='' size='30' />
    <br />
    <label for='e_gender'>Gender:</label>
    <select name='e_gender' id='e_gender' size='1'>
      <option value='m' selected='selected'>Male</option>
      <option value='f'>Female</option>
    </select>
    <br />
    <label for='e_enrolled'>Enrolled Year:</label>
    <select name='e_enrolled' id='e_enrolled' size='1'>
      <option value='2006' selected='selected'>2006</option>
      <option value='2005'>2005</option>
      <option value='2004'>2004</option>
      <option value='2003'>2003</option>
      <option value='2002'>2002</option>
      <option value='2001'>2001</option>
    </select>
    <br />
    <label for='e_store_password'>Store Password:</label>
    <input type='text' name='e_store_password' id='e_store_password' value='' size='30' />
    <br />
    <label for='e_permissions'>Permissions:</label>
    <select name='e_permissions' id='e_permissions' size='1'>
      <option value='6' selected='selected'>6</option>
      <option value='5'>5</option>
      <option value='4'>4</option>
      <option value='3'>3</option>
      <option value='2'>2</option>
      <option value='1'>1</option>
    </select>
    <br />
    <label for='e_office_password'>Office Password:</label>
    <input type='text' name='e_office_password' id='e_office_password' value='' size='30' />
    <br />
  </fieldset>
  <fieldset>
    <label for='e_submit'></label>
    <input type='submit' name='e_submit' id='e_submit' value='Edit' class='button' />
    <br />
  </fieldset>
</form>