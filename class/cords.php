<?php

class Cords
{

    private $_cords = array();
    private $_coordWidth;
    private $_coordHeight;

    function createMap($width, $height) {
        $this->_coordWidth = $width / 10;
        $this->_coordHeight = $height / 10;

        $widthArr = array_flip(range(1, $width));
        $heightArr = array_flip(range(1, $height));

        for ($start = 1; $start <= count($widthArr); $start++) {
            $widthArr[$start] = '';
        }
        for ($start = 1; $start <= count($heightArr); $start++) {
            $this->_cords[$start] = $widthArr;
        }
    }

    public function __construct($xw, $yw) {
        $this->createMap($xw, $yw);
    }

    public function render() {
        include 'template/h.php';
        foreach ($this->renderField() as $e) {
            echo $e;
        }
        include 'template/f.php';
    }

    /**
     *
     * @param type $pixelWidth
     * @param type $pixelHeight
     * @param type $imageName
     * @return boolean
     */
    function cords($pixelWidth, $pixelHeight, $imageName) {

        $possibleStartWidth = null; //Start ID will be set to $curWidth if the first ID is empty and null if the id is taken

        $curHeight = 1; //Which width and height are currently in the memory
        $curWidth = 1; // -||- see above
        //Loop through the coordinates
        foreach ($this->_cords as $ID) {

            //Set the possibleStartWidth when looping through new height
            $possibleStartWidth = null;
            //When the height is switch, the width is automatically set to 1 to prevent overflow
            $curWidth = 1;


            //Loop through the width of each height
            foreach ($ID as $i) {


                //The [X,Y] is empty
                if (empty($i)) {
                    //Set the possible Start Width
                    if (empty($possibleStartWidth)) {
                        $possibleStartWidth = $curWidth;
                    }


                    if (($curWidth - $possibleStartWidth + 1) == $pixelWidth / 10) {
                        if ($this->checkHeight($possibleStartWidth, $curWidth, $pixelHeight / 10, $curHeight, $imageName)) {
                            //The cord has been successfully added!
                            return false;
                        } else {
                            //Does not fit because one of the height ID for the width is taken, #Reset
                            $possibleStartWidth = null;
                            //  $curWidth = 1;
                        }
                    }
                } else {
                    //The width ID is already taken..#Reset
                    $possibleStartWidth = null;
                    //  $curWidth = 1;
                }

                $curWidth += 1;
            }
            $curHeight += 1;
        }
    }

    /**
     *
     * @param type $wFrom
     * @param type $wTo
     * @param type $setHeight
     * @param type $height
     * @param type $imageName
     * @param type $pixelWidth
     * @param type $pixelHeight
     * @return boolean
     */
    function checkHeight($wFrom, $wTo, $setHeight, $height, $imageName) {
        $width = range($wFrom, $wTo);
        $height = range($height, ($setHeight + $height - 1));

        //IF the maximum height value is greater than the maximum cord height ,#skip
        if (max($height) > $this->_coordHeight) {
            echo $imageName, ' does not fit!';
            return false;
        }

        foreach ($width as $w) {
            //w => the width to check if the height value is availalble;
            foreach ($height as $h) {
                if (!empty($this->_cords[$h][$w])) {
                    return false;
                }
            }
        }
        //Passed all tests, all cords should be available
        $this->addCords(min($width), max($width), min($height), max($height), $imageName);
        return true;
    }

    /**
     *
     * @param type $wFrom
     * @param type $wTo
     * @param type $hFrom
     * @param type $hTo
     * @param type $imageName
     * @param type $pixelWidth
     * @param type $pixelHeight
     */
    function addCords($wFrom, $wTo, $hFrom, $hTo, $imageName) {
        $width = range($wFrom, $wTo);
        $height = range($hFrom, (($hTo - $hFrom) + $hFrom));


        $maxwidth = max(array_keys($this->_cords));

        $curWidth = 0;
        $one = 0;

        foreach ($width as $w) {
            //w => the width to check if the hight value is availalble;
            foreach ($height as $h) {

                if ($curWidth == $maxwidth) {
                    $curWidth = 0;
                    //Max width...
                }
                if ($one === 0) {
                    $this->_cords[$h][$w] = array(
                        'name' => $imageName,
                        'size-w' => $this->_coordWidth,
                        'size-h' => $this->_coordHeight,
                    );
                } else {
                    $this->_cords[$h][$w] = array(
                        'name' => 'null',
                    );
                }

                $one += 1;
                $curWidth += 1;
            }
        }
    }

    function renderField() {
        $item = 0;
        $height = 0;
        $render[] = '<div id="map" style="width:' . ($this->_coordWidth * 10) . 'px;height:' . ($this->_coordHeight * 10) . 'px;">';

        foreach ($this->_cords as $h) {

            foreach ($h as $w) {
                if ($item == $this->_coordWidth) {
                    $item = 0;
                    $height += 1;
                }
                if (!empty($w['name'])) {

                    if ($w['name'] != 'null') {
                        //Set image
                        $render[] = '<div class="taken"  style="
     left:' . ($item * 10) . 'px;top:' . ($height * 10) . 'px;
     background-image:url(' . $w['name'] . ');
     height:' . ($w['size-h'] * 10) . 'px;
     width:' . ($w['size-w'] * 10) . 'px;"></div>';
                    }
                }
                $item += 1;
            }
        }


        return $render;
    }

}