<?php

	// $Header: /www/cvsroot/php2go/examples/image.example.php,v 1.3 2005/11/08 20:56:25 mpont Exp $
	// $Revision: 1.3 $
	// $Date: 2005/11/08 20:56:25 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

    require_once('config.example.php');
    import('php2go.graph.Image');
    
	/**
	 * You can create an image using one of the 2 static methods:
	 * create($width, $height) - creates an empty image based on values of width and height
	 * loadFromFile($file, $type) - creates a image from a existent file, given its path and type
	 */
    $Img =& Image::create(300, 300);
	
	/**
	 * Example of fill operation
	 * IMPORTANT: Each time the drawing color must be changed, you must change the active color
	 * in the image class. The setColor accepts hexadecimal color identifiers, or arrays
	 * containing values of the RGB components: array($red, $green, $blue)
	 */
    $Img->setColor('#ffffff');
	$Img->fill(0, 0);
	
	/**
	 * Example of drawing a simple rectangle using black color
	 * In this example, this rectangle will be the border of the final image
	 */
	$Img->setColor('#000000');
	$Img->draw(new ImageRectangle(0, 0, $Img->getWidth()-1, $Img->getHeight()-1));
	
	/**
	 * Draw a simple line, using random color
	 * Using the randomColor method, you specify the lower and upper bounds that must 
	 * be used to define the values of the RGB components
	 */
	$Img->randomColor(128, 224);
	$Img->draw(new ImageLine(0, 150, $Img->getWidth(), $Img->getHeight()));
	
	/**
	 * Example of other types of shapes: polygon, arc, circle
	 */
	$Img->randomColor(0, 255);
	$Img->draw(new ImagePolygon(array(0, 225, 75, 150, 0, 75), TRUE));
	$Img->randomColor(0, 255);	
	$Img->draw(new ImageArc(150, 225, 100, 100, 0, 180, IMG_ARC_EDGED, 5, '#000000'));
	$Img->setColor('#ff0000');
	$Img->draw(new ImageCircle(150, 150, 60, IMG_ARC_EDGED, 5, '#000000'));
	
	/** 
	 * Copy and resize an external image into the new image
	 */
	$Img->copyResized(Image::loadFromFile('resources/p2g_logo1.png', IMAGETYPE_PNG), 10, 10, 0, 0, 77, 40);
	
	/**
	 * Flip, rotate and resize examples
	 * These methods return a new Image object
	 */
	$Flipped = $Img->flip(IMAGEFLIP_HORIZONTAL);
	$Rotated = $Flipped->rotate(180, '#ffffff');
	$Resized = $Rotated->resize(250, 250);
	 	
	/**
	 * Example of drawing text using GD internal fonts or true type fonts
	 */
	$Resized->setColor('#000000');
	$Resized->draw(new ImageText('PHP2Go Example', 120, 215, GDFONT_3));
	if (ImageUtils::ttfSupported())
		$Resized->draw(new ImageTTFText('php2go.graph.Image', 120, 240, 'arial', 10, 0));
	else
		$Resized->draw(new ImageText('php2go.graph.Image', 120, 230, GDFONT_2));
	
	/**
	 * Finally, send headers and image content to the browser
	 */
	$Resized->display(IMAGETYPE_PNG);

?>