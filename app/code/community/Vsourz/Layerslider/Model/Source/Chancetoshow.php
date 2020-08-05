<?php	
class Vsourz_Layerslider_Model_Source_Chancetoshow
{
	public function toOptionArray()
	{
		return array(
			array('value' => '0', 'label' => 'Never'),
			array('value' => '1', 'label' => 'On Mouse Over'),
			array('value' => '2', 'label' => 'Always'),
		);
	}
}
