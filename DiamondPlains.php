<?php

class DiamondPlains extends Plains {
    public function getWealthFactor () {
        return parent::getWealthFactor() + 2;
    }
}
class PollutedPlains extends Plains {
    public function getWealthFactor () {
        return parent::getWealthFactor() - 4;
    }
}
$tile = new PollutedPlains();
print $tile->getWealthFactor();