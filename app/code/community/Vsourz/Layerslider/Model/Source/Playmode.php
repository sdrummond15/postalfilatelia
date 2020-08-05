<?php
class Vsourz_Layerslider_Model_Source_Playmode
{
	public function toOptionArray()
	{
		return array(
			array('value' => '0', 'label' => 'No Play'),
			array('value' => '1', 'label' => 'Chain'),
			array('value' => '3', 'label' => 'Chain Flatten'),
		);
	}
}
