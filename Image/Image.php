<?php

/**
 * Class to manage image.
 */
class FS_Image
{
    const RATIO_LOWER = 0;
    const RATIO_HIGHER = 1;
    
    const FORMAT_JPG = 'jpeg';
    const FORMAT_PNG = 'png';
    const FORMAT_GIF = 'gif';
    const FORMAT_BMP = 'bmp';
    
    private $_image;
    private $_path;
    private $_type;
    
    private $_width;
    private $_height;
    
    private $_toWidth;
    private $_toHeight;
    
    private $_ratio;
    private $_keepRatio;
    private $_allowUpscale;
    
    private $_red;
    private $_green;
    private $_blue;
    
    /**
     * Constructor
     * @param string $path  Path of image
     */
    public function __construct($pPath)
    {
        $this->_path = $pPath;
        /*
        $this->_image = self::Open($pPath, $this->_type);
        if ($this->_image) {
            FS_Exception::Launch("Could not open image: $pPath is not a valid image file.");
        }
         */
        
        $this->_ratio = self::RATIO_LOWER;
        $this->_keepRatio = TRUE;
        $this->_allowUpscale = TRUE;
        
        $this->_red = $this->_green = $this->_blue = 255;
    }
    
    public function __destruct()
    {
        $this->Destroy();
    }
    
    /**
     * Get image's width
     * @param Boolean $SourceWidth  If TRUE, return source's width.
     * @return int
     */
    public function GetWidth($pSourceWidth = TRUE)
    {
        if ($pSourceWidth === FALSE && !is_null($this->_toWidth)) {
            return $this->_toWidth;
        }
        if (is_null($this->_width)) {
            /*
            $this->_width = imagesx($this->_image);
            if (is_null($this->_toWidth)) {
                $this->_toWidth = $this->_width;
            }
             */
            $lSize = getimagesize($this->_path);
            if ($lSize === FALSE) {
                FS_Exception::Launch("Could not open image: $this->_path is not a valid image file.");
            }
            $this->_width = $lSize[0];
        }
        return $this->_width;
    }
    
    /**
     * Set new width
     * @param int $newHeight New height value
     * @return \FS_Image
     */
    public function SetWidth($pNewWidth)
    {
        $this->_toWidth = $pNewWidth;
        return $this;
    }

    /**
     * Get image's height
     * @param Boolean $sourceHeight If TRUE, return source's height.
     * @return int
     */
    public function GetHeight($pSourceHeight = TRUE)
    {
        if ($pSourceHeight === FALSE && !is_null($this->_toHeight)) {
            return $this->_toHeight;
        }
        if (is_null($this->_height)) {
            /*
            $this->_height = imagesy($this->_image);
            if (is_null($this->_toHeight)) {
                $this->_toHeight = $this->_height;
            }*/
            $lSize = getimagesize($this->_path);
            if ($lSize === FALSE) {
                FS_Exception::Launch("Could not open image: $this->_path is not a valid image file.");
            }
            $this->_height = $lSize[1];
        }
        return $this->_height;
    }
    
    /**
     * Set new height
     * @param int $newHeight New height value
     * @return \FS_Image
     */
    public function SetHeight($pNewHeight)
    {
        $this->_toHeight = $pNewHeight;
        return $this;
    }
    
    /**
     * Set ratio mode
     * @param string $ratio Ratio mode
     * @return \FS_Image
     */
    public function SetRatio($pRatio = self::RATIO_LOWER)
    {
        $this->_ratio = $pRatio;
        return $this;
    }
    
    /**
     * Get ratio mode
     * @return string
     */
    public function GetRatio()
    {
        return $this->_ratio;
    }
    
    /**
     * Set keep ratio mode
     * @param Boolean $keepRatio    TRUE if keep ratio
     * @return \FS_Image
     */
    public function SetKeepRatio($pKeepRatio)
    {
        $this->_keepRatio = $pKeepRatio;
        return $this;
    }
    
