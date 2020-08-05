<?php
class Vsourz_Layerslider_Model_Source_Pauseonhover
{
	public function toOptionArray()
	{
		return array(
			array('value' => '0', 'label' => 'No Pause'),
			array('value' => '1', 'label' => 'Pause For Desktop'),
			array('value' => '2', 'label' => 'Pause For Touch Device'),
			array('value' => '3', 'label' => 'Pause for Both'),
		);
	}
}
