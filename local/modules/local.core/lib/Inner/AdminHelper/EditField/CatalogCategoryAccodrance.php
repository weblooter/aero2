<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class CatalogCategoryAccodrance extends Base
{

    private $_sectionsTree = null;
    private $_marketTaxonomy = null;
    private $_marketTaxonomyFile = null;
    private $_marketTaxonomyFileSeparator = ' - '; // разделительно idCategory - nameCategory

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {

        global $APPLICATION;

        $arValues = $this->getValue();

        if ( !is_array( $arValues ) )
        {
            $arValues = [];
        }

        $return = "";
        foreach ( $this->_sectionsTree as $st )
        {

            ob_start();

            $currentValueId = $this->_marketTaxonomy[ $arValues[ $st[ 'ID' ] ] ][ 'ID' ];
            $currentValueName = $this->_marketTaxonomy[ $arValues[ $st[ 'ID' ] ] ][ 'NAME' ];
            $currentValue = $currentValueName.' ['.$currentValueId.']';

            $APPLICATION->IncludeComponent(
                'bitrix:main.lookup.input',
                'iblockedit',
                array(
                    'CONTROL_ID' => $this->getCode().'_'.mt_rand( 0, 10000 ),
                    'INPUT_NAME' => $this->getCode().'['.$st[ 'ID' ].']',
                    'INPUT_NAME_STRING' => 'text_area_'.$this->getCode().'['.$st[ 'ID' ].']',
                    'INPUT_VALUE_STRING' => $currentValue,
                    'START_TEXT' => "Начните вводить текст",
                    'SEARCH_DATA_FILE' => $this->_marketTaxonomyFile,
                    'SEARCH_DATA_FILE_SEPARATOR' => $this->_marketTaxonomyFileSeparator,
                    'MULTIPLE' => 'N',
                    'WITHOUT_IBLOCK' => 'Y',
                    'FILTER' => 'Y',
                    'TYPE' => 'SECTION',
                ), null, array('HIDE_ICONS' => 'Y')
            );

            $input = ob_get_contents();
            ob_end_clean();

            $padding = str_repeat( '&nbsp;&nbsp;&nbsp;', $st[ 'DEPTH_LEVEL' ] );
            $return .= "<tr>
                    <td width='30%' style='text-align:left;'>
                        {$padding} {$st['NAME']} [{$st['ID']}]
                    </td>
                    <td>
                        {$input}
                    </td>
                </tr>";

        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        return htmlspecialcharsbx( $this->getValue() );
    }

    /**
     * Возвращает полный html строки поля
     *
     * @return string
     */
    public function getRowHtml()
    {

        $this->readMarketTaxonomy();
        if ( empty( $this->_marketTaxonomy ) )
        {
            throw new \Exception( "Не удалось прочитать файл с категоризацией товаров либо файл заполнен не корректно" );
        }

        if ( empty( $this->_sectionsTree ) )
        {
            throw new \Exception( "Не установленн массив с категориями каталога" );
        }

        if ( $this->isEditable === true )
        {
            return $this->getEditFieldHtml();
        }
        else
        {
            return $this->getViewFieldHtml();
        }
    }

    public function readMarketTaxonomy()
    {
        if ( !$this->_marketTaxonomyFile || !file_exists( $_SERVER[ 'DOCUMENT_ROOT' ].$this->_marketTaxonomyFile ) )
        {
            throw new \Exception( "Не верно задан файл с категоризацией товаров" );
        }

        $handle = fopen( $_SERVER[ 'DOCUMENT_ROOT' ].$this->_marketTaxonomyFile, "r" );
        if ( $handle )
        {

            $sep = $this->getMarketTaxonomyFileSeparator();

            while ( ( $buffer = fgets( $handle ) ) !== false )
            {

                $ar = explode( $sep, trim( $buffer ) );

                if ( 2 == count( $ar ) )
                {
                    $this->_marketTaxonomy[ $ar[ 0 ] ] = [
                        'ID' => $ar[ 0 ],
                        'NAME' => $ar[ 1 ],
                    ];
                }
            }

            if ( !feof( $handle ) )
            {
                throw new \Exception( "Не удалось прочитать файл с категоризацией товаров" );
            }

            fclose( $handle );

        }
        else
        {
            throw new \Exception( "Не удалось прочитать файл с категоризацией товаров" );
        }

        return $this;

    }

    /**
     * @return null
     */
    public function getMarketTaxonomy()
    {
        return $this->_marketTaxonomy;
    }

    /**
     * @return null|array
     */
    public function getSectionsTree()
    {
        return $this->_sectionsTree;
    }

    /**
     * @param null $sectionsTree
     */
    public function setSectionsTree( $sectionsTree )
    {
        $this->_sectionsTree = $sectionsTree;
        return $this;
    }

    /**
     * @return null
     */
    public function getMarketTaxonomyFile()
    {
        return $this->_marketTaxonomyFile;
    }

    /**
     * @param null $marketTaxonomyFile
     */
    public function setMarketTaxonomyFile( $marketTaxonomyFile )
    {
        $this->_marketTaxonomyFile = $marketTaxonomyFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getMarketTaxonomyFileSeparator(): string
    {
        return $this->_marketTaxonomyFileSeparator;
    }

    /**
     * @param string $marketTaxonomyFileSeparator
     */
    public function setMarketTaxonomyFileSeparator( string $marketTaxonomyFileSeparator )
    {
        $this->_marketTaxonomyFileSeparator = $marketTaxonomyFileSeparator;
        return $this;
    }


}
