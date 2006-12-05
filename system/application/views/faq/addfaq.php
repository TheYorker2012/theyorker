        <br />
        <form name='a_search_form' id='a_search_form' action='<?php echo site_url('faq/addfaq'); ?>' method='post'>
            <fieldset id='SearchForm' title='Add new FAQ'>
                <legend>Add new FAQ</legend>
                <p><br /><label for='question'>Question:</label> <input type='text' name='question' id='question' value='' /></p>
                <p><label for='answer'>Answer:</label> <textarea name='answer' id='answer' rows='8' cols='50'></textarea></p>
                <p><label for='submit'>&nbsp;</label><input type='submit' name='submit' id='submit' value='Add' class='submit' /></p>
            </fieldset>
        </form>