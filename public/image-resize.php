<?php
ini_set("memory_limit","80M");

# prevent creation of new directories
$is_locked = false;

# file formats to ignore
$ignore_formats = ['GIF'];

# figure out requested path and actual physical file paths
$orig_dir = dirname(__FILE__);
$path = $_GET['path'];

$tokens = explode("/", $path);
$file = "/".implode('/', array_slice($tokens,1));

$orig_file = $orig_dir.$file;

# Check if this is an image in storage or not
if(!preg_match('/\/www\/public\/assets\/img/i', $orig_file))
{
  $orig_file = preg_replace('/\/public/', '', $orig_file);
  $orig_file = preg_replace('/\/www/', '/www/storage/app/public', $orig_file);
}

if (!file_exists($orig_file))
{
  header("Status: 404 Not Found");
  echo "<br/>PATH=$path<br/>ORIGFILE=$orig_file";
  return 0;
}

# check if new directory would need to be created
$save_path = "$orig_dir/imagecache/$tokens[0]$file";
$save_dir = dirname($save_path);

if(!file_exists($save_dir) && $is_locked)
{
  header("Status: 403 Forbidden");
  echo "Directory creation is forbidden.";
  return 0;
}

# parse out the requested image dimensions and resize mode
$x_pos = strpos($tokens[0], 'x');
$dash_pos = strpos($tokens[0], '-');
$target_width = substr($tokens[0], 0, $x_pos);
$target_height = substr($tokens[0], $x_pos+1, $dash_pos-$x_pos-1);
$mode = substr($tokens[0], $dash_pos+1);

$new_width = $target_width;
$new_height = $target_height;
$image = new Imagick($orig_file);

$bypass_processing = in_array($image->getImageFormat(), $ignore_formats);
if(!$bypass_processing)
{
  list($orig_width, $orig_height, $type, $attr) = getimagesize($orig_file);

  # preserve aspect ratio, fitting image to specified box
  if ($mode == "0")
  {
    $new_height = $orig_height * $new_width / $orig_width;
    if(is_numeric($target_height) && ($new_height > $target_height))
    {
      $new_width = $orig_width * $target_height / $orig_height;
      $new_height = $target_height;
    }
  }
  # zoom and crop to exactly fit specified box
  else if ($mode == "2")
  {
    // crop to get desired aspect ratio
    $desired_aspect = $target_width / $target_height;
    $orig_aspect = $orig_width / $orig_height;

    if ($desired_aspect > $orig_aspect)
    {
      $trim = $orig_height - ($orig_width / $desired_aspect);
      $image->cropImage($orig_width, $orig_height-$trim, 0, $trim/2);
      error_log("HEIGHT TRIM $trim");
    }
    else
    {
      $trim = $orig_width - ($orig_height * $desired_aspect);
      $image->cropImage($orig_width-$trim, $orig_height, $trim/2, 0);
    }
  }

  # mode 3 (stretch to fit) is automatic fall-through as image will be blindly resized
  $image->resizeImage($new_width, $new_height, imagick::FILTER_LANCZOS, 1);
  $image->setImageCompression(Imagick::COMPRESSION_JPEG);
  $image->setImageCompressionQuality(90);
  $image->setImageFormat("jpg");
  $image->stripImage();

  # save and return the resized image file
  if(!file_exists($save_dir))
    mkdir($save_dir, 0777, true);

  $image->writeImage($save_path);
}
else
{
  # Copy image over, no processing done
  if(!file_exists($save_dir))
    mkdir($save_dir, 0777, true);

  copy($orig_file, $save_path);
}

header("Content-Type: image/".$image->getImageFormat());
echo file_get_contents($save_path);

return true;