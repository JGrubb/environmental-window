<?php

add_action('admin_menu', 'randomtext_menu');

function randomtext_menu() {
  add_options_page('Random Text', 'Random Text', 'update_plugins', 'randomtext', 'randomtext_options');
}

function randomtext_options() {
	$action = isset($_GET['action']) ? $_GET['action'] : false;
	switch($action){
		case 'new' :
			randomtext_edit();
			break;
		case 'edit' :
			$id = intval($_GET['id']);
			randomtext_edit($id);
			break;
		case 'delete' :
			$id = intval($_GET['id']);
			check_admin_referer('randomtext_delete'.$id);
			randomtext_delete($id);
			// now display summary page
			randomtext_list();
			break;
		default:
			randomtext_list();
	}
}

function randomtext_pagetitle($suffix='') {
 return '
 <div id="icon-options-general" class="icon32"><br/></div><h2>Random Text '.$suffix.'</h2>
 ';
}

function randomtext_error($text='An undefined error has occured.') {
	echo '<div class="wrap">'.randomtext_pagetitle(' - ERROR!').'<h3>'.$text.'</h3></div>';
}
 
function randomtext_list() {
	global $wpdb, $user_ID;
	$table_name = $wpdb->prefix . 'randomtext';
	$pageURL = 'options-general.php?page=randomtext';
	$perpage = 20;
	$cat = isset($_GET['cat']) ? $_GET['cat'] : false;
	$author_id = isset($_GET['author_id']) ? intval($_GET['author_id']) : 0;
	$where = '';
	$paged = isset($_GET['paged']) ? intval($_GET['paged']) : 0;
	$paged = $paged ? $paged : 1;
	$startrow = ($paged-1)*$perpage;
	
	if($cat) {
		$where = " WHERE category = '$cat'";
		$page_params = '&cat='.urlencode($cat);
	}
	if($author_id) {
		$where = " WHERE user_id = $author_id";
		$page_params .= '&author_id='.$author_id;
	}
	$item_count = $wpdb->get_row("Select count(*) items FROM $table_name $where");
	if(isset($item_count->items)) {
		$totalrows = 	$item_count->items;
	} else {
		echo '<h3>Achtung! The expected database table "<i>'.$table_name.'</i>" does not appear to exist.</h3>';
	}
	$rows = $wpdb->get_results("SELECT * FROM $table_name $where ORDER BY randomtext_id LIMIT $startrow, $perpage");
		
	$author = array();
	
	$num_pages = 1+floor($totalrows/$perpage);
	
	$paging = paginate_links( array(
		'base' => add_query_arg( 'paged', '%#%' ),
		'format' => '',
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' => $num_pages,
		'current' => $paged
		));

	?>
<div class="wrap">
	<?php echo randomtext_pagetitle(); ?>
	<div class="tablenav">
		<div class="alignleft actions">
			<input type="submit" class="button-secondary action" id="randomtext_add" name="randomtext_add" value="Add New" onclick="location.href='options-general.php?page=randomtext&action=new'"/>
			Category: <select id="randomtext_category" name="randomtext_category" onchange="javascript:window.location='<?php echo $pageURL.'&cat='; ?>'+(this.options[this.selectedIndex].value);">
			<option value="">View all categories </option>
			<?php echo randomtext_get_category_options($cat); ?>
			</select>
		</div>
		<div class="tablenav-pages">
			<span class="displaying-num">Displaying <?php echo $item_range.' of '.$totalrows; ?></span>
			<?php echo $paging; ?>
		</div>
	</div>

	<table class="widefat">
	<thead><tr>
		<th>ID</th>
		<th>Text</th>
		<th width="10%">Category</th>
		<th width="10%">Author</th>
		<th width="10%">Action</th>
	</tr></thead>
	<tbody>
<?php		
	$alt = '';
	foreach($rows as $row) {
		$alt = ($alt) ? '' : ' class="alternate"'; // stripey :)
		if(!isset($author[$row->user_id])){
			$user_info = get_userdata($row->user_id);
			$author[$row->user_id] = $user_info->display_name;
		}
		$status = ($row->visible=='yes') ? 'visible' : 'hidden';
		$bytes = strlen($row->text);
		if(strlen($row->text) > 200)
			$row->text = trim(mb_substr($row->text,0,350,'UTF-8')).'...';
		echo '<tr'.$alt.'>
		<td>'.$row->randomtext_id.'</td>
		<td>'.esc_html($row->text).'</td>
		<td><a href="'.$pageURL.'&cat='.$row->category.'">'.$row->category.'</a><br />'.$status.'</td>
		<td class="author column-author"><a href="'.$pageURL.'&author_id='.$row->user_id.'">'.$author[$row->user_id].'</a><br />'.$bytes.' bytes</td>
		<td><a href="'.$pageURL.'&action=edit&id='.$row->randomtext_id.'">Edit</a><br />';
		$del_link = wp_nonce_url($pageURL.'&action=delete&id='.$row->randomtext_id, 'randomtext_delete' . $row->randomtext_id);
		echo '<a onclick="if ( confirm(\'You are about to delete post #'.$row->randomtext_id.'\n Cancel to stop, OK to delete.\') ) { return true;}return false;" href="'.$del_link.'" title="Delete this post" class="submitdelete">Delete</a>';
		echo '</td></tr>';		
	}
	echo '</tbody></table>';

  echo '</div>';
}

