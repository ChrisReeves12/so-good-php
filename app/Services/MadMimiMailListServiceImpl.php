<?php
/**
 * The MadMimiMailListServiceImpl class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Services\Contracts\IMailListService;
use Mockery\Exception;

/**
 * Class MadMimiMailListServiceImpl
 * @package App\Services
 */
class MadMimiMailListServiceImpl implements IMailListService
{
  private $api_key;

  private $api_user;

  private $base_endpoint;

  private $timeout = 5;

  /**
   * MadMimiMailListServiceImpl constructor.
   */
  public function __construct()
  {
    $this->api_user = business('mad_mimi_user');
    $this->api_key = business('mad_mimi_api_key');
    $this->base_endpoint = 'https://api.madmimi.com';
    $this->timeout = 5;
  }


  /**
   * Add contact to list
   *
   * @param string $email
   * @param string $list_name
   * @param string $name
   */
  public function addContact(string $email, string $list_name, string $name = null)
  {
    $list_name = urlencode($list_name);
    $this->_doApiCall("/audience_lists/{$list_name}/add", ['email' => $email], 'POST');
  }

  /**
   * Update contact
   *
   * @param string $email
   * @param array $data
   * @throws \Exception
   */
  public function updateContact(string $email, array $data = [])
  {
    throw new \Exception('Not used.');
  }

  /**
   * Remove contact
   * @param string $email
   * @param string $list_name
   */
  public function removeContact(string $email, string $list_name = '')
  {
    $list_name = urlencode($list_name);
    $this->_doApiCall("/audience_lists/{$list_name}/remove?email=" . urlencode($email), [], 'POST');
  }

  /**
   * Do API connection call
   *
   * @param string $endpoint
   * @param array $data
   * @param string $method
   * @return mixed
   */
  private function _doApiCall(string $endpoint, array $data = [], string $method = 'POST')
  {
    $full_endpoint = $this->getBaseEndpoint() . $endpoint;
    $data['username'] = $this->getApiUser();
    $data['api_key'] = $this->getApiKey();

    // Make connection
    $ch = curl_init($full_endpoint);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->getTimeout());

    if($method !== 'POST' && $method !== 'GET')
    {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    }

    curl_setopt($ch, CURLOPT_POST, ($method === 'POST'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $response = curl_exec($ch);

    // Returns a blank response when the response passed
    if(!empty($response))
      throw new Exception('MadMimi API Error: ' . $response);

    return $response;
  }

  /**
   * @return string
   */
  public function getApiKey()
  {
    return $this->api_key;
  }

  /**
   * @return string
   */
  public function getApiUser()
  {
    return $this->api_user;
  }

  /**
   * @return int
   */
  public function getTimeout()
  {
    return $this->timeout;
  }

  /**
   * @return string
   */
  public function getBaseEndpoint()
  {
    return $this->base_endpoint;
  }
}