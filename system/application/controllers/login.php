<?php

/// Main login controller.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Used for logging in and related functions.
 */
class Login extends Controller
{
	/// Default constructor
	function __construct()
	{
		parent::Controller();

		$this->load->helper('uri_tail');
	}

	/// Main login screen.
	/**
	 * Any additional uri segments are used as the redirect address after
	 *	successful login.
	 */
	function main()
	{
		if (!CheckPermissions('public', FALSE, TRUE)) return;

		LoginHandler('student', GetUriTail(2));
	}

	function newpass($user = null, $key = null) {
		$password = $this->input->post('newpassword');
		$password2 = $this->input->post('confirmnewpassword');

		if (!CheckPermissions('public')) return;

		$this->pages_model->SetPageCode('account_password_new');

		try {
			$this->user_auth->login($user, $key, false, true);
		} catch (Exception $e) {
			get_instance()->messages->AddMessage('error','<p>'.$e->getMessage().'</p>');
			redirect('/account/password/register');
		}

		if (is_string($password)) {
			if ($password == $password2) {
				$this->user_auth->setPassword($password);
				redirect('/register');
			} else {
				get_instance()->messages->AddMessage('error','<p>Passwords do not match.</p>');
			}
		}

		$this->main_frame->SetContentSimple('account/newpass', array());
		$this->main_frame->Load();
	}

	/// VIP login screen.
	/**
	 * Any additional uri segments are used as the redirect address after
	 *	successful login.
	 */
	function vip()
	{
		if (!CheckPermissions('student', FALSE, TRUE)) return;

		LoginHandler('vip', GetUriTail(2));
	}

	/// VIP login screen for specific organisation.
	/**
	 * @param $Organisation string Organisation code.
	 *
	 * Any additional uri segments are used as the redirect address after
	 *	successful login.
	 */
	function vipswitch($Organisation = NULL)
	{
		if (!CheckPermissions('student', FALSE, TRUE)) return;

		LoginHandler('vip', GetUriTail(3), $Organisation);
	}

	/// Office login screen.
	/**
	 * Any additional uri segments are used as the redirect address after
	 *	successful login.
	 */
	function office()
	{
		if (!CheckPermissions('student', FALSE, TRUE)) return;

		LoginHandler('office', GetUriTail(2));
	}

	/// Facebook login.
	/**
	 * Any additional uri segments are used as the redirect address after
	 *	successful login.
	 */
	function facebook()
	{
		session_start();
		$this->load->library('facebook');
		$this->facebook->Enable();

		RedirectUriTail(2);
	}
}
?>
