<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.8
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2016 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * The Welcome Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Welcome extends Controller
{
	/**
	 * The basic welcome message
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		echo "hello";
		// return Response::forge(View::forge('welcome/index'));
	}

	/**
	 * A typical "Hello, Bob!" type example.  This uses a Presenter to
	 * show how to use them.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_hello()
	{
		echo "Hello";
		// return Response::forge(Presenter::forge('welcome/hello'));
	}

	public function action_view()
	{
		$data = array();
		$data['title'] = 'TITLE';
		$data['username'] = 'NT';
		$data['posted_at'] = '20:04';
		$data['content'] = 'CONTENT';

		$view = array();
		$view['header'] = View::forge('header', $data);

		return View::forge('layout', $view);
	}

	public function action_read()
	{

		$id_array = array("user1", "user2");
		$results = DB::select()->from('table1')->where('username', 'in', $id_array)->execute()->get('username');


		echo '<pre>';
		print_r($results);


		// $data = array();
		// $data['title'] = 'TITLE';
		// $data['username'] = 'NT';
		// $data['posted_at'] = '20:04';
		// $data['content'] = 'CONTENT';

		// $view = array();
		// $view['header'] = View::forge('header', $data);

		// return View::forge('layout', $view);
	}

	public function action_insert()
	{
		
		DB::insert('table1')->set(array(
			'username' => 'user2',
			'password' => '54321',
			'chat_content' => 'Good morning',
			'posted_at' => '22:04'
		))->execute();
	}

	/**
	 * The 404 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_404()
	{
		return Response::forge(Presenter::forge('welcome/404'), 404);
	}
}
