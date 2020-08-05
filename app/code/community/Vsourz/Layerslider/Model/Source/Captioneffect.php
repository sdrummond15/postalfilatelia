<?php	
class Vsourz_layerslider_Model_Source_Captioneffect
{
	public function toOptionArray()
	{
		return array(
			array('value' => '$FlyDirection:2,$Easing:{$Left:$JssorEasing$.$EaseInOutSine},$ScaleHorizontal:0.6,$Opacity:2', 'label' => 'Right'),
			
			array('value' => '$FlyDirection:1,$Easing:{$Left:$JssorEasing$.$EaseInOutSine},$ScaleHorizontal:0.6,$Opacity:2', 'label' => 'Left'),
			
			array('value' => '$FlyDirection:4,$Easing:{$Top:$JssorEasing$.$EaseInOutSine},$ScaleVertical:0.6,$Opacity:2', 'label' => 'Top'),
			
			array('value' => '$FlyDirection:8,$Easing:{$Top:$JssorEasing$.$EaseInOutSine},$ScaleVertical:0.6,$Opacity:2', 'label' => 'Bottom'),
			
			array('value' => '$Opacity:2', 'label' => 'Fade'),
			
			array('value' => '$Rotate:6.25,$FlyDirection:1,$Easing:$JssorEasing$.$EaseLinear,$ScaleHorizontal:0.5,$Opacity:2,$During:{$Left:[0,0.33],$Rotate:[0,0.33]},$Round:{$Rotate:0.25}', 'label' => 'Fade Left'),
			
			array('value' => '$Rotate:6.25,$FlyDirection:2,$Easing:$JssorEasing$.$EaseLinear,$ScaleHorizontal:0.5,$Opacity:2,$During:{$Left:[0,0.33],$Rotate:[0,0.33]},$Round:{$Rotate:0.25}', 'label' => 'Fade Right'),
			
			array('value' => '$Rotate:6.25,$FlyDirection:4,$Easing:$JssorEasing$.$EaseLinear,$ScaleHorizontal:0.5,$Opacity:2,$During:{$Left:[0,0.33],$Rotate:[0,0.33]},$Round:{$Rotate:0.25}', 'label' => 'Fade Top'),
			
			array('value' => '$Rotate:6.25,$FlyDirection:8,$Easing:$JssorEasing$.$EaseLinear,$ScaleVertical:0.5,$Opacity:2,$During:{$Top:[0,0.33],$Rotate:[0,0.33]},$Round:{$Rotate:0.25}', 'label' => 'Fade Bottom'),
			
			array('value' => '$Clip:3,$FlyDirection:8,$Easing:$JssorEasing$.$EaseInCubic,$ScaleVertical:0.6,$Opacity:2', 'label' => 'B*CLIP'),
			
			array('value' => '$Clip:3,$Easing:$JssorEasing$.$EaseInOutCubic,$ScaleHorizontal:0.8,$ScaleClip:0.8,$Opacity:2,$During:{$Left:[0.4,0.6],$Clip:[0,0.4],$Opacity:[0.4,0.6]}', 'label' => 'ListHC|L'),
			
			array('value' => '$Zoom:3,$Rotate:2,$Easing:{$Left:$JssorEasing$.$EaseInCubic,$Rotate:$JssorEasing$.$EaseInWave},$ScaleHorizontal:0.6,$Opacity:2', 'label' => 'L*IW'),
			
			array('value' => '$Zoom:2,$Rotate:0.25,$FlyDirection:1,$Easing:{$Left:$JssorEasing$.$EaseOutJump,$Zoom:$JssorEasing$.$EaseInCubic,$Opacity:$JssorEasing$.$EaseLinear,$Rotate:$JssorEasing$.$EaseOutWave},$ScaleHorizontal:0.5,$Opacity:2,$Round:{$Rotate:0.5}', 'label' => 'RTT*JDN1|L'),
			
			array('value' => '$Rotate:-0.05,$Easing:{$Top:$JssorEasing$.$EaseInOutSine},$ScaleHorizontal:0.6,$Opacity:2', 'label' => 'L*'),
			
			array('value' => '$Rotate:-1,$FlyDirection:5,$Easing:$JssorEasing$.$EaseLinear,$ScaleHorizontal:0.5,$ScaleVertical:0.5,$Opacity:2,$During:{$Left:[0.67,0.33],$Top:[0,0.33],$Rotate:[0,0.33]},$Round:{$Rotate:0.25}', 'label' => 'L-T*'),
			
			array('value' => '$Rotate:-1,$FlyDirection:10,$Easing:$JssorEasing$.$EaseLinear,$ScaleHorizontal:0.5,$ScaleVertical:0.5,$Opacity:2,$During:{$Left:[0.67,0.33],$Top:[0,0.33],$Rotate:[0,0.33]},$Round:{$Rotate:0.25}', 'label' => 'L-B*'),
			
			array('value' => '$Rotate:-1,$FlyDirection:5,$Easing:$JssorEasing$.$EaseLinear,$ScaleHorizontal:0.5,$ScaleVertical:0.5,$Opacity:2,$During:{$Left:[0,0.33],$Top:[0.67,0.33],$Rotate:[0,0.33]},$Round:{$Rotate:0.25}', 'label' => 'T-L*'),
			
			array('value' => '$Rotate:1,$FlyDirection:6,$Easing:$JssorEasing$.$EaseLinear,$ScaleHorizontal:0.5,$ScaleVertical:0.5,$Opacity:2,$During:{$Left:[0,0.33],$Top:[0.67,0.33],$Rotate:[0,0.33]},$Round:{$Rotate:0.25}', 'label' => 'T-R*'),
			
			array('value' => '$Rotate:-1,$FlyDirection:9,$Easing:$JssorEasing$.$EaseLinear,$ScaleHorizontal:0.5,$ScaleVertical:0.5,$Opacity:2,$During:{$Left:[0,0.33],$Top:[0.67,0.33],$Rotate:[0,0.33]},$Round:{$Rotate:0.25}', 'label' => 'B-L*'),
			
			array('value' => '$Rotate:-1,$FlyDirection:10,$Easing:$JssorEasing$.$EaseLinear,$ScaleHorizontal:0.5,$ScaleVertical:0.5,$Opacity:2,$During:{$Left:[0,0.33],$Top:[0.67,0.33],$Rotate:[0,0.33]},$Round:{$Rotate:0.25}', 'label' => 'B-R*'),
			
			array('value' => '$Rotate:-1,$FlyDirection:6,$Easing:$JssorEasing$.$EaseLinear,$ScaleHorizontal:0.5,$ScaleVertical:0.5,$Opacity:2,$During:{$Left:[0.67,0.33],$Top:[0,0.33],$Rotate:[0,0.33]},$Round:{$Rotate:0.25}', 'label' => 'R-T*'),
			
			array('value' => '$Rotate:-1,$FlyDirection:10,$Easing:$JssorEasing$.$EaseLinear,$ScaleHorizontal:0.5,$ScaleVertical:0.5,$Opacity:2,$During:{$Left:[0.67,0.33],$Top:[0,0.33],$Rotate:[0,0.33]},$Round:{$Rotate:0.25}', 'label' => 'R-B*'),
			
			array('value' => '$Zoom:1,$Easing:$JssorEasing$.$EaseInCubic,$Opacity:2', 'label' => 'Zoom'),
			
			array('value' => '$Zoom:11,$FlyDirection:10,$Easing:{$Left:$JssorEasing$.$EaseOutJump,$Top:$JssorEasing$.$EaseInSine,$Zoom:$JssorEasing$.$EaseInCubic},$ScaleHorizontal:0.6,$ScaleVertical:0.6,$Opacity:2,$Round:{$Left:0.5}', 'label' => 'Zoom With Wave'),
			
			array('value' => '$Rotate:1,$Easing:{$Opacity:$JssorEasing$.$EaseLinear,$Rotate:$JssorEasing$.$EaseInQuad},$Opacity:2', 'label' => 'Rotate 360'),
			
			array('value' => '$Zoom:11,$Rotate:0.2,$FlyDirection:6,$Easing:{$Left:$JssorEasing$.$EaseLinear,$Top:$JssorEasing$.$EaseInCubic,$Zoom:$JssorEasing$.$EaseInCubic},$ScaleHorizontal:0.8,$ScaleVertical:0.5,$Opacity:2,$During:{$Top:[0,0.5]}', 'label' => 'Rotate Right Top'),
			
			array('value' => '$Clip:8,$Move:true,$Easing:{$Clip:$JssorEasing$.$EaseInOutCubic}', 'label' => 'Bottom To Top'),
			
			array('value' => '$Zoom:1,$FlyDirection:5,$Easing:{$Left:$JssorEasing$.$EaseInOutSine,$Top:$JssorEasing$.$EaseInWave,$Zoom:$JssorEasing$.$EaseInOutQuad},$ScaleHorizontal:0.5,$ScaleVertical:0.3,$Opacity:2,$During:{$Left:[0,0.7],$Top:[0.1,0.7]},$Round:{$Top:0.4}', 'label' => 'Latency'),
			
			array('value' => '$Zoom:1,$FlyDirection:8,$Easing:{$Top:$JssorEasing$.$EaseOutWave,$Zoom:$JssorEasing$.$EaseOutCubic},$ScaleVertical:0.2,$Opacity:2,$During:{$Top:[0,0.7]},$Round:{$Top:1.3}', 'label' => 'Tortuous | HR'),
			
			array('value' => '$Zoom:3,$Rotate:0.1,$FlyDirection:6,$Easing:{$Left:$JssorEasing$.$EaseInQuint,$Top:$JssorEasing$.$EaseInWave,$Opacity:$JssorEasing$.$EaseInQuint},$ScaleHorizontal:1,$ScaleVertical:0.1,$Opacity:2', 'label' => 'Spaceship | RT'),
			
			array('value' => '$Zoom:1,$FlyDirection:9,$Easing:{$Left:$JssorEasing$.$EaseOutWave,$Top:$JssorEasing$.$EaseInExpo},$ScaleHorizontal:0.1,$ScaleVertical:0.5,$Opacity:2,$During:{$Left:[0.3,0.7],$Top:[0,0.7]},$Round:{$Left:1.3}', 'label' => 'Attack | BL'),
			

			array('value' => '$FlyDirection:10,$Easing:{$Left:$JssorEasing$.$EaseLinear,$Top:$JssorEasing$.$EaseInWave},$ScaleHorizontal:0.6,$ScaleVertical:0.3,$Opacity:2,$Round:{$Top:2.5}', 'label' => 'Wave R|R'),
			
			array('value' => '$FlyDirection:10,$Easing:{$Left:$JssorEasing$.$EaseInJump,$Top:$JssorEasing$.$EaseLinear},$ScaleHorizontal:0.3,$ScaleVertical:0.6,$Opacity:2,$Round:{$Left:1.5}', 'label' => 'Jump'),
			
			array('value' => '$Clip:3,$Opacity:1.7,$During:{$Clip:[0.5,0.5],$Opacity:[0,0.5]}', 'label' => 'Clip LR|Fade'),
			
			array('value' => '$Rotate:-1,$FlyDirection:10,$Easing:$JssorEasing$.$EaseLinear,$ScaleHorizontal:0.5,$ScaleVertical:0.5,$Opacity:2,$During:{$Left:[0.67,0.33],$Top:[0,0.33],$Rotate:[0,0.33]},$Round:{$Rotate:0.25}', 'label' => 'Move OUT R|B')			
		);
	}
}
