<?php	
class Vsourz_Layerslider_Model_Source_Autocenter
{
	public function toOptionArray()
	{
		return array(
			array('value' => '0', 'label' => 'No'),
			array('value' => '1', 'label' => 'Horizontal'),
			array('value' => '2', 'label' => 'Vertical'),
			array('value' => '3', 'label' => 'Both'),
		);
	}
}