function randomtext_edit($randomtext_id=0) {

	if($_POST) {
		// process the posted data and display summary page - not pretty :(
		randomtext_save($_POST);
		randomtext_list();
		die();
	}

	echo '<div class="wrap">';
	$title = '- Add New';
	if($randomtext_id) {
		$title = '- Edit';
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'randomtext';
		$sql = "SELECT * from $table_name where randomtext_id=$randomtext_id";
		$row = $wpdb->get_row($sql);
		if(!$row)
			$error_text = '<h2>The requested entry was not found.</h2>';
	} else {
		$row = new stdClass();
		$row->text = '';
		$row->visible = 'yes';
	}
	echo randomtext_pagetitle($title); 
	
	if($randomtext_id && !$row) {
		echo '<h3>The requested entry was not found.</h3>';
	} else {
	// display the add/edit form 
	echo '<form method="post" action="'.$_SERVER["REQUEST_URI"].'">
		'.wp_nonce_field('randomtext_edit' . $randomtext_id).'
		<input type="hidden" id="randomtext_id" name="randomtext_id" value="'.$randomtext_id.'">
		<h3>Text To Display</h3>
		<textarea name="randomtext_text" style="width: 80%; height: 100px;">'.apply_filters('format_to_edit',$row->text).'</textarea>
		<h3>Category</h3>
		<p>Select a category from the list or enter a new one.</p>
		<label for="randomtext_category">Category: </label><select id="randomtext_category" name="randomtext_category">'; 
	echo randomtext_get_category_options($row->category);
	echo '</select></p>
		<p><label for="randomtext_category_new">New Category: </label><input type="text" id="randomtext_category_new" name="randomtext_category_new"></p>';
		
		echo '<h3>Is visible.</h3>
			<p><label for="randomtext_visible_yes"><input type="radio" id="randomtext_visible_yes" name="randomtext_visible" value="yes" '.checked($row->visible,'yes',false).' /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
			<label for="randomtext_visible_no"><input type="radio" id="randomtext_visible_no" name="randomtext_visible" value="no" '.checked($row->visible,'no',false).' /> No</label></p>';
		if(!$randomtext_id) {
			// don't offer Bulk Insert on edit
			echo '<h3>Use Bulk Insert</h3>
			<p><input type="radio" name="randomtext_bulkinsert" value="yes" /> Yes&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="randomtext_bulkinsert" value="no" checked="checked" /> No</p>
			<small>Bulk insert will create a new record for each line (delimited by carriage return) within the text box above using the same category selected.<br />Empty lines will be ignored.</small>';
		}
		echo '<div class="submit">
			<input class="button-primary" type="submit" name="randomtext_Save" value="Save Changes" />
			</div>
			</form>
			
			<p>Return to <a href="options-general.php?page=randomtext">Random Text summary page</a>.</p>';
	}
  echo '</div>';	
}

function randomtext_save($data) {
	global $wpdb, $user_ID;
	$table_name = $wpdb->prefix . 'randomtext';
	
	$randomtext_id = intval($data['randomtext_id']);
	check_admin_referer('randomtext_edit'.$randomtext_id);
	
	$sqldata = array();
	$category_new = trim($data['randomtext_category_new']);
	$sqldata['category'] = ($category_new) ? $category_new : $data['randomtext_category'];
	$sqldata['user_id'] = $user_ID;
	$sqldata['visible'] = $data['randomtext_visible'];
	
	// check for "Bulk Insert"
	$do_bulkinsert = isset($data['randomtext_bulkinsert']) ? $data['randomtext_bulkinsert'] : 'no';
	if ($do_bulkinsert == 'yes') {
		// Split the data by carriage returns
		$lines = preg_split("/[\n|\r]/", trim(stripslashes($data['randomtext_text'])));
		foreach ($lines as $key=>$value) {
			// Ignore empty lines
			if (!empty($value)) {
				// Set the datavalue and insert
				$sqldata['text'] = $value;
				$wpdb->insert($table_name, $sqldata);
			}
		}
	} else {
		// single record insert/update
		$sqldata['text'] = trim(stripslashes($data['randomtext_text']));
		if($randomtext_id)
			$wpdb->update($table_name, $sqldata, array('randomtext_id'=>$randomtext_id));
		else
			$wpdb->insert($table_name, $sqldata);
	}
	
}

function randomtext_delete($id) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'randomtext';
	$id = intval($id);
	$sql = "DELETE FROM $table_name WHERE randomtext_id = $id";
	$wpdb->query($sql);
}

?>