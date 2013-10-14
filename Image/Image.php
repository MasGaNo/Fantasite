<?php

/**
 * Class to manage image.
 */
class FS_Image
{
    const RATIO_LOWER = 0;
    const RATIO_HIGHER = 1;
    
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
        $lSrc = @imagecreatefromgif($pSrcPath);
        if (!$lSrc)
        {
            $lSrc = @imagecreatefrompng($pSrcPath);
            if (!$lSrc)
            {
                $lSrc = @self::imagecreatefrombmp($pSrcPath);
                if (!$lSrc)
                {
                    $lSrc = @imagecreatefromjpeg($pSrcPath);
                    if (!$lSrc) {
                        return FALSE;
                    }
                }
            }
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
                FS_Exception::Launch('Unknown $ration parameter. Must be FS_Image::RATIO_LOWER or FS_Image::RATIO_HIGHER.');
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