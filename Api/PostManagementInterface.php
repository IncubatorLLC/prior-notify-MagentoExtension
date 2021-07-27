<?php 
namespace IncubatorLLC\PriorNotify\Api;


interface PostManagementInterface {

	/**
	 * GET for Post api
	 * @param string $token
	 * @return string
	 */
	
	public function getPost($token);

	/**
	 * GET for Post api
	 * @return string
	 */

	public function deleteToken();

	/**
	 * GET for Post api
	 * @return string
	 */

	public function checkToken();
}