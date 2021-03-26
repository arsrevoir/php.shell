<?php 

    // file handling related
    class FileHandler {
        private array $files_arr = array();
        private array $formats;
        
        public function __construct($files, $path, $format) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);

            $this->formats = $format;
            $this->file_count = count($files['name']) - 1;

            for($i=0;$i<=$this->file_count;$i++) {
                $ext = array_search($finfo->file($files['tmp_name'][$i]), $this->formats, true);
                $file_name = name_generate(5, 20) . '.' . $ext;

                $file = array(
                    'file' => array(
                        'name'     => $files['name'][$i],
                        'type'     => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error'    => $files['error'][$i],
                        'size'     => $files['size'][$i]
                    ),

                    'file_name' => array(
                        'file_name' => $file_name,
                        'ext'       => $ext
                    ),

                    'path' => $path . $file_name
                );

                array_push($this->files_arr, $file);
            }
        }

        public function validateFile($ext, $formats) {
            for($i=0;$i<=$this->file_count;$i++) {

                 // --- undefined/ multiply files/ corruption attack 
                if (!isset($this->files_arr[$i]['file']['error']) || is_array($this->files_arr[$i]['file']['error'])) {
                    throw new RuntimeException('Invalid parameters.');
                }
    
                // --- error check
                switch ($this->files_arr[$i]['file']['error']) {
                    case UPLOAD_ERR_OK:
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        throw new RuntimeException('No file sent.');
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        throw new RuntimeException('Exceeded filesize limit.');
                    default:
                        throw new RuntimeException('Unknown errors.');
                }
    
                // --- format check
                if($ext === FALSE) {
                    throw new RuntimeException('Unexpected file format, expect: ' . arr_to_string($formats) . '; Obtain: ' . $ext);
                }
            }
        }


        public function moveFile() {
            try {
                for($i=0;$i<=$this->file_count;$i++) {
                    $this->validateFile($this->files_arr[$i]['file_name']['ext'], $this->formats);
                }

                for($i=0;$i<=$this->file_count;$i++) {
                    if(!move_uploaded_file($this->files_arr[$i]['file']['tmp_name'], $this->files_arr[$i]['path'])) {
                        throw new RuntimeException('RunTime Error');
                    }
                }
            } catch(RuntimeException $e) {
                echo $e->getMessage();
            }
        }
    }

?>