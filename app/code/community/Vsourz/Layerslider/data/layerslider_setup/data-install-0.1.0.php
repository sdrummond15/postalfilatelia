<?php
$slider = array(
	array(
		'slide_title' => 'Slide 1',
		'slide_url' => '',
		'slide_img' => 'layerslider/banner1-slide-img-2015-04-10-08-43-43.jpg',
		'slide_captionimg1' => 'layerslider/banner1_thumb_1-slide_captionimg1-img-2015-04-10-08-43-43.png',
		'slide_captionimg2' => 'layerslider/banner1_thumb_2-slide_captionimg2-2015-04-10-08-43-43.png',
		'slide_caption1' => 'New York-Style Pizza',
		'slide_caption2' => 'We are happy to serve you',
		'slide_caption3' => 'Deep Dish Pizza',
		'slide_caption4' => 'Summer Shack. Food is Love',
		'slide_caption5' => 'Gourmet Pizza',
		'status' => '1'
	),
	array(
		'slide_title' => 'Slide 2',
		'slide_url' => '',
		'slide_img' => 'layerslider/banner2-slide-img-2015-04-10-08-48-49.jpg',
		'slide_captionimg1' => 'layerslider/banner2_thumb_1-slide_captionimg1-img-2015-04-10-08-48-49.png',
		'slide_captionimg2' => 'layerslider/banner2_thumb_2-slide_captionimg2-2015-04-10-08-48-49.png',
		'slide_caption1' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit',
		'slide_caption2' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit',
		'slide_caption3' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit',
		'slide_caption4' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit',
		'status' => '1'
	),
	array(
		'slide_title' => 'Slide 3',
		'slide_url' => '',
		'slide_img' => 'layerslider/banner3-slide-img-2015-04-10-08-49-49.jpg',
		'slide_captionimg1' => 'layerslider/banner3_thumb_1-slide_captionimg1-img-2015-04-10-08-50-08.png',
		'slide_captionimg2' => 'layerslider/banner3_thumb_2-slide_captionimg2-2015-04-10-08-50-08.png',
		'slide_caption1' => 'juice',
		'status' => '1'
	)
);
foreach ($slider as $slides){
	Mage::getModel('layerslider/layerslider')->setData($slides)->save();
}

