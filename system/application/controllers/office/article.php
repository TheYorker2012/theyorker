<?php

/**
 *	Yorker Office - Article Manager
 *	@author Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Article extends Controller
{

	function __construct()
	{
		parent::Controller();

		//$this->load->model('news_model');
		//$this->load->model('requests_model');
		$this->load->model('article_model');
		$this->load->model('photos_model');
		$this->load->model('tags_model');
	}

	function _remap ($method = NULL)
	{
		if (empty($method)) {
			redirect('/office/articles');
		} elseif (method_exists($this, $method)) {
			$this->$method();
		} else {
			$this->index($method);
		}
	}

	function index ($article_id = NULL)
	{
		if (!CheckPermissions('office')) return;
		if (!CheckRolePermissions('ARTICLE_VIEW')) return;
		$data = array();

		// Have we got an article to load?
		if (empty($article_id)) show_404();
		$data['article'] = $this->article_model->getById($article_id);

		// Does the article exist?
		if (empty($data['article'])) show_404();

		// Quick way of adding photos to an article straight from the gallery
		if (isset($_SESSION['img']) && (count($_SESSION['img']) > 0)) {
			foreach ($_SESSION['img'] as $photo) {
				$photo_req_id = $this->photos_model->AddNewPhotoRequest($this->user_auth->entityId, $article_id, '', '');
				$this->photos_model->SuggestPhoto($photo_req_id, $photo['list'], 'Added to article straight from gallery.', $this->user_auth->entityId);
				$this->photos_model->FlagRequestReady($photo_req_id);
				$this->photos_model->SelectPhoto($photo_req_id, $photo['list'], $this->user_auth->entityId);
			}
			unset($_SESSION['img']);
		}

		// Setup AJAX functionality
		$this->load->library('xajax');
		$this->xajax->registerFunction(array('_ajax', &$this, '_ajax'));
		$this->xajax->registerFunction(array('_reporterChange', &$this, '_reporterChange'));
		$this->xajax->registerFunction(array('_getMediaFiles', &$this, '_getMediaFiles'));
		$this->xajax->processRequests();

		// Get more article data
		$data['photos'] = $this->photos_model->GetPhotoRequestsForArticle($article_id);
		$data['tags'] = $this->tags_model->getArticleTags($article_id);
		$data['reporters'] = $this->article_model->getReportersForArticle($article_id);

		// Get possible field values
		$data['types'] = $this->article_model->getAllContentTypes();
		$data['photo_types'] = $this->photos_model->GetThumbnailTypes();
		$data['tag_groups'] = $this->tags_model->getPossibleArticleTags($data['article']['type_id']);
		$data['editors'] = $this->permissions_model->getAllUsersWithPermission('ARTICLE_PUBLISH');
		$data['all_reporters'] = $this->permissions_model->getAllUsersWithPermission('ARTICLE_MODIFY');

		// Convert some data to JSON
		$data['articleJS'] = json_encode(array(
			'article'		=>	$data['article'],
			'photos'		=>	$data['photos'],
			'photo_types'	=>	$data['photo_types'],
			'tags'			=>	$data['tags'],
			'tag_groups'	=>	$data['tag_groups'],
			'reporters'		=>	$data['reporters']
		));

		// Which tab should we display first?
		if (!empty($_SESSION['oi_defaultPage']) && !empty($_SESSION['oi_defaultPage'][$article_id])) {
			$data['start_page'] = $_SESSION['oi_defaultPage'][$article_id];
			unset($_SESSION['oi_defaultPage']);
		} else {
			$data['start_page'] = 'brief';
		}

		// Setup page
		$this->pages_model->SetPageCode('office_news_article');
		$this->main_frame->SetTitleParameters(
			array('title' => (empty($data['article']['content_heading']) ? $data['article']['request_title'] : $data['article']['content_heading']))
		);
		$this->main_frame->SetContentSimple('office/article/main', $data);
		$this->main_frame->SetData('menu_tab', 'articles');
		$this->main_frame->IncludeJs('javascript/wikitoolbar.js');
		$this->main_frame->IncludeJs('/javascript/office_interface.js');
		$this->main_frame->IncludeCss('/stylesheets/office_interface.css');
		$this->main_frame->IncludeCss('/stylesheets/calendar_select.css');
		$this->main_frame->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));
		$this->main_frame->Load();
	}

	function photo ()
	{
		if (!CheckPermissions('office')) return;
		if (!CheckRolePermissions('ARTICLE_MODIFY')) return;

		$article_id = $this->uri->segment(4);
		$upload = $this->uri->segment(5);

		if (empty($article_id)) show_404();
		if (empty($_SESSION['oi_defaultPage'])) $_SESSION['oi_defaultPage'] = array();
		$_SESSION['oi_defaultPage'][$article_id] = 'photos';
		if (empty($upload)) {
			redirect('/office/gallery');
		} else {
			redirect('/office/gallery/upload');
		}
	}

	function publish ()
	{
		if (!CheckPermissions('office')) return;
		if (!CheckRolePermissions('ARTICLE_PUBLISH')) return;

		$article_id = $this->uri->segment(4);
		$publish_date = $this->uri->segment(5);

		if (empty($article_id)) show_404();
		$article = $this->article_model->getById($article_id);
		if (empty($article)) show_404();

		$errors = array();
		if (empty($article['content_heading'])) $errors[] = 'Headline not specified.';
		if (empty($article['content_subtext'])) $errors[] = 'Blurb not specified.';
		if ($article['thumbnail_photo_id'] === null) $errors[] = 'Photo to use for article thumbnails not selected.';
		if ($publish_date < mktime(0, 0, 0, 1, 1, 2000)) $errors[] = 'Please set the publish date for this article.';

		$reporters = $this->article_model->getReportersForArticle($article_id);
		if (empty($reporters)) $errors[] = 'At least one reporter must be assigned and have accepted to write this article. All reporters must have a business card also.';

		if (empty($errors)) {
			$publish_date = date('Y-m-d H:i:s', $publish_date);
			$this->article_model->publish($article_id, $article['content_id'], $publish_date);
//			$this->load->library('facebook_ticker');
//			if ($this->facebook_ticker->TickerUpdate()) {
//				$this->main_frame->AddMessage('success','The Yorker Facebook News Ticker Application was successfully updated.');
//			} else {
//				$this->main_frame->AddMessage('error','There was a problem updating The Yorker Facebook News Ticker Application.');
//			}
			$this->main_frame->AddMessage('success','The article was successfully published.');
		} else {
			$this->main_frame->AddMessage('error', 'Unable to publish the article for the following reasons: <ul><li>' . implode('</li><li>', $errors) . '</li></ul>');
		}

		if (empty($_SESSION['oi_defaultPage'])) $_SESSION['oi_defaultPage'] = array();
		$_SESSION['oi_defaultPage'][$article_id] = 'publish';
		redirect('/office/article/' . $article_id);
	}

	function _ajax ($article = NULL, $photos = NULL, $tags = NULL, $reporters = NULL)
	{
		$article_id = $this->uri->segment(3);
		$data = array();
		$xajax_response = new xajaxResponse();

		if (!$this->permissions_model->hasUserPermission('ARTICLE_MODIFY')) {
			$xajax_response->addScriptCall('errorPermission', 'ARTICLE_MODIFY');
			return $xajax_response;
		}

		if (!empty($reporters)) {
			foreach ($reporters as $reporter) {
				$this->article_model->changeByline($article_id, $reporter['user_id'], $reporter['byline_id']);
			}
		}

		if (!empty($tags)) {
			$this->tags_model->resetArticleTags($article_id);
			$tag_ids = array_keys($tags);
			$this->tags_model->addArticleTags($article_id, $tag_ids);
		}
		$data['tags'] = $this->tags_model->getArticleTags($article_id);

		if (!empty($photos)) {
			foreach ($photos as $photo) {
				$this->photos_model->ChangeDetails($photo['request_id'],$photo['photo_caption'],$photo['photo_alt'],$photo['photo_type']);
			}
		}
		$data['photos'] = $this->photos_model->GetPhotoRequestsForArticle($article_id);

		// Process main article changes
		if (!empty($article)) {
			$this->load->library('wikiparser');
			foreach ($data['photos'] as $photo) {
				$this->wikiparser->add_image_override($photo['photo_number'], $this->image->getThumb($photo['photo_id'], $photo['photo_codename'], true), $photo['photo_caption']);
			}
			$cache = $this->wikiparser->parse($article['content_wikitext']);
			if (empty($article['date_deadline'])) {
				$deadline = mktime();
			} else {
				$deadline = $article['date_deadline'];
			}
			$deadline = date('Y-m-d H:i:s', $deadline);
			$this->article_model->update($article['id'], $article['type_id'], $article['request_title'], $article['request_description'], $article['thumbnail_photo_id'], $article['main_photo_id'], $deadline, $article['editor_user_id']);
			$revision = $this->article_model->getLastRevisionMeta($article['id']);
			if (empty($revision)) {
				// Create a new revision
				$this->article_model->newRevision($article['id'], $this->user_auth->entityId, $article['content_heading'], $article['content_subtext'], $article['content_wikitext'], $cache, $article['content_blurb']);
			} elseif ($revision->last_update > mktime() - (60 * 10)) {
				if ($revision->user_id != $this->user_auth->entityId) {
					// Someone else is editing this article!!!
					$xajax_response->addScriptCall('errorInUse', $revision->user_name);
					return $xajax_response;
				} else {
					// Last update recent so use same revision
					$this->article_model->updateRevision($revision->id, $article['content_heading'], '', $article['content_subtext'], $article['content_blurb'], $article['content_wikitext'], $cache);
				}
			} else {
				// Last update was a while ago, create a new revision
				$this->article_model->newRevision($article['id'], $this->user_auth->entityId, $article['content_heading'], $article['content_subtext'], $article['content_wikitext'], $cache, $article['content_blurb']);
			}
		}
		$data['article'] = $this->article_model->getById($article_id);

		$xajax_response->addScriptCall('savedChanges');
		return $xajax_response;
	}

	function _reporterChange ($user_id = NULL, $op = 'add')
	{
		$article_id = $this->uri->segment(3);
		$data = array();
		$xajax_response = new xajaxResponse();

		if (!$this->permissions_model->hasUserPermission('ARTICLE_MODIFY')) {
			$xajax_response->addScriptCall('errorPermission', 'ARTICLE_MODIFY');
			return $xajax_response;
		}

		if (!empty($article_id) && !empty($user_id)) {
			if ($op == 'add') {
				$this->article_model->addReporter($article_id, $user_id, $this->user_auth->entityId);
			} elseif ($op == 'remove') {
				$this->article_model->removeReporter($article_id, $user_id);
			}
		}

		$reporters = $this->article_model->getReportersForArticle($article_id);
		$xajax_response->addScriptCall('callbackReporters', $reporters);
		return $xajax_response;
	}

	function _getMediaFiles()
	{
		$xajax_response = new xajaxResponse();
		$this->load->model('static_model');
		$exts = array('flv', 'mp3');
		$options = array();
		foreach ($this->static_model->GetDirectoryListing($this->config->item('static_local_path') . '/media', '', $exts) as $option) {
			$options[] = array(
				$option,
				$this->config->item('static_web_address') . '/media' . $option
			);
		}
		$xajax_response->addScriptCall('insertMediaPlayerOptions', $options);
		return $xajax_response;
	}

	function create ()
	{
		if (!CheckPermissions('office')) return;
		if (!CheckRolePermissions('ARTICLE_ADD')) return;

		$deadline = date('Y-m-d H:i:s', mktime() + (60*60*24));
		$article_id = $this->article_model->create($this->user_auth->entityId, $deadline);

		redirect('/office/article/' . $article_id);
	}










	function create2()
	{
		$data['boxes'] = $this->requests_model->getBoxes();
		$data['user_level'] = 'editor';
		$data['status'] = 'article';

		$article_id = $this->requests_model->CreateRequest('request',$this->input->post('r_box'),$this->input->post('r_title'),$this->input->post('r_brief'),$this->user_auth->entityId,$deadline);
		$byline = $this->article_model->GetReporterByline($this->user_auth->entityId);
		$this->requests_model->AddUserToRequest($article_id, $this->user_auth->entityId, $this->user_auth->entityId, ((isset($byline['id'])) ? $byline['id'] : NULL));
		$this->requests_model->AcceptRequest($article_id, $this->user_auth->entityId);
		$accept_data = array(
			'editor' 		=>	$this->user_auth->entityId,
			'publish_date' 	=>	$deadline,
			'title'			=>	$this->input->post('r_title'),
			'description'	=>	$this->input->post('r_brief'),
			'content_type'	=>	$this->input->post('r_box')
		);
		$this->requests_model->UpdateRequestStatus($article_id,'request',$accept_data);
		$revision = $this->article_model->CreateNewRevision($article_id, $this->user_auth->entityId, '', '', '', '', '', '');
	}


	function _showarticle($article_id = 0)
	{
        $this->xajax->registerFunction(array('_deleteArticle', &$this, '_deleteArticle'));

		/// @todo jh559,cdt502 ajaxify comments
		$this->load->library('comment_views');
		$thread = $this->news_model->GetPrivateThread($article_id);
		$this->comment_views->SetUri('/office/news/'.$article_id.'/');
		/// @todo jh559,cdt502 comment pages (page hardwired to 1 atm)
		$data['comments'] = $this->comment_views->CreateStandard($thread, /* included comment */ 0);

		$data['revisions'] = $this->requests_model->GetArticleRevisions($article_id);
		$revision = $this->article_model->GetLatestRevision($article_id);
		if (!$revision) {
			// There is no revision for this article yet... so create one
			$revision = $this->article_model->CreateNewRevision($article_id, $this->user_auth->entityId, '', '', '', '', '', '');
		}
		// Get latest revision's data
		$data['revision'] = $this->article_model->GetRevisionData($revision);

	}

	function preview()
	{
		if (!CheckPermissions('office')) return;

		$_SESSION['office_news_preview'] = $this->uri->segment(6);
		redirect('/news/' . $this->uri->segment(5) . '/' . $this->uri->segment(4));
	}



	function _deleteArticle()
	{
		$xajax_response = new xajaxResponse();
		$article_id = $this->uri->segment(3);
		$data['article'] = $this->article_model->GetArticleDetails($article_id);

		// Make it so we only have to worry about two levels of access as admins can do everything editors can
		$data['user_level'] = GetUserLevel();
		if ($data['user_level'] == 'admin') {
			$data['user_level'] = 'editor';
		}
//		if (($data['user_level'] == 'editor') || ($this->requests_model->IsUserRequestedForArticle($article_id, $this->user_auth->entityId) == 'accepted')) {
		if ($data['user_level'] == 'editor') {
			$this->requests_model->DeleteArticle($article_id);
			$this->main_frame->AddMessage('success','The article was successfully deleted.');
			$xajax_response->addRedirect('/office/news');
		} else {
			$xajax_response->addAlert('You must be an editor to delete an article!');
		}
		return $xajax_response;
	}

	function _addComment($comment_text)
	{
		$xajax_response = new xajaxResponse();
		if ($comment_text == '') {
			$xajax_response->addAlert('Please enter a comment to submit.');
			$xajax_response->addScriptCall('commentAdded','','','');
		} else {
			$new_comment = $this->article_model->InsertArticleComment($this->uri->segment(3), $this->user_auth->entityId, $comment_text);
			$xajax_response->addScriptCall('commentAdded',date('D jS F Y @ H:i',$new_comment['time']),$new_comment['name'],nl2br($comment_text));
		}
		return $xajax_response;
	}

}

?>