    /**
     * Get keep ratio mode
     * @return Boolean
     */
    public function GetKeepRatio()
    {
        return $this->_keepRatio;
    }
    
    /**
     * Set allow upscale
     * @param Boolean $allowUpscale If TRUE, allowed upscale
     * @return \FS_Image
     */
    public function SetAllowUpscale($pAllowUpscale)
    {
        $this->_allowUpscale = $pAllowUpscale;
        return $this;
    }
    
    /**
     * Get if upscale is allowed
     * @return Boolean
     */
    public function GetAllowUpscale()
    {
        return $this->_allowUpscale;
    }
    
    /**
     * Get ratio coef
     * @return stdClass
     */
    public function GetRatioCoef()
    {
        $lSrcWidth = $this->GetWidth();
        $lSrcHeight = $this->GetHeight();
        
        $lDestWidth = $this->GetWidth(FALSE);
        $lDestHeight = $this->GetHeight(FALSE);

        if ($this->_allowUpscale === FALSE) {
            if ($lSrcWidth < $lDestWidth)
                $lDestWidth = $lSrcWidth;
            if ($lSrcHeight < $lDestHeight)
                $lDestHeight = $lSrcHeight;
        }

        $lCoefX = $lDestWidth / $lSrcWidth;
        $lCoefY = $lDestHeight / $lSrcHeight;
        
        if ($this->_keepRatio === TRUE) {
            if ($this->_ratio === self::RATIO_LOWER) {
                $lCoefX = $lCoefY = min(array($lCoefX, $lCoefY));
            } else if ($this->_ratio === self::RATIO_HIGHER) {
                $lCoefX = $lCoefY = max(array($lCoefX, $lCoefY));
            } else {
                FS_Exception::Launch('Unknown $ratio parameter. Must be FS_Image::RATIO_LOWER or FS_Image::RATIO_HIGHER.');
            }
        }
        
        return (object)array('x' => $lCoefX, 'y' => $lCoefY);
    }
    
    /**
     * Get size with ratio
     * @return stdClass
     */
    public function GetRatioSize()
    {
        $lCoef = $this->GetRatioCoef();
        $lSrcWidth = $this->GetWidth();
        $lSrcHeight = $this->GetHeight();

        return (object)array('width'=>$lSrcWidth * $lCoef->x, 'height' => $lSrcHeight * $lCoef->y);
    }
    
    /**
     * Set background canal red
     * @param int $red    0-255 red value
     * @return \FS_Image
     */
    public function SetR($pRed)
    {
        $this->_red = $pRed;
        return $this;
    }
    
    /**
     * Get background canal red
     * @return int
     */
    public function GetR()
    {
        return $this->_red;
    }
    
    /**
     * Set background canal green
     * @param int $green    0-255 green value
     * @return \FS_Image
     */
    public function SetG($pGreen)
    {
        $this->_green = $pGreen;
        return $this;
    }
    
    /**
     * Get background canal green
     * @return int
     */
    public function GetG()
    {
        return $this->_green;
    }
    
    /**
     * Set background canal blue
     * @param int $blue    0-255 blue value
     * @return \FS_Image
     */
    public function SetB($pBlue)
    {
        $this->_blue = $pBlue;
        return $this;
    }
    
    /**
     * Get background canal blue
     * @return int
     */
    public function GetB()
    {
        return $this->_blue;
    }
    
    /**
     * Set RGB background
     * @param int|array|stdClass $red     Red canal
     * @param int $green   Green canal
     * @param int $blue    Blue canal
     * @return \FS_Image
     */
    public function SetRGB($pRed = 255, $pGreen = 255, $pBlue = 255)
    {
        if (is_array($pRed) || $pRed instanceof stdClass) {
            if (is_array($pRed)) {
                $lColor = (object)$pRed;
            } else {
                $lColor = $pRed;
            }
            if (isset($lColor->r)) {
                $pRed = $lColor->r;
            } else if (isset($lColor->red)) {
                $pRed = $lColor->red;
            }
            if (isset($lColor->g)) {
                $pGreen = $lColor->g;
            } else if (isset($lColor->green)) {
                $pGreen = $lColor->green;
            }
            if (isset($lColor->b)) {
                $pBlue = $lColor->b;
            } else if (isset($lColor->blue)) {
                $pBlue = $lColor->blue;
            }
        }
        return $this;
    }
    
