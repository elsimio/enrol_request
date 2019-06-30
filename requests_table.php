<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * @package   enrol_request
 * @copyright 2012 Inter-American Development Bank (http://www.iadb.org) (bid-indes@iadb.org)
 * @author    Maiquel Sampaio de Melo - Melvyn Gomez (melvyng@openranger.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * Based on work of Michael Avelar <mavelar@moodlerooms.com> and Lucas Sa (lucas.sa@gmail.com)
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

class enrol_request_requests_table extends flexible_table {
	
    /** @var integer total of requests */
    private $page_requests_count;
	
    /** @var integer total */
    private $total;
	
    /** @var string URL for the form */
    private $submiturl;
	
    /** @var integer total of records per page */
    private $perpage;
	
    /** @var strings status */
    private $selstatus;	
	
    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct('enrol-request-manage-requests');
    }
	
	/**
	 * 	Set parameteres for wrap code around the table
	 */
	public function set_wrap_parameters($page_requests_count, $total, $submiturl, $perpage, $selstatus){
		$this->page_requests_count = $page_requests_count;
		$this->total = $total;
		$this->submiturl = $submiturl;
		$this->perpage = $perpage;
		$this->selstatus = $selstatus;
	}
	
    public function  wrap_html_start() {
		echo '<p>' . get_string('showing', 'enrol_request') . ' ' . $this->page_requests_count . ' ' .  get_string('of', 'enrol_request') . ' ' . $this->total . ' </p>';
		
		echo '<div class="enrol-request-center-text">';
    }

    public function wrap_html_finish() {
		echo '<br/></div>';
    }
}
