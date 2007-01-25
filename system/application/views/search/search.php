
<!-- a_search_form defined twice, remember to recreate into class !! :) //-->
<!-- Why is no one using CSS!!!//-->

<div style="float: right; width: 630px; margin-left: 5px; background-color: rgb(255, 255, 255);">
	<div style="border-style: solid none solid solid; border-color: rgb(147, 150, 154) -moz-use-text-color rgb(147, 150, 154) rgb(147, 150, 154); border-width: 1px 0pt 1px 1px; padding: 5px; width: 380px; float: left;">
		<div class="ArticleColumn">
			<?=$search_form?>
<?php if (isset($search_results)) { ?>
		    <ol>
				<?php foreach ($search_results as $result):?>
		        <li>
		            <a href="<?=$result['link']?>"><?=$result['title']?></a>
					<?=$result['blurb']?>
		        </li>
				<?php endforeach;?>
		    </ol>
			<p><?=$search_numbering?></p>
	</div>
</div>
<div style="width: 239px; float: right;">
	<div style="border-left: 1px solid rgb(147, 150, 154); padding-left: 5px; padding-bottom: 10px;">
		<div style="margin: 0pt; padding: 3px 3px 3px 5px; background-color: rgb(148, 151, 155); color: rgb(255, 255, 255); font-size: 12px; font-weight: bold;">
			Refine
		</div>
		<ul>
			<li>All results <i>(<?=$search_all?>)</i></li>
			<li>News <i>(<?=$search_news?>)</i></li>
			<li>Reviews <i>(<?=$search_reviews?>)</i></li>
			<li>Features <i>(<?=$search_features?>)</i></li>
			<li>Events <i>(<?=$search_events?>)</i></li>
			<li>How do I <i>(<?=$search_how?>)</i></li>
			<li>Yorkipedia <i>(<?=$search_york?>)</i></li>
		</ul>
	</div>
	<div style="border-style: solid solid solid none; border-color: rgb(147, 150, 154) rgb(147, 150, 154) rgb(147, 150, 154) -moz-use-text-color; border-width: 1px 1px 1px 0pt; clear: both; padding-left: 10px;">
		<ul>
			<li>Sort by Relevancy</li>
			<li>Sort by Date</li>
		</ul>
	</div>
<?php } ?>
</div>
