<?
class Image {
    
    var $uploaddir;
    var $quality = 80;
    var $ext;
    var $dst_r;
    var $img_r;
    var $img_w;
    var $img_h;
    var $output;
    var $data;
    var $datathumb;
    
    function setFile($src = null) {
        $this->ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
        if(is_file($src) && ($this->ext == "jpg" OR $this->ext == "jpeg")) {
            $this->img_r = ImageCreateFromJPEG($src);
        } elseif(is_file($src) && $this->ext == "png") {
            $this->img_r = ImageCreateFromPNG($src);      
        } elseif(is_file($src) && $this->ext == "gif") {
            $this->img_r = ImageCreateFromGIF($src);
        }
        $this->img_w = imagesx($this->img_r);
        $this->img_h = imagesy($this->img_r);
    }
    
    function resize($w = 100) {
        $h =  $this->img_h / ($this->img_w / $w);
        $this->dst_r = ImageCreateTrueColor($w, $h);
        imagecopyresampled($this->dst_r, $this->img_r, 0, 0, 0, 0, $w, $h, $this->img_w, $this->img_h);
        $this->img_r = $this->dst_r;
        $this->img_h = $h;
        $this->img_w = $w;
    }
    
    function createFile($output_filename = null) {
        if($this->ext == "jpg" OR $this->ext == "jpeg") {
            imageJPEG($this->dst_r, $this->uploaddir.$output_filename.'.'.$this->ext, $this->quality);
        } elseif($this->ext == "png") {
            imagePNG($this->dst_r, $this->uploaddir.$output_filename.'.'.$this->ext);
        } elseif($this->ext == "gif") {
            imageGIF($this->dst_r, $this->uploaddir.$output_filename.'.'.$this->ext);
        }
        $this->output = $this->uploaddir.$output_filename.'.'.$this->ext;
    }
    
    function setUploadDir($dirname) {
        $this->uploaddir = $dirname;
    }
    
    function flush() {
        $tempFile = $_FILES['Filedata']['tmp_name'];
        $targetPath = $_SERVER['DOCUMENT_ROOT'] . $_GET['folder'] . '/';
        $targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];                
        imagedestroy($this->dst_r);
        //sunlink($targetFile);
        //imagedestroy($this->img_r);                
    }
    
}
?>