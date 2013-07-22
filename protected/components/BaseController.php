<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class BaseController extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to 'application.views.layouts.column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='application.views.layouts.main';
	
	/**
     * Contains js to put on page.
     * 
     * @property array
     */
    public $js = array();
	
	/**
	 * Low priority js.
	 * 
	 * @property array
	 */
	public $js_low_priority = array();
	
	/**
     * Contains css to put on page.
     *
     * @property array
     */
    public $css = array();
	
	/**
	 * Exclude css from minfication
	 */
	 public $excl_css = array();
	
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	
	public function init()
	{
		Yii::app()->request->baseUrl = URL;
		
		// Default js & css
		$this->js['/js/jquery-1.7.2.min.js'] = '/js/all.js';
		$this->js['/js/vendors/bootstrap/bootstrap.min.js'] = '/js/all.js';
		$this->excl_css['/css/bootstrap/css/bootstrap.min.css'] = '/css/all.css';
		$this->css['/css/site_main.css'] = '/css/all.css';
	}
}