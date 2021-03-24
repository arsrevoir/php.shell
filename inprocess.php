<?php


    // -----------------------------------------------------func-----------------------------------------------------

    // TODO: user input validation

    function length($arr) {
        return count($arr) - 1;
    }

    function arr_to_string($arr) {
        $string = '';
        $i = 0;

        foreach($arr as $elem) {
            if($i == length($arr)) {
                $string .= $elem;
            } else {
                $string .= $elem . ', ';
            }

            $i++;
        }

        return $string;
    }

    function name_generate($min, $max) {
        $sym = ['A','a','B','b','C','c','D','d','E','e','F','f','G','g','H','h','I','i','J','j','K','k','L','l','M','m','N','n','O','o','P','p','Q','q','R','r','S','s','T','t','U','u','V','v','W','w','X','x','Y','y','Z','z','1','2','3','4','5','6','7','8','9','0'];
        $finarr = [];
        $sym_num = floor(rand($min , $max));
        for($i = 0; $i < $sym_num;$i++) {
            $random = floor(rand(0,sizeof($sym) - 1));
            $finarr[$i] = $sym[$random];
        }   
        $str = implode('' , $finarr);
        return $str;
    }

    function regexp_validate($pattern, $string) { 
        if(preg_match($pattern, $string)) {
            return true;
        } else {
            return false;
        }
    }

    function unset_POST($post) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            unset($post);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }

    function childcheck($children, $form, $fontsize, $list_unit) {
        foreach($children as $child) {
            $child_name = $child['name'];
            $child_id = $child['id'];
            $basename = '';
            if($child['basename']) {
                $basename = 'document.php?doc_id=' . $child_id;
            }

            $list_unit .= "<li class='lower-menu-item'><a href='".$basename."' style='font-size: ".$fontsize."em'>". $child_name ."</a></li>";

            $children = $form->select('SELECT * FROM categories WHERE parent_id = :category_id', [':category_id' => $child_id]);

            if(!empty($children)) {

                return childcheck($children, $form, $fontsize - 0.2, $list_unit);

            } else {
                echo $list_unit;
                $list_unit = '';
            }

        }
    }

    function child_display($children, $form, $zindex) {
        $children_display = '';

        $zindex++;

        foreach($children as $child) {
            $children = $form->select('SELECT * FROM categories WHERE parent_id = :category_id', [':category_id' => $child['id']]);

            $children_display_local = "<li class='lower-menu-item'><a href='".$child['basename']."' style='font-size: 1.4em'>". $child['name'] ."</a>";

            if(!empty($children)) {
                $children_display .= $children_display_local . '<ul class="item-submenu" style="z-index:'.$zindex.';">' .  child_display($children, $form, $zindex) . '</ul></li>';
            } else {
                $children_display .= $children_display_local . '</li>';
                $children_display_local = '';
            }
        }

        return $children_display;
    }

    function array_add_to_subarray($array, $array_key, $subarray) {
        if(array_key_exists($array_key, $array)) {
            $array[$array_key] .= $subarray;
        } else {
            $array[$array_key] = $subarray;
        }
    }

    function array_filter_by_primarykey($primary_key, $criterion) {

    }

    function array_filter_mdarray($array, $primary_key, $criterion, $action) {
        $positive_condition = function($element) use ($primary_key, $criterion) {
            return ($element[$primary_key] == $criterion);
        };

        $negative_condition = function($element) use ($primary_key, $criterion) {
            return !($element[$primary_key] == $criterion);
        };

        if($action) {
            return array_filter($array, $positive_condition);
        } else {
            return array_filter($array, $negative_condition);
        }
    }

    

    function array_show_recursive($array, $continuation_condition) {
        $result = '';
        foreach($array as $element) {    
            if(!empty($element[$continuation_condition])) {

                $result .= '<li class="lower-menu-item"><a href="document.php?doc_id='. $element['id'] .'" style="font-size: 1.4em">'. $element['name'] .'</a><ul class="item-submenu" style="z-index:100;">';
                $result .= array_show_recursive($element['children'], $continuation_condition,);
                $result .= '</ul>';

            } else {
                $result .= '<li class="lower-menu-item"><a href="document.php?doc_id='. $element['id'] .'" style="font-size: 1.4em">'. $element['name'] .'</a></li></li>';
            }
        }

        return $result;
    }


    // -----------------------------------------------------classes-----------------------------------------------------

    class MDArray {

        public function __construct(&$ancestors, &$elements, $primary_key, $criterion) {
            $this->primary_key = $primary_key;

            $this->mdarray_create($ancestors, $elements, $primary_key, $criterion);
        }

        private function mdarray_create(&$ancestors, &$elements, $primary_key, $criterion) {

            $key = $primary_key;
            $params = ['primary_key' => $primary_key, 'criterion' => $criterion];

            function child_integration(&$ancestors, &$elements, $params) {
                $primary_key = $params['primary_key'];
                $criterion   = $params['criterion'];  
                $next_turn_ancestors = array();   
                
                if(!empty($elements)) {
                    foreach($elements as $key => &$element) {
                        foreach($ancestors as &$ancestor) {
                            if($element[$primary_key] == $ancestor['id']) {
                                if(array_key_exists('children', $ancestor)) {
                                    array_push($ancestor['children'], $element);
                                    $next_turn_ancestors[length($next_turn_ancestors)] = &$ancestor['children'][length($ancestor['children'])];

                                } else {
                                    $ancestor['children'][0] = $element;
                                    $next_turn_ancestors[length($next_turn_ancestors)] = &$ancestor['children'][0];
                                }

                                unset($elements[$key]);
                                break;
                            }
                        }
                    }
                    
                    child_integration($next_turn_ancestors, $elements, $params);  
                }       
            }

            child_integration($ancestors, $elements, $params);

            return $ancestors;

        }
    }

        // -----------------------------------------------------connection----------------------------------------------


    class Connection {

        public function setConnection() {
            try {
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                if(!isset($_SESSION)) {
                    session_start();
                }

            } catch(PDOException $e) {
                die();
            }
        }


        // --- construct

        public function __construct($server, $db, $user, $pass) {
            $server = 'mysql:host=' . $server;

            if($db) {
                $db = ';dbname=' . $db;
                $this->conn = new PDO($server . $db, $user, $pass);
            } else {
                $this->conn = new PDO($server, $user, $pass);
            }
        }
    }


    // -----------------------------------------------------classes-----------------------------------------------------

        // -----------------------------------------------------files---------------------------------------------------

    // TODO: Make moving files from local storage possible

    class File {

        public function validateFile($ext, $formats) {

            // --- undefined/ multiply files/ corruption attack                             

            if (
                !isset($this->files['error']) ||
                is_array($this->files['error'])
            ) {
                throw new RuntimeException('Invalid parameters.');
            }

            // --- error array

            switch ($this->files['error']) {
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

            // --- format

            if($ext === FALSE) {
                 throw new RuntimeException('Unexpected file format, expect: ' . arr_to_string($formats) . '; Obtain: ' . $ext);
            }

        }


        public function moveFile() {

            try {
                $this->validateFile($this->ext, $this->formats);

                if(!move_uploaded_file($this->files['tmp_name'], $this->path)) {
                    throw new RuntimeException('RunTime Error');
                }

            } catch(RuntimeException $e) {
                echo $e->getMessage();

                $this->file_name = undefinded;
            }
        }


        // --- construct

        public function __construct($files_arr, $uploaddir, $format) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);

            $this->files = $files_arr;
            $this->formats = $format;
            $this->ext = array_search($finfo->file($this->files['tmp_name']), $this->formats, true);
            $this->file_name = name_generate(5, 20) . '.' . $this->ext;
            $this->path = $uploaddir . $this->file_name;
        } 
    }


    class Request {

        public function insert($sql, $values) {
            foreach($values as &$value) {
                trim($value);
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($values);                                  
        }

        public function select($sql, $values) {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($values);

            $result = array();

            while($row = $stmt->fetch()) {
                array_push($result, $row);
            }

            return $result;
        }

        public function __construct($conn) {
            $this->conn = $conn;
        }
    }


?>