    /**
     * Render to file
     * @param string $path  Path to render file
     * @return \FS_Image
     */
    public function RenderToFile($pPath)
    {
        if (is_null($this->_image)) {
            $this->_image = self::Open($this->_path, $this->_type);
        }
        
        $lSize = $this->GetRatioSize();

        if ($this->_type === self::FORMAT_JPG) {
            $this->saveToJpeg($pPath, $lSize->width, $lSize->height);
        } else {
            $this->saveToJpeg($pPath, $lSize->width, $lSize->height);
        }
        
        return $this;
    }
    
    private function saveToJpeg($pPath, $pDestWidth, $pDestHeight)
    {
        // Create new image
        $lDest = imagecreatetruecolor($pDestWidth, $pDestHeight);
        // White code color... Why ??
        $lWhite = imagecolorallocate($lDest, $this->_red, $this->_green, $this->blue);
        // Fill image with white... Why ?? If I want a png alpha image ?...
        imagefilledrectangle($lDest, 0, 0, $pDestWidth, $pDestHeight, $lWhite);
        // Copy image and resize
        imagecopyresampled($lDest, $this->_image, 0, 0, 0, 0, $pDestWidth, $pDestHeight, $this->GetWidth(), $this->GetHeight());
        // Create final file... Why only jpeg ??
        imagejpeg($lDest, $pPath, 100);

        imagedestroy($lDest);
    }
    
    /**
     * Destroy and free resource
     * @return \FS_Image
     */
    public function Destroy()
    {
        if (is_null($this->_image)) {
            return $this;
        }
        imagedestroy($this->_image);
        $this->_image = NULL;
        return $this;
    }
    
    /**
     * Open an image
     * @param string $srcPath   Path of image
     * @param string &$format   Format of image
     * @return Resource image
     */
    private static function Open($pSrcPath, &$pFormat = self::FORMAT_GIF)
    {
        $lSrc = @imagecreatefromgif($pSrcPath);
        $pFormat = self::FORMAT_GIF;
        if (!$lSrc)
        {
            $lSrc = @imagecreatefrompng($pSrcPath);
            $pFormat = self::FORMAT_PNG;
            if (!$lSrc)
            {
                $lSrc = @self::imagecreatefrombmp($pSrcPath);
                $pFormat = self::FORMAT_BMP;
                if (!$lSrc)
                {
                    $lSrc = @imagecreatefromjpeg($pSrcPath);
                    $pFormat = self::FORMAT_JPG;
                    if (!$lSrc) {
                        $pFormat = NULL;
                        return NULL;
                    }
                }
            }
        }
        return $lSrc;
    }
    
