<?php

class Setup
{

    private $upload_dir;
    private $actual_dirname;

    function __construct(){
        $this->upload_dir = wp_upload_dir();
        $this->actual_dirname = $this->upload_dir['basedir'] . '/actual';

        $this->create_upload_dir();

    }

    private function create_upload_dir() {
        if (!file_exists($this->actual_dirname)) {
            wp_mkdir_p($this->actual_dirname);
        }
    } 

}
