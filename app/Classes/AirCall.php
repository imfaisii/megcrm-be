<?php

namespace App\Classes;

use App\traits\Jsonify;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class AirCall
{
  use Jsonify;

  protected string $version = "v1";
  protected string $BaseUrl =  "https://api.aircall.io/";

  protected Http|PendingRequest $HttpClient;

  protected $data;

  public function __construct()
  {
    $this->data = collect();
    $token =  base64_encode(config("credentials.AIRCALL_API_ID") . ":" . config("credentials.AIRCALL_API_TOKEN"));
    $this->HttpClient =  Http::withHeaders([
      'Authorization' => "Basic {$token}",
      'Accept' => 'application/json',
      'Content-Type' => 'application/json',
    ])->timeout(120)->retry(3, 1000);
  }
  public function pingServer()
  {
    try {
      $response = $this->HttpClient->get("{$this->BaseUrl}{$this->version}/ping");
      return $response->successful() ? $this->success(data: $response->json()) : $this->error();
    } catch (Exception $e) {
      return $this->error($e);
    }
  }
  /**
   * Get details of the users associated with the account or if the user id is provided then get the specific user's details 
   *
   * @param array $queryParams
   * @param string|null $userId
   * @return JsonResponse
   */
  public function getUsers(array $queryParams = ['order' => 'asc'], ?string $userId = null): JsonResponse
  {
    try {
      $isNextPage = true;
      $Url = $userId ?  Str::of("{$this->BaseUrl}{$this->version}/users/")->append($userId) : "{$this->BaseUrl}{$this->version}/users";
      while (!empty($isNextPage)) {
        $response = $this->HttpClient->get($Url, $queryParams);
        if ($response->successful()) {
          $Url = data_get($response->json(), 'meta.next_page_link', null);
          $isNextPage = filled($Url);
          $result = data_get($response->json(), $userId ? 'user' : 'users', []);
          $ResponseData =   filled($result) ? (Arr::get($result, '0', null) ? $result : [$result]) : [];
          // dd($ResponseData);
          foreach ($ResponseData as $eachuser) {
            $this->data->push($eachuser);
          }
        } else {
          $isNextPage = null;
        }
      }
      return $response->successful() ? $this->success(data: $this->data) : $this->error();
    } catch (Exception $e) {
      return $this->error($e);
    }
  }


  /**
   * Get details of the users availablities
   *
   * @param array $queryParams
   * @return JsonResponse
   */
  public function getAvailablities(array $queryParams = ['order' => 'asc']): JsonResponse
  {
    try {
      $isNextPage = true;
      $Url =  "{$this->BaseUrl}{$this->version}/users/availabilities";
      while (!empty($isNextPage)) {
        $response = $this->HttpClient->get($Url, $queryParams);
        if ($response->successful()) {
          $Url = data_get($response->json(), 'meta.next_page_link', null);
          $isNextPage = filled($Url);
          foreach (data_get($response->json(),  'users', []) as $eachuser) {
            $this->data->push($eachuser);
          }
        } else {
          $isNextPage = null;
        }
      }
      return $response->successful() ? $this->success(data: $this->data) : $this->error();
    } catch (Exception $e) {
      return $this->error($e);
    }
  }

  /**
   * Get details of the users availablities
   *
   * @param string $userId
   * @return JsonResponse
   */
  public function getAvailablityOfAUser(string $userId): JsonResponse
  {
    try {
      $Url =  "{$this->BaseUrl}{$this->version}/users/{$userId}/availability";
      $response = $this->HttpClient->get($Url);
      return $response->successful() ? $this->success(data: $response->json()) : $this->error();
    } catch (Exception $e) {
      return $this->error($e);
    }
  }


  /**
   * Start An Outbound Call 
   * @param string $userId
   * @param array $queryParams
   * @return JsonResponse
   */
  public function startACall(string $userId, array $queryParams = []): JsonResponse
  {
    try {
      if (blank($queryParams) || blank(data_get($queryParams, 'number_id', null)) || blank(data_get($queryParams, 'to', null)))
        return $this->error();

      $Url =  "{$this->BaseUrl}{$this->version}/users/{$userId}/calls";
      $response = $this->HttpClient->POST($Url, $queryParams);
      return $response->successful() ? $this->success(data: $response->json()) : $this->error(message: "Couldn't do it because of status Code :{$response->status()}");
    } catch (Exception $e) {
      dd($e->getMessage());
      return $this->error($e);
    }
  }


  /**
   * set the dial number on the app for user 
   * @param string $userId
   * @param array $queryParams
   * @return JsonResponse
   */
  public function dialCall(string $userId, array $queryParams = []): JsonResponse
  {
    try {
      if (blank($queryParams) ||  blank(data_get($queryParams, 'to', null)))
        return $this->error();

      $Url =  "{$this->BaseUrl}{$this->version}/users/{$userId}/dial";
      $response = $this->HttpClient->POST($Url, $queryParams);
      return $response->successful() ? $this->success(data: $response->json()) : $this->error(message: "Couldn't do it because of status Code :{$response->status()}");
    } catch (Exception $e) {
      dd($e->getMessage());
      return $this->error($e);
    }
  }




  public function testFunction()
  {
    try {
    } catch (Exception $e) {
      dd($e->getMessage());
    }
  }
}
