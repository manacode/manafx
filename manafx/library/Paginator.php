<?php

/**
 * Paginator
 *
 * Helps to build Pagination
 */
class Paginator extends Phalcon\Mvc\User\Component
{
	var $base_url			= ''; // The page we are linking to
	var $current_url	= ''; // The page we are linking to
	var $prefix				= ''; // A custom prefix added to the path.
	var $suffix				= ''; // A custom suffix added to the path.

	var $total_rows			=  0; // Total number of items (database results)
	var $per_page			= 10; // Max number of items you want shown per page
	var $num_links			=  2; // Number of "digit" links to show before/after the currently viewed page
	var $cur_page			=  0; // The current page being viewed
	var $use_page_numbers	= TRUE; // Use page number for segment instead of offset
	var $first_link			= '&lsaquo; First';
	var $next_link			= '&gt;';
	var $prev_link			= '&lt;';
	var $last_link			= 'Last &rsaquo;';
	var $full_tag_open		= '';
	var $full_tag_close		= '';
	var $first_tag_open		= '';
	var $first_tag_close	= '&nbsp;';
	var $last_tag_open		= '&nbsp;';
	var $last_tag_close		= '';
	var $cur_tag_open		= '&nbsp;<strong>';
	var $cur_tag_close		= '</strong>';
	var $next_tag_open		= '&nbsp;';
	var $next_tag_close		= '&nbsp;';
	var $prev_tag_open		= '&nbsp;';
	var $prev_tag_close		= '';
	var $num_tag_open		= '&nbsp;';
	var $num_tag_close		= '';
	var $query_string_segment = 'page';
	var $display_pages		= TRUE;
	var $anchor_class		= '';

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 */
	public function __construct($params = array())
	{
		if (count($params) > 0) {
			$this->initialize($params);
		}
		if ($this->anchor_class != '') {
			$this->anchor_class = 'class="'.$this->anchor_class.'" ';
		}
	}

	/**
	 * Initialize Preferences
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 * @return	void
	 */
	function initialize($params = array())
	{
		if (count($params) > 0) {
			foreach ($params as $key => $val) {
				if (isset($this->$key)) {
					$this->$key = $val;
				}
			}
		}
	}

	function create_links()
	{
		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows == 0 OR $this->per_page == 0) {
			return '';
		}
		// Calculate the total number of pages
		$num_pages = ceil($this->total_rows / $this->per_page);
		if ($num_pages == 1) {
			return '';
		}
		// Set the base page index for starting page number
		if ($this->use_page_numbers) {
			$base_page = 1;
		} else {
			$base_page = 0;
		}
		// Set current page to 1 if using page numbers instead of offset
		if ($this->use_page_numbers AND $this->cur_page == 0) {
			$this->cur_page = $base_page;
		}
		$this->num_links = (int)$this->num_links;
		if ($this->num_links < 1) {
			throw new Exception($this->t->_('Your number of links must be a positive number.'));
		}
		if (!is_numeric($this->cur_page)) {
			$this->cur_page = $base_page;
		}
		// Show the last page if the page number beyond the result range
		if ($this->use_page_numbers) {
			if ($this->cur_page > $num_pages) {
				$this->cur_page = $num_pages;
			}
		} else {
			if ($this->cur_page > $this->total_rows) {
				$this->cur_page = ($num_pages - 1) * $this->per_page;
			}
		}

		$uri_page_number = $this->cur_page;
		
		if (!$this->use_page_numbers) {
			$this->cur_page = floor(($this->cur_page/$this->per_page) + 1);
		}

		// Calculate the start and end numbers.
		// These determine which number to start and end the digit links with
		$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;

		// Is pagination being used over GET or POST?
		// If GET, add a per_page query string. If POST, add a trailing slash to the base URL if needed.
		$request = new \Phalcon\Http\Request();
		if ($request->isPost() == true) {
			$this->base_url = rtrim($this->base_url, '/') .'/';
		} else {
			$this->base_url = '/' . $_GET['_url'];
			$aGet = $_GET;
			if (count($aGet>0)) {
				$this->base_url .= "?";
			}
			foreach($aGet as $var => $value) {
				if ($var != $this->query_string_segment && $var != "_url") {
					$this->base_url .= $var . '=' . $value . '&amp;';
				}
			}
			$this->base_url .= $this->query_string_segment.'=';
		}
	
		$output = '';

		// Render the "First" link
		if  ($this->first_link !== FALSE AND $this->cur_page > ($this->num_links + 1)) {
			$output .= $this->first_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.'1">'.$this->first_link.'</a>'.$this->first_tag_close;
		}

		// Render the "previous" link
		if  ($this->prev_link !== FALSE AND $this->cur_page != 1) {
			if ($this->use_page_numbers) {
				$i = $uri_page_number - 1;
			} else {
				$i = $uri_page_number - $this->per_page;
			}
			$i = ($i == 0) ? '' : $this->prefix.$i.$this->suffix;
			$output .= $this->prev_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$i.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
		}

		// Render the pages
		if ($this->display_pages !== FALSE) {
			// Write the digit links
			for ($loop = $start -1; $loop <= $end; $loop++) {
				if ($this->use_page_numbers) {
					$i = $loop;
				} else {
					$i = ($loop * $this->per_page) - $this->per_page;
				}

				if ($i >= $base_page) {
					if ($this->cur_page == $loop) {
						$output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
					} else {
						$n = ($i == $base_page) ? '1' : $i;
						$n = ($n == '') ? '' : $this->prefix.$n.$this->suffix;
						$output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$n.'">'.$loop.'</a>'.$this->num_tag_close;
					}
				}
			}
		}

		// Render the "next" link
		if ($this->next_link !== FALSE AND $this->cur_page < $num_pages) {
			if ($this->use_page_numbers) {
				$i = $this->cur_page + 1;
			} else {
				$i = ($this->cur_page * $this->per_page);
			}
			$output .= $this->next_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$this->prefix.$i.$this->suffix.'">'.$this->next_link.'</a>'.$this->next_tag_close;
		}

		// Render the "Last" link
		if ($this->last_link !== FALSE AND ($this->cur_page + $this->num_links) < $num_pages) {
			if ($this->use_page_numbers) {
				$i = $num_pages;
			} else {
				$i = (($num_pages * $this->per_page) - $this->per_page);
			}
			$output .= $this->last_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$this->prefix.$i.$this->suffix.'">'.$this->last_link.'</a>'.$this->last_tag_close;
		}

		// Remove double slashes
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->full_tag_open . $output . $this->full_tag_close;

		return $output;
	}
}