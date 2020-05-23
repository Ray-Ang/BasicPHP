<?php

/**
 * The AppController is a Class Controller reserved for endpoint
 * routes, i.e. REST endpoints, using the route_class() function.
 */

class AppController
{

	public function listUsers()
	{

		if (! isset($_GET['order'])) $_GET['order'] = 0;
		if (! is_numeric($_GET['order'])) {
			$error_message = 'Post order value should be numeric.';
			$page_title = 'Error in order parameter';

			$data = compact('error_message', 'page_title');
			view('error', $data);
		}
		if (isset($_GET['order']) && $_GET['order'] < 0) $_GET['order'] = 0;

		$per_page = 3;
		$order = intval($_GET['order']);

		$post = new PostModel;
		$stmt = $post->list( $per_page, $order );
		$total = $post->total();

		if (isset($_GET['order']) && $_GET['order'] > $total) $_GET['order'] = $total;

		$page_title = 'List of Posts';

		$data = compact('stmt', 'total', 'per_page', 'page_title');
		view('post_list', $data);

	}

	public function viewUser()
	{

		if (isset($_POST['delete-post']) && isset($_POST['csrf-token']) && isset($_SESSION['csrf-token']) && $_POST['csrf-token'] == $_SESSION['csrf-token']) {
			$this->deleteUser();
			header('Location: ' . BASE_URL . 'posts');
			exit();
		}

		if (isset($_POST['goto-edit'])) {

			header('Location: ' . BASE_URL . 'posts/' . segment(2) . '/edit');
			exit();

		}

		$post = new PostModel;
		$row = $post->view( segment(2) );

		if ($row) {

			$page_title = 'View Post';

			$data = compact('row', 'page_title');
			view('post_view', $data);

		} else {

			$error_message = 'The Post ID does not exist.';
			$page_title = 'Error in Post ID';

			$data = compact('error_message', 'page_title');
			view('error', $data);

		}

	}

	public function editUser()
	{

		$post = new PostModel;

		if (isset($_POST['edit-post']) && isset($_POST['csrf-token']) && isset($_SESSION['csrf-token']) && $_POST['csrf-token'] == $_SESSION['csrf-token']) {

			$post->edit( segment(2) );

			header('Location: ' . BASE_URL . 'posts/' . segment(2));
			exit();

		}

		$row = $post->view( segment(2) );

		if ($row) {

			$page_title = 'Edit Post';

			$data = compact('row', 'page_title');
			view('post_edit', $data);

		} else {

			$error_message = "The Post ID does not exist.";
			$page_title = 'Error in Post ID';

			$data = compact('error_message', 'page_title');
			view('error', $data);

		}

	}

	public function deleteUser()
	{

		$post = new PostModel;
		$post->delete( segment(2) );

	}

}