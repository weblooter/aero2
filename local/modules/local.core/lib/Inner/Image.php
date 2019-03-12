<?php

namespace Local\Core\Inner;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages( __FILE__ );

/**
 * Class Image
 * @package Local\Core\Inner
 */
class Image extends File
{
    /**
     * Отресайзить картинку и получить ее объект
     *
     * @param     $width
     * @param     $height
     * @param int $options
     *
     * @return Image
     */
    public function getResizeImage( $width, $height, $options = BX_RESIZE_IMAGE_PROPORTIONAL_ALT )
    {
        $width = (int)$width;
        $height = (int)$height;

        $ar_fields = $this->getFieldValues();

        $ar_picture = \CFile::ResizeImageGet( $ar_fields, [
            'width' => $width,
            'height' => $height,
        ], $options, true );

        $ar_fields[ 'FILENAME' ] = basename( $ar_picture[ 'src' ] );
        $ar_fields[ 'SUBDIR' ] = trim( str_replace( '/'.$this->uploadDir.'/', '', dirname( $ar_picture[ 'src' ] ) ),
            '/' );

        return new self( $ar_fields );
    }

    /**
     * Получить html-код картинки
     *
     * @param array $params
     *
     * @return string
     */
    public function getImgCode( array $params = [] )
    {
        $class = $params[ 'class' ];
        $style = $params[ 'style' ];
        $alt = $params[ 'alt' ];
        $title = $params[ 'title' ];
        $width = (int)$params[ 'width' ];
        $height = (int)$params[ 'height' ];

        if ( $width || $height )
        {
            $src = $this->getResizeImage( $width, $height )->getSrc();
        }

        return '<img src="'.$src.'" '.( $class ? 'class="'.$class.'"' : '' ).' '.( $style ? 'style="'.$style.'"' : '' ).' '.( $alt ? 'alt="'.$alt.'"' : '' ).' '.( $title ? 'title="'.$title.'"' : '' ).' />';
    }
}
