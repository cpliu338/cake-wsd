<?php
/*
Adapted from https://github.com/blueimp/jQuery-File-Upload/blob/master/server/php/UploadHandler.php
*/
namespace ChunkedFileUpload\Handler;

class UploadHandler {

    protected $options;

    // PHP File Upload error message codes:
    // https://php.net/manual/en/features.file-upload.errors.php
    protected $error_messages = array(
    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => 'The uploaded file was only partially uploaded',
    4 => 'No file was uploaded',
    6 => 'Missing a temporary folder',
    7 => 'Failed to write file to disk',
    8 => 'A PHP extension stopped the file upload',
    'post_max_size' => 'The uploaded file exceeds the post_max_size directive in php.ini',
    'max_file_size' => 'File is too big',
    'min_file_size' => 'File is too small',
    'accept_file_types' => 'Filetype not allowed',
    'max_number_of_files' => 'Maximum number of files exceeded',
    'invalid_file_type' => 'Invalid file type',
    'max_width' => 'Image exceeds maximum width',
    'min_width' => 'Image requires a minimum width',
    'max_height' => 'Image exceeds maximum height',
    'min_height' => 'Image requires a minimum height',
    'abort' => 'File upload aborted',
    'image_resize' => 'Failed to resize image'
);

const IMAGETYPE_GIF = 'image/gif';
const IMAGETYPE_JPEG = 'image/jpeg';
const IMAGETYPE_PNG = 'image/png';

public function handle() {
    /*
    if ($this->get_query_param('_method') === 'DELETE') {
        return $this->delete($print_response);
    }
    */
    $result = [];
    $upload = $this->get_upload_data($this->options['param_name']);
    // Parse the Content-Disposition header, if available:
    $content_disposition_header = $this->get_server_var('HTTP_CONTENT_DISPOSITION');
    $file_name = $content_disposition_header ?
        rawurldecode(preg_replace(
            '/(^[^"]+")|("$)/',
            '',
            $content_disposition_header
        )) : null;
    // Parse the Content-Range header, which has the following form:
    // Content-Range: bytes 0-524287/2000000
    $content_range_header = $this->get_server_var('HTTP_CONTENT_RANGE');
    $content_range = $content_range_header ?
        preg_split('/[^0-9]+/', $content_range_header) : null;
    $size =  @$content_range[3];
    $files = array();
    if ($upload) {
            // param_name is a single object identifier like "file",
            // $upload is a one-dimensional array:
            $files[] = $this->handle_file_upload(
                isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
                $file_name ? $file_name : (isset($upload['name']) ?
                    $upload['name'] : null),
                $size ? $size : (isset($upload['size']) ?
                    $upload['size'] : $this->get_server_var('CONTENT_LENGTH')),
                isset($upload['type']) ?
                    $upload['type'] : $this->get_server_var('CONTENT_TYPE'),
                isset($upload['error']) ? $upload['error'] : null,
                null,
                $content_range
            );
    }
    $result['upload'] = $upload;
    $result['files'] = $files;
    return $result;
}
protected function handle_file_upload($uploaded_file, $name, $size, $type, $error,
    $index = null, $content_range = null) {
    $file = new \stdClass();
    $file->name = $this->get_file_name($uploaded_file, $name, $size, $type, $error,
        $index, $content_range);
    $file->size = $this->fix_integer_overflow((int)$size);
    $file->type = $type;
    if ($this->validate($uploaded_file, $file, $error, $index, $content_range)) {
        $this->handle_form_data($file, $index);
        $upload_dir = $this->get_upload_path();
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, $this->options['mkdir_mode'], true);
        }
        $file_path = $this->get_upload_path($file->name);
        $append_file = $content_range && is_file($file_path) &&
            $file->size > $this->get_file_size($file_path);
            $file->file_path = $file_path;
        if ($uploaded_file && is_uploaded_file($uploaded_file)) {
            // multipart/formdata uploads (POST method uploads)
            if ($append_file) {
                file_put_contents(
                    $file_path,
                    fopen($uploaded_file, 'r'),
                    FILE_APPEND
                );
            } else {
                move_uploaded_file($uploaded_file, $file_path);
            }
        } else {
            // Non-multipart uploads (PUT method support)
            file_put_contents(
                $file_path,
                fopen($this->options['input_stream'], 'r'),
                $append_file ? FILE_APPEND : 0
            );
        }
        $file_size = $this->get_file_size($file_path, $append_file);
        if ($file_size === $file->size) {
            //$file->url = $this->get_download_url($file->name);
            //if ($this->has_image_file_extension($file->name)) {
            //    if ($content_range && !$this->validate_image_file($file_path, $file, $error, $index)) {
            //        unlink($file_path);
            //    } else {
            //        $this->handle_image_file($file_path, $file);
            //    }
            //}
        } else {
            $file->size = $file_size;
            if (!$content_range && $this->options['discard_aborted_uploads']) {
                unlink($file_path);
                $file->error = $this->get_error_message('abort');
            }
        }
        //$this->set_additional_file_properties($file);
    }
    return $file;
}
protected function get_file_name($file_path, $name, $size, $type, $error,
    $index, $content_range) {
    $name = $this->trim_file_name($file_path, $name, $size, $type, $error,
        $index, $content_range);
    return $this->get_unique_filename(
        $file_path,
        $this->fix_file_extension($file_path, $name, $size, $type, $error,
            $index, $content_range),
        $size,
        $type,
        $error,
        $index,
        $content_range
    );
}
protected function get_upload_path($file_name = null, $version = null) {
    // used user specified name no matter what in get_file_name
    $file_name = $file_name ? $file_name : '';
    if (empty($version)) {
        $version_path = '';
    } else {
        $version_dir = @$this->options['image_versions'][$version]['upload_dir'];
        if ($version_dir) {
            return $version_dir.$this->get_user_path().$file_name;
        }
        $version_path = $version.'/';
    }
    return $this->options['upload_dir'] /*.$this->get_user_path()*/
        .$version_path.$file_name;
}
// Fix for overflowing signed 32 bit integers,
// works for sizes up to 2^32-1 bytes (4 GiB - 1):
protected function fix_integer_overflow($size) {
    if ($size < 0) {
        $size += 2.0 * (PHP_INT_MAX + 1);
    }
    return $size;
}
protected function handle_form_data($file, $index) {
    // Handle form data, e.g. $_POST['description'][$index]
}
protected function validate($uploaded_file, $file, $error, $index, $content_range) {
    if ($error) {
        $file->error = $this->get_error_message($error);
        return false;
    }
    $content_length = $this->fix_integer_overflow(
        (int)$this->get_server_var('CONTENT_LENGTH')
    );
    $post_max_size = $this->get_config_bytes(ini_get('post_max_size'));
    if ($post_max_size && ($content_length > $post_max_size)) {
        $file->error = $this->get_error_message('post_max_size');
        return false;
    }
    if (!preg_match($this->options['accept_file_types'], $file->name)) {
        $file->error = $this->get_error_message('accept_file_types');
        return false;
    }
    if ($uploaded_file && is_uploaded_file($uploaded_file)) {
        $file_size = $this->get_file_size($uploaded_file);
    } else {
        $file_size = $content_length;
    }
    if ($this->options['max_file_size'] && (
            $file_size > $this->options['max_file_size'] ||
            $file->size > $this->options['max_file_size'])
    ) {
        $file->error = $this->get_error_message('max_file_size');
        return false;
    }
    if ($this->options['min_file_size'] &&
        $file_size < $this->options['min_file_size']) {
        $file->error = $this->get_error_message('min_file_size');
        return false;
    }
    if (is_int($this->options['max_number_of_files']) &&
        ($this->count_file_objects() >= $this->options['max_number_of_files']) &&
        // Ignore additional chunks of existing files:
        !is_file($this->get_upload_path($file->name))) {
        $file->error = $this->get_error_message('max_number_of_files');
        return false;
    }
    /*
    if (!$content_range && $this->has_image_file_extension($file->name)) {
        return $this->validate_image_file($uploaded_file, $file, $error, $index);
    }
    */
    return true;
}

protected function get_file_objects($iteration_method = 'get_file_object') {
    $upload_dir = $this->get_upload_path();
    if (!is_dir($upload_dir)) {
        return array();
    }
    return array_values(array_filter(array_map(
        array($this, $iteration_method),
        scandir($upload_dir)
    )));
}

protected function count_file_objects() {
    return count($this->get_file_objects('is_valid_file_object'));
}
protected function get_error_message($error) {
    return isset($this->error_messages[$error]) ?
        $this->error_messages[$error] : $error;
}
protected function get_file_size($file_path, $clear_stat_cache = false) {
    if ($clear_stat_cache) {
        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            clearstatcache(true, $file_path);
        } else {
            clearstatcache();
        }
    }
    return $this->fix_integer_overflow(filesize($file_path));
}
public function get_config_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    if (is_numeric($val)) {
        $val = (int)$val;
    } else {
        $val = (int)substr($val, 0, -1);
    }
    switch ($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $this->fix_integer_overflow($val);
}    
protected function fix_file_extension($file_path, $name, $size, $type, $error,
    $index, $content_range) {
    // Add missing file extension for known image types:
    if (strpos($name, '.') === false &&
        preg_match('/^image\/(gif|jpe?g|png)/', $type, $matches)) {
        $name .= '.'.$matches[1];
    }
    if ($this->options['correct_image_extensions']) {
        $extensions = $this->get_valid_image_extensions($file_path);
        // Adjust incorrect image file extensions:
        if (!empty($extensions)) {
            $parts = explode('.', $name);
            $extIndex = count($parts) - 1;
            $ext = strtolower(@$parts[$extIndex]);
            if (!in_array($ext, $extensions)) {
                $parts[$extIndex] = $extensions[0];
                $name = implode('.', $parts);
            }
        }
    }
    return $name;
}
protected function get_unique_filename($file_path, $name, $size, $type, $error,
    $index, $content_range) {
    while(is_dir($this->get_upload_path($name))) {
        $name = $this->upcount_name($name);
    }
    // Keep an existing filename if this is part of a chunked upload:
    $uploaded_bytes = $this->fix_integer_overflow((int)@$content_range[1]);
    while (is_file($this->get_upload_path($name))) {
        if ($uploaded_bytes === $this->get_file_size(
                $this->get_upload_path($name))) {
            break;
        }
        $name = $this->upcount_name($name);
    }
    return $name;
}
protected function trim_file_name($file_path, $name, $size, $type, $error,
    $index, $content_range) {
    // Remove path information and dots around the filename, to prevent uploading
    // into different directories or replacing hidden system files.
    // Also remove control characters and spaces (\x00..\x20) around the filename:
    $name = trim($this->basename(stripslashes($name)), ".\x00..\x20");
    // Replace dots in filenames to avoid security issues with servers
    // that interpret multiple file extensions, e.g. "example.php.png":
    $replacement = $this->options['replace_dots_in_filenames'];
    if (!empty($replacement)) {
        $parts = explode('.', $name);
        if (count($parts) > 2) {
            $ext = array_pop($parts);
            $name = implode($replacement, $parts).'.'.$ext;
        }
    }
    // Use a timestamp for empty filenames:
    if (!$name) {
        $name = str_replace('.', '-', microtime(true));
    }
    return $name;
}
protected function get_valid_image_extensions($file_path) {
    switch ($this->imagetype($file_path)) {
        case self::IMAGETYPE_JPEG:
            return array('jpg', 'jpeg');
        case self::IMAGETYPE_PNG:
            return  array('png');
        case self::IMAGETYPE_GIF:
            return array('gif');
    }
}
protected function get_upload_data($id) {
    return @$_FILES[$id];
}
protected function get_server_var($id) {
    return @$_SERVER[$id];
}
public function __construct($options = null, /*$initialize = true, */$error_messages = null) {
    $this->options = array(
        'script_url' => '',//$this->get_full_url().'/'.$this->basename($this->get_server_var('SCRIPT_NAME')),
        'upload_dir' => dirname($this->get_server_var('SCRIPT_FILENAME')).'/files/',
        'upload_url' => '',//$this->get_full_url().'/files/',
        'input_stream' => 'php://input',
        'user_dirs' => false,
        'mkdir_mode' => 0755,
        'param_name' => 'files',
        // Set the following option to 'POST', if your server does not support
        // DELETE requests. This is a parameter sent to the client:
        'delete_type' => 'DELETE',
        'access_control_allow_origin' => '*',
        'access_control_allow_credentials' => false,
        'access_control_allow_methods' => array(
            'OPTIONS',
            'HEAD',
            'GET',
            'POST',
            'PUT',
            'PATCH',
            'DELETE'
        ),
        'access_control_allow_headers' => array(
            'Content-Type',
            'Content-Range',
            'Content-Disposition'
        ),
        // By default, allow redirects to the referer protocol+host:
        'redirect_allow_target' => '/^'.preg_quote(
                parse_url($this->get_server_var('HTTP_REFERER'), PHP_URL_SCHEME)
                .'://'
                .parse_url($this->get_server_var('HTTP_REFERER'), PHP_URL_HOST)
                .'/', // Trailing slash to not match subdomains by mistake
                '/' // preg_quote delimiter param
            ).'/',
        // Enable to provide file downloads via GET requests to the PHP script:
        //     1. Set to 1 to download files via readfile method through PHP
        //     2. Set to 2 to send a X-Sendfile header for lighttpd/Apache
        //     3. Set to 3 to send a X-Accel-Redirect header for nginx
        // If set to 2 or 3, adjust the upload_url option to the base path of
        // the redirect parameter, e.g. '/files/'.
        'download_via_php' => false,
        // Read files in chunks to avoid memory limits when download_via_php
        // is enabled, set to 0 to disable chunked reading of files:
        'readfile_chunk_size' => 10 * 1024 * 1024, // 10 MiB
        // Defines which files can be displayed inline when downloaded:
        'inline_file_types' => '/\.(gif|jpe?g|png)$/i',
        // Defines which files (based on their names) are accepted for upload.
        // By default, only allows file uploads with image file extensions.
        // Only change this setting after making sure that any allowed file
        // types cannot be executed by the webserver in the files directory,
        // e.g. PHP scripts, nor executed by the browser when downloaded,
        // e.g. HTML files with embedded JavaScript code.
        // Please also read the SECURITY.md document in this repository.
        'accept_file_types' => '/\.(gif|jpe?g|png)$/i',
        // Replaces dots in filenames with the given string.
        // Can be disabled by setting it to false or an empty string.
        // Note that this is a security feature for servers that support
        // multiple file extensions, e.g. the Apache AddHandler Directive:
        // https://httpd.apache.org/docs/current/mod/mod_mime.html#addhandler
        // Before disabling it, make sure that files uploaded with multiple
        // extensions cannot be executed by the webserver, e.g.
        // "example.php.png" with embedded PHP code, nor executed by the
        // browser when downloaded, e.g. "example.html.gif" with embedded
        // JavaScript code.
        'replace_dots_in_filenames' => '-',
        // The php.ini settings upload_max_filesize and post_max_size
        // take precedence over the following max_file_size setting:
        'max_file_size' => null,
        'min_file_size' => 1,
        // The maximum number of files for the upload directory:
        'max_number_of_files' => null,
        // Reads first file bytes to identify and correct file extensions:
        'correct_image_extensions' => false,
        // Image resolution restrictions:
        'max_width' => null,
        'max_height' => null,
        'min_width' => 1,
        'min_height' => 1,
        // Set the following option to false to enable resumable uploads:
        'discard_aborted_uploads' => true,
        // Set to 0 to use the GD library to scale and orient images,
        // set to 1 to use imagick (if installed, falls back to GD),
        // set to 2 to use the ImageMagick convert binary directly:
        'image_library' => 1,
        // Uncomment the following to define an array of resource limits
        // for imagick:
        /*
        'imagick_resource_limits' => array(
            imagick::RESOURCETYPE_MAP => 32,
            imagick::RESOURCETYPE_MEMORY => 32
        ),
        */
        // Command or path for to the ImageMagick convert binary:
        'convert_bin' => 'convert',
        // Uncomment the following to add parameters in front of each
        // ImageMagick convert call (the limit constraints seem only
        // to have an effect if put in front):
        /*
        'convert_params' => '-limit memory 32MiB -limit map 32MiB',
        */
        // Command or path for to the ImageMagick identify binary:
        'identify_bin' => 'identify',
        'image_versions' => array(
            // The empty image version key defines options for the original image.
            // Keep in mind: these image manipulations are inherited by all other image versions from this point onwards.
            // Also note that the property 'no_cache' is not inherited, since it's not a manipulation.
            '' => array(
                // Automatically rotate images based on EXIF meta data:
                'auto_orient' => true
            ),
            // You can add arrays to generate different versions.
            // The name of the key is the name of the version (example: 'medium').
            // the array contains the options to apply.
            /*
            'medium' => array(
                'max_width' => 800,
                'max_height' => 600
            ),
            */
            'thumbnail' => array(
                // Uncomment the following to use a defined directory for the thumbnails
                // instead of a subdirectory based on the version identifier.
                // Make sure that this directory doesn't allow execution of files if you
                // don't pose any restrictions on the type of uploaded files, e.g. by
                // copying the .htaccess file from the files directory for Apache:
                //'upload_dir' => dirname($this->get_server_var('SCRIPT_FILENAME')).'/thumb/',
                //'upload_url' => $this->get_full_url().'/thumb/',
                // Uncomment the following to force the max
                // dimensions and e.g. create square thumbnails:
                // 'auto_orient' => true,
                // 'crop' => true,
                // 'jpeg_quality' => 70,
                // 'no_cache' => true, (there's a caching option, but this remembers thumbnail sizes from a previous action!)
                // 'strip' => true, (this strips EXIF tags, such as geolocation)
                'max_width' => 80, // either specify width, or set to 0. Then width is automatically adjusted - keeping aspect ratio to a specified max_height.
                'max_height' => 80 // either specify height, or set to 0. Then height is automatically adjusted - keeping aspect ratio to a specified max_width.
            )
        ),
        'print_response' => true
    );
    if ($options) {
        $this->options = $options + $this->options;
    }
    if ($error_messages) {
        $this->error_messages = $error_messages + $this->error_messages;
    }
    /*
    if ($initialize) {
        $this->initialize();
    }
    */
}
protected function basename($filepath, $suffix = null) {
    $splited = preg_split('/\//', rtrim ($filepath, '/ '));
    return substr(basename('X'.$splited[count($splited)-1], $suffix), 1);
}
}