    /**
     * Resize a picture
     * @param string    $srcPath       Image source path
     * @param string    $destPath      Image final path
     * @param int       $destWidth     Width wanted
     * @param int       $destHeight    Height wanted
     * @param Boolean   $keepRatio     If TRUE, keep ratio.
     * @param Boolean   $noUpscale     If TRUE, Resize only downscale if image is bigger. 
     * @param int       $ratio         If $keepRatio is TRUE, this method resize to the lower coeficient if RATIO_LOWER is passed, or to the higher coeficient if RATIO_HIGHER is passed.
     * @return boolean
     */
    public static function Resize($pSrcPath, $pDestPath, $pDestWidth, $pDestHeight, $pKeepRatio = TRUE, $pNoUpscale = FALSE, $pRatio = self::RATIO_LOWER)
    {
        $lSrc = self::Open($pSrcPath);
        if (is_null($lSrc)) {
            return FALSE;
        }

        // Recuperation des dimensions de la source
        $lSrcWidth = imagesx($lSrc);
        $lSrcHeight = imagesy($lSrc);

        if ($pNoUpscale) {
            if ($lSrcWidth < $pDestWidth)
                $pDestWidth = $lSrcWidth;
            if ($lSrcHeight < $pDestHeight)
                $pDestHeight = $lSrcHeight;
        }

        $lCoefX = $pDestWidth / $lSrcWidth;
        $lCoefY = $pDestHeight / $lSrcHeight;
        
        if ($pKeepRatio === TRUE) {
            if ($pRatio === self::RATIO_LOWER) {
                $lCoefX = $lCoefY = min(array($lCoefX, $lCoefY));
            } else if ($pRatio === self::RATIO_HIGHER) {
                $lCoefX = $lCoefY = max(array($lCoefX, $lCoefY));
            } else {
                FS_Exception::Launch('Unknown $ratio parameter. Must be FS_Image::RATIO_LOWER or FS_Image::RATIO_HIGHER.');
            }
        }
        
        $lDestWidth = $lSrcWidth * $lCoefX;
        $lDestHeight = $lSrcHeight * $lCoefY;
        
        // Create new image
        $lDest = imagecreatetruecolor($lDestWidth, $lDestHeight);
        // White code color... Why ??
        $lWhite = imagecolorallocate($lDest, 255, 255, 255);
        // Fill image with white... Why ?? If I want a png alpha image ?...
        imagefilledrectangle($lDest, 0, 0, $lDestWidth, $lDestHeight, $lWhite);
        // Copy image and resize
        imagecopyresampled($lDest, $lSrc, 0, 0, 0, 0, $lDestWidth, $lDestHeight, $lSrcWidth, $lSrcHeight);
        // Create final file... Why only jpeg ??
        imagejpeg($lDest, $pDestPath, 100);

        imagedestroy($lDest);
        imagedestroy($lSrc);

        return TRUE;
    }

    /**
     * Open a bmp file.
     * @param string $filename  Path of image.
     * @return boolean|image
     */
    static public function imagecreatefrombmp($filename)
    {
        //Ouverture du fichier en mode binaire
        if (! $f1 = fopen($filename,"rb")) return FALSE;

        //1 : Chargement des ent?tes FICHIER
        $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
        if ($FILE['file_type'] != 19778) return FALSE;

        //2 : Chargement des ent?tes BMP
        $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
                 '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
                 '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
        $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
        if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
        $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
        $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
        $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
        $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
        $BMP['decal'] = 4-(4*$BMP['decal']);
        if ($BMP['decal'] == 4) $BMP['decal'] = 0;

        //3 : Chargement des couleurs de la palette
        $PALETTE = array();
        if ($BMP['colors'] < 16777216)
        {
            $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
        }

        //4 : Cr?ation de l'image
        $IMG = fread($f1,$BMP['size_bitmap']);
        $VIDE = chr(0);

        $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
        $P = 0;
        $Y = $BMP['height']-1;
        while ($Y >= 0)
        {
            $X=0;
            while ($X < $BMP['width'])
            {
                if ($BMP['bits_per_pixel'] == 24)
                    $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
                elseif ($BMP['bits_per_pixel'] == 16)
                { 
                    $COLOR = unpack("n",substr($IMG,$P,2));
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                }
                elseif ($BMP['bits_per_pixel'] == 8)
                { 
                    $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                }
                elseif ($BMP['bits_per_pixel'] == 4)
                {
                    $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
                    if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                }
                elseif ($BMP['bits_per_pixel'] == 1)
                {
                    $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
                    if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
                    elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
                    elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
                    elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
                    elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
                    elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
                    elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
                    elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                }
                else
                    return FALSE;
                imagesetpixel($res,$X,$Y,$COLOR[1]);
                $X++;
                $P += $BMP['bytes_per_pixel'];
            }
            $Y--;
            $P+=$BMP['decal'];
        }

        //Fermeture du fichier
        fclose($f1);

        return $res;
    }

}