<?php	
class Vsourz_layerslider_Model_Source_Slideeffects
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'random', 'label' => 'Random'),
			array('value' => '$Duration:1200,$Opacity:2', 'label' => 'Fade'),
			
			array('value' => '$Duration:500,$FlyDirection:4,$Easing:$JssorEasing$.$EaseInQuad', 'label' => 'Slide Down'),
			
			array('value' => '$Duration:500,$FlyDirection:8,$Easing:$JssorEasing$.$EaseInQuad', 'label' => 'Slide Up'),
			
			array('value' => '$Duration:400,$FlyDirection:1,$Easing:$JssorEasing$.$EaseInQuad', 'label' => 'Slide Right'),
			
			array('value' => '$Duration:400,$FlyDirection:2,$Easing:$JssorEasing$.$EaseInQuad', 'label' => 'Slide Left'),
			
			array('value' => '$Duration:1200,$FlyDirection:1,$Easing:{$Left:$JssorEasing$.$EaseInOutQuart,$Opacity:$JssorEasing$.$EaseLinear},$Opacity:2,$Brother:{$Duration:1200,$FlyDirection:2,$Easing:{$Left:$JssorEasing$.$EaseInOutQuart,$Opacity:$JssorEasing$.$EaseLinear},$Opacity:2}', 'label' => 'Shift LR'),
			
			array('value' => '$Duration:1200,$FlyDirection:4,$Easing:{$Top:$JssorEasing$.$EaseInOutQuart,$Opacity:$JssorEasing$.$EaseLinear},$Opacity:2,$Brother:{$Duration:1200,$FlyDirection:8,$Easing:{$Top:$JssorEasing$.$EaseInOutQuart,$Opacity:$JssorEasing$.$EaseLinear},$Opacity:2}', 'label' => 'Shift TB'),
		
			array('value' => '$Duration:1400,$Zoom:1.5,$FlyDirection:1,$Easing:{$Left:$JssorEasing$.$EaseInWave,$Zoom:$JssorEasing$.$EaseInSine},$ScaleHorizontal:0.25,$Opacity:2,$ZIndex:-10,$Brother:{$Duration:1400,$Zoom:1.5,$FlyDirection:2,$Easing:{$Left:$JssorEasing$.$EaseInWave,$Zoom:$JssorEasing$.$EaseInSine},$ScaleHorizontal:0.25,$Opacity:2,$ZIndex:-10}', 'label' => 'Switch'),
			
			array('value' => '$Duration:1500,$Cols:2,$FlyDirection:1,$ChessMode:{$Column:3},$Easing:{$Left:$JssorEasing$.$EaseInOutCubic},$ScaleHorizontal:0.5,$Opacity:2,$Brother:{$Duration:1500,$Opacity:2}', 'label' => 'Doors'),
		
			array('value' =>	'$Duration:600,$Delay:50,$Cols:8,$Rows:4,$SlideOut:true,$FlyDirection:2,$Formation:$JssorSlideshowFormations$.$FormationZigZag,$Assembly:513,$Easing:{$Left:$JssorEasing$.$EaseInCubic,$Opacity:$JssorEasing$.$EaseOutQuad},$Opacity:2', 'label' => 'Float Right Zing Zag'),
		
			
			array('value' => '$Duration:1000,$Cols:12,$FlyDirection:8,$Formation:$JssorSlideshowFormations$.$FormationStraight,$ChessMode:{$Column:12}', 'label' => 'Vertical Chess Stripe'),
			
			
			array('value' => '$Duration:1200,$Cols:8,$Rows:4,$Clip:15,$During:{$Top:[0.5,0.5],$Clip:[0,0.5]},$FlyDirection:8,$Formation:$JssorSlideshowFormations$.$FormationStraight,$ChessMode:{$Column:12},$ScaleClip:0.5', 'label' => 'Clip & Ches In'),
					
			array('value' => '$Duration:1200,$Cols:8,$Rows:4,$Clip:15,$During:{$Top:[0.5,0.5],$Clip:[0,0.5]},$SlideOut:true,$FlyDirection:8,$Formation:$JssorSlideshowFormations$.$FormationStraight,$ChessMode:{$Column:12},$ScaleClip:0.5', 'label' => 'Clip & Ches Out'),
			
			array('value' => '$Duration:500,$Delay:50,$Cols:12,$FlyDirection:9,$Formation:$JssorSlideshowFormations$.$FormationStraightStairs,$Assembly:260,$Easing:{$Left:$JssorEasing$.$EaseLinear,$Top:$JssorEasing$.$EaseOutWave,$Opacity:$JssorEasing$.$EaseLinear},$ScaleHorizontal:0.2,$ScaleVertical:0.1,$Opacity:2,$Round:{$Top:2}', 'label' => 'Fluter Inside in Column'),
			
			array('value' => '$Duration:800,$Delay:30,$Cols:12,$SlideOut:true,$FlyDirection:2,$Formation:$JssorSlideshowFormations$.$FormationStraight,$Assembly:260,$Easing:{$Left:$JssorEasing$.$EaseInOutExpo,$Opacity:$JssorEasing$.$EaseInOutQuad},$ScaleHorizontal:0.2,$Opacity:2,$Outside:true,$Round:{$Top:0.5}', 'label' => 'Extrude Out Stripe'),
			
			
			array('value' => '$Duration:800,$Delay:20,$Cols:10,$FlyDirection:1,$Formation:$JssorSlideshowFormations$.$FormationStraight,$Easing:{$Left:$JssorEasing$.$EaseInOutQuart,$Opacity:$JssorEasing$.$EaseLinear},$Opacity:2,$ZIndex:-10,$Brother:{$Duration:1200,$Delay:40,$Cols:10,$FlyDirection:1,$Formation:$JssorSlideshowFormations$.$FormationStraight,$Easing:{$Top:$JssorEasing$.$EaseInOutQuart,$Opacity:$JssorEasing$.$EaseLinear},$Opacity:2,$ZIndex:-10,$Shift:-100}', 'label' => 'Return LR'),
			
			array('value' => '$Duration:1600,$Rows:2,$FlyDirection:1,$ChessMode:{$Row:3},$Easing:{$Left:$JssorEasing$.$EaseInOutQuart,$Opacity:$JssorEasing$.$EaseLinear},$Opacity:2,$Brother:{$Duration:1600,$Rows:2,$FlyDirection:2,$ChessMode:{$Row:3},$Easing:{$Left:$JssorEasing$.$EaseInOutQuart,$Opacity:$JssorEasing$.$EaseLinear},$Opacity:2}', 'label' => 'Chess Replace TB'),
			
			array('value' => '$Duration:1600,$Cols:2,$FlyDirection:8,$ChessMode:{$Column:12},$Easing:{$Top:$JssorEasing$.$EaseInOutQuart,$Opacity:$JssorEasing$.$EaseLinear},$Opacity:2,$Brother:{$Duration:1600,$Cols:2,$FlyDirection:4,$ChessMode:{$Column:12},$Easing:{$Top:$JssorEasing$.$EaseInOutQuart,$Opacity:$JssorEasing$.$EaseLinear},$Opacity:2}', 'label' => 'Chess Replace LR'),
			
			array('value' => '$Duration:1000,$Delay:30,$Cols:8,$Rows:4,$Clip:15,$SlideOut:true,$Formation:$JssorSlideshowFormations$.$FormationStraightStairs,$Assembly:2049,$Easing:$JssorEasing$.$EaseOutQuad', 'label' => 'Collapse Stairs'),
			
			array('value' => '$Duration:800,$Delay:300,$Cols:8,$Rows:4,$Clip:15,$SlideOut:true,$Formation:$JssorSlideshowFormations$.$FormationSquare,$Easing:$JssorEasing$.$EaseOutQuad', 'label' => 'Collapse Square'),
			
			array('value' => '$Duration:1000,$Delay:30,$Cols:8,$Rows:4,$Clip:15,$Formation:$JssorSlideshowFormations$.$FormationStraightStairs,$Assembly:2050,$Easing:$JssorEasing$.$EaseInQuad', 'label' => 'Expand Stairs'),
			
			array('value' => '$Duration:1000,$Rows:4,$Clip:4', 'label' => 'Horizontal Stripe'),
			
			array('value' => '$Duration:1000,$Cols:8,$Clip:1', 'label' => 'Vertical Stripe'),
			
			array('value' => '$Duration:600,$Delay:50,$Cols:8,$Rows:4,$FlyDirection:6,$Formation:$JssorSlideshowFormations$.$FormationSwirl,$Assembly:264,$Easing:{$Top:$JssorEasing$.$EaseInQuart,$Opacity:$JssorEasing$.$EaseLinear},$Opacity:2', 'label' => 'Parabola Swirl in'),
			
			array('value' => '$Duration:600,$Delay:30,$Cols:8,$Rows:4,$FlyDirection:6,$Formation:$JssorSlideshowFormations$.$FormationStraightStairs,$Easing:{$Left:$JssorEasing$.$EaseInQuart,$Top:$JssorEasing$.$EaseInQuart,$Opacity:$JssorEasing$.$EaseLinear},$Opacity:2', 'label' => 'Parabola Stairs in'),
			
			
		
		);
	}
}
