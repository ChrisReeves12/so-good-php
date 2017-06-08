<?php
/**
 * The IAffiliateService class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

/**
 * Interface IAffiliateService
 * @package App\Services\Contracts
 */
interface IAffiliateService
{
  /**
   * Find a affiliates by slug
   * @param string $slug
   * @param bool $admin
   * @return mixed
   */
  public function findActiveAffiliate(string $slug, bool $admin = false);

  /**
   * @param int $id
   * @param UploadedFile $file
   * @return array
   */
  public function uploadImageToAffiliate(int $id, UploadedFile $file): array;

  /**
   * Remove image
   * @param int $id
   * @param string $removed_image
   * @param string $new_main_image
   * @return array
   */
  public function deleteImage(int $id, string $removed_image, string $new_main_image = null): array;

  /**
   * Update main image of affiliate
   * @param int $id
   * @param string $main_image_file
   * @return array
   */
  public function updateMainImage(int $id, string $main_image_file): array;

  /**
   * Get all active
   * @param bool $is_admin
   * @return Collection
   */
  public function getActiveVloggers(bool $is_admin = false): Collection;
}