<?php if($this->uri->segment(3) == ''): ?>
    <h2>Edit Frequently Asked Questions</h2>
    <ul>
        <li>Something here <a href='/admin/editfaq/1'>Edit</a> <a href="#">Delete</a></li>
        <li>Something here <a href='/admin/editfaq/1'>Edit</a> <a href="#">Delete</a></li>
        <li>Something here <a href='/admin/editfaq/1'>Edit</a> <a href="#">Delete</a></li>
        <li>Something here <a href='/admin/editfaq/1'>Edit</a> <a href="#">Delete</a></li>
        <li>Something here <a href='/admin/editfaq/1'>Edit</a> <a href="#">Delete</a></li>
    </ul>
<?php else: ?>
        <br />
        <form name='a_search_form' id='a_search_form' action='<?php echo site_url('faq/addfaq'); ?>' method='post'>
            <fieldset id='SearchForm' title='Add new FAQ'>
                <legend>Add new FAQ</legend>
                <p><br /><label for='question'>Question:</label> <input type='text' name='question' id='question' value='Something here' /></p>
                <p><label for='answer'>Answer:</label> <textarea name='answer' id='answer' rows='8' cols='50'>
Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam tempus risus in eros. Donec velit turpis, scelerisque eget, euismod vitae, pretium ut, mauris. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nam ipsum felis, tincidunt malesuada, accumsan eu, pellentesque tincidunt, lorem. Proin consectetuer, risus quis ultricies scelerisque, justo mauris pretium augue, et vulputate est felis sed dui. Integer eu felis. Integer congue libero sit amet sapien. Sed id eros. Sed commodo lorem in ipsum. In hac habitasse platea dictumst. Morbi pellentesque ligula nec sem. Donec dictum, sapien ultrices facilisis bibendum, diam neque aliquet nibh, non ornare turpis odio in urna. Phasellus elementum. Vivamus lacus felis, posuere a, luctus eget, tristique vel, turpis. Suspendisse non tortor. Maecenas lectus justo, suscipit eu, sodales vitae, sagittis lobortis, metus. Nam vehicula felis ac metus. Pellentesque sit amet lorem et sem rhoncus congue.
                </textarea></p>
                <p><label for='submit'>&nbsp;</label><input type='submit' name='submit' id='submit' value='Edit' class='submit' /></p>
            </fieldset>
        </form>
<?php endif; ?>