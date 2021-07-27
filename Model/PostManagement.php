<?php 
namespace IncubatorLLC\PriorNotify\Model;

use Magento\Framework\App\ResourceConnection;
use IncubatorLLC\PriorNotify\Api\PostManagementInterface;
use Magento\Framework\HTTP\Client\Curl;
class PostManagement implements PostManagementInterface {

	const PRIOR_NOTIFY_TABLE = 'prior_notify';
    const KEY_FIELD = 'key';
    const VALUE_FIELD = 'value';

	
	/**
	 * @var ResourceConnection
     */
	private $resourceConnection;

	/**
     * @var Curl
     */
    protected $curl;

    public function __construct(
		ResourceConnection $resourceConnection
    ) {
		$this->resourceConnection = $resourceConnection;
		$this->curl = new Curl();

	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getPost($token) 
	{
		$connection  = $this->resourceConnection->getConnection();
		$tableName = $connection->getTableName(self::PRIOR_NOTIFY_TABLE);

		try {
			$data = [ self::KEY_FIELD => 'token', self::VALUE_FIELD => $token ];
			$connection->insert(self::PRIOR_NOTIFY_TABLE, $data);
			return 'success';
		} catch (Exception $error) {
			return 'error  ' . $error;
		}
	}

	public function deleteToken() 
	{
		$connection  = $this->resourceConnection->getConnection();
		$tableName = $connection->getTableName(self::PRIOR_NOTIFY_TABLE);

		try {
			$query = 'SELECT value FROM ' . $tableName . ' ORDER BY id DESC limit 1';
			$token =$this->resourceConnection->getConnection()->fetchOne($query);

			if ($token) { 
				$whereConditions = [
					$connection->quoteInto('created_at > ?', strtotime('-2 weeks')),
				];
				$connection->delete(self::PRIOR_NOTIFY_TABLE, $whereConditions);
				
				$url = 'https://dev-api.priornotify.com/integrations/magento/logout';
				
				$this->curl->addHeader("x-magento-plugin-token", "Token ".$token);
				$this->curl->post($url, []);
				$response = json_decode($this->curl->getBody(), true);
				if (isset($response['success'])) {
					return 'success';
				}
				return 'invalid token';
			}
			return 'not exist';
		} catch (Exception $error) {
			return 'error  ' . $error;
		}
	}

	public function checkToken()
	{
		$connection  = $this->resourceConnection->getConnection();
		$tableName = $connection->getTableName(self::PRIOR_NOTIFY_TABLE);

		try {
			$query = 'SELECT value FROM ' . $tableName . ' ORDER BY id DESC limit 1';
			$token =$this->resourceConnection->getConnection()->fetchOne($query);
			if ($token) { 
				return 'success';
			}
			return 'not exist';
		} catch (Exception $error) {
			return 'error  ' . $error;
		}
	}
}