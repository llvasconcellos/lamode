<?php
/**
* Deep Zoom Tools
*
* Copyright (c) 2008-2009, OpenZoom <http://openzoom.org/>
* Copyright (c) 2008-2009, Nicolas Fabre <nicolas.fabre@gmail.com>
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without modification,
* are permitted provided that the following conditions are met:
*
* 1. Redistributions of source code must retain the above copyright notice,
* this list of conditions and the following disclaimer.
*
* 2. Redistributions in binary form must reproduce the above copyright
* notice, this list of conditions and the following disclaimer in the
* documentation and/or other materials provided with the distribution.
*
* 3. Neither the name of OpenZoom nor the names of its contributors may be used
* to endorse or promote products derived from this software without
* specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
* ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
* DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
* ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
* (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
* ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
* SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * @see Thumbnail
 */
require_once 'thumbnail.inc.php';
/**
 * @see Oz_Deepzoom_Descriptor
 */
require_once 'Oz/Deepzoom/Descriptor.php';
/**
 * @see Oz_Deepzoom_Exception
 */
require_once 'Oz/Deepzoom/Exception.php';


/**
 * Creates Deep Zoom collections.
 *
 * @category   Oz
 * @package    Oz_Deepzoom
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class Oz_Deepzoom_CollectionCreator {
	/**
	 * @var float
	 */
	protected $_imageQuality;
	/**
	 * @var int
	 */
	protected $_tileSize;
	/**
	 * @var int
	 */
	protected $_maxLevel;
	/**
	 * @var string
	 */
	protected $_tileFormat;
	/**
	 * @var bool
	 */
	protected $_copyMetadata;
	/**
	 * 
	 * @param $image_quality
	 * @param $tile_size
	 * @param $max_level
	 * @param $tile_format
	 * @param $copy_metadata unused
	 */
	public function __construct($imageQuality=0.95, $tileSize=254,$maxLevel=8, $tileFormat="jpg", $copyMetadata=true) {
		$this->_imageQuality = $imageQuality;
        $this->_tileSize = $tileSize;
        $this->_maxLevel = $maxLevel;
        $this->_tileFormat = $tileFormat;
        $this->_copyMetadata = $copyMetadata;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name) {
		return $this->{'_'.$name};
	}

	/**
	 * Returns position (column, row) from given Z-order (Morton number.)
	 * 
	 * @param unknown_type $zOrder
	 * @return array
	 */
	protected function _getPosition($zOrder){
		$column = 0;
        $row = 0;
        foreach(range(0, 32, 2) as $i) {
        	$offset = $i / 2;
        	// column
        	$column_offset = $i;
        	$column_mask = 1 << $column_offset;
        	$column_value = ($zOrder & $column_mask) >> $column_offset;
        	$column |= $column_value << $offset;
        	// row
        	$row_offset = $i + 1;
            $row_mask = 1 << $row_offset;
            $row_value = ($zOrder & $row_mask) >> $row_offset;
            $row |= $row_value << $offset;
            return array((int)$column, (int)$row);
        }
	}
	
	/**
	 * Returns the Z-order (Morton number) from given position.
	 * @param $column
	 * @param $row
	 * @return Morton number
	 */
	protected function _getZOrder($column, $row){
		$zOrder = 0;
		foreach(range(0, 32) as $i) {
			$zOrder |= ($column & 1 << $i) << $i | ($row & 1 << $i) << ($i + 1);
		}
		return $zOrder;	
	}
	
    /**
     * 
     * @param $zOrder
     * @param $level
     * @param $tileSize
     */
	protected function _getTilePosition($zOrder, $level, $tileSize){
		$level_size = pow(2, $level);
		list($x,$y) = $this->_getPosition($zOrder);
		
		$nvX = (int)floor(($x * $level_size) / $tileSize);
		$nvY = (int)floor(($y * $level_size) / $tileSize);
		return array($nvX,$nvY);
	}
	
    /**
     * Creates a Deep Zoom collection from a list of images
     * 
     * @param unknown_type $images
     * @param unknown_type $destination
     */   
    public function create($images, $destination) {
    	$this->_createPyramid($images, $destination);
    	$this->_createDescriptor($images, $destination);	
    }    
 	
    /**
     * Creates a Deep Zoom collection pyramid from a list of images
     * 
     * @param unknown_type $images
     * @param unknown_type $destination
     */
 	protected function _createPyramid($images, $destination) {
    	
    }  
 
    /**
     * Creates a Deep Zoom collection descriptor from a list of images
     * 
     * @param unknown_type $images
     * @param unknown_type $destination
     */
    protected function _createDescriptor($images, $destination) {
		$doc = new DOMDocument('1.0', 'UTF-8');
		
		$collection = $doc->appendChild(new DOMElement('Collection'));
		$collection.setAttribute("xmlns", NS_DEEPZOOM);
        $collection.setAttribute("MaxLevel", str($this._maxLevel));
        $collection.setAttribute("TileSize", str($this._tileSize));
        $collection.setAttribute("Format", str($this._tileFormat));
        $collection.setAttribute("Quality", str($this._imageQuality));
        
        $items = $doc->appendChild(new DOMElement('Items'));
        
        $next_item_id = 0;
        foreach($images as $path) {
        	$descriptor = Oz_Deepzoom_Descriptor();
        	$descriptor->open($path);
			$id = $next_item_id;
            $n = $next_item_id;
            $source = $path; # relative path
            $width = $descriptor.width;
            $height = $descriptor.height;
 
            $item = new DOMElement('I');
            $item.setAttribute("Id", str($id));
            $item.setAttribute("N", str($n));
            $item.setAttribute("Source", str($source));
 
            $size = new DOMElement('Size');
            $size.setAttribute("Width", str($width));
            $size.setAttribute("Height", str($height));
            $item.appendChild($size);
 
            $items.appendChild($item);
            $next_item_id += 1;
        }
        collection.setAttribute("NextItemId", str($next_item_id));
        
        file_put_contents($destination,$collection->saveXML());
    	/*
    	 doc = xml.dom.minidom.Document()
        collection = doc.createElementNS(NS_DEEPZOOM, "Collection")
        collection.setAttribute("xmlns", NS_DEEPZOOM)
        collection.setAttribute("MaxLevel", str(self.max_level))
        collection.setAttribute("TileSize", str(self.tile_size))
        collection.setAttribute("Format", str(self.tile_format))
        collection.setAttribute("Quality", str(self.image_quality))
 
        items = doc.createElementNS(NS_DEEPZOOM, "Items")
 
        next_item_id = 0
        for path in images:
            descriptor = DeepZoomImageDescriptor()
            descriptor.open(path)
            id = next_item_id
            n = next_item_id
            source = path # relative path
            width = descriptor.width
            height = descriptor.height
 
            item = doc.createElementNS(NS_DEEPZOOM, "I")
            item.setAttribute("Id", str(id))
            item.setAttribute("N", str(n))
            item.setAttribute("Source", str(source))
 
            size = doc.createElementNS(NS_DEEPZOOM, "Size")
            size.setAttribute("Width", str(width))
            size.setAttribute("Height", str(height))
            item.appendChild(size)
 
            items.appendChild(item)
            next_item_id += 1
 
        collection.setAttribute("NextItemId", str(next_item_id))
 
        collection.appendChild(items)
        doc.appendChild(collection)
 
        descriptor = doc.toxml(encoding="UTF-8")
# descriptor = doc.toprettyxml(indent=" ", encoding="UTF-8")
        file = open(destination, "w")
        file.write(descriptor)
        file.close()
    	 */
    }
}
	
/**
 class CollectionCreator(object):
 
   
    def _create_pyramid(self, images, destination):
        """Creates a Deep Zoom collection pyramid from a list of images."""
        pyramid_path = os.path.splitext(destination)[0] + "_files"
        if not os.path.exists(pyramid_path):
            os.mkdir(pyramid_path)
 
        for level in xrange(self.max_level + 1):
            level_size = 2**level
            level_path = pyramid_path + "/" + str(level)
            if not os.path.exists(level_path):
                os.mkdir(level_path)
 
            for i in xrange(len(images)):
                path = images[i]
                descriptor = DeepZoomImageDescriptor()
                descriptor.open(path)
                column, row = self._get_tile_position(i, level, self.tile_size)
                tile_path = level_path + "/%s_%s.%s"%(column, row, self.tile_format)
                if not os.path.exists(tile_path):
                    tile_image = PIL.Image.new("RGB", (self.tile_size, self.tile_size))
                    tile_image.save(tile_path, "JPEG", quality=int(self.image_quality * 100))
                tile_image = PIL.Image.open(tile_path)
                source_path = open(os.path.splitext(path)[0] + "_files/" + str(level) + "/%s_%s.%s"%(0, 0, descriptor.tile_format))
                source_image = PIL.Image.open(source_path)
                images_per_tile = int(math.floor(self.tile_size / level_size))
                column, row = self._get_position(i)
                x = (column % images_per_tile) * level_size
                y = (row % images_per_tile) * level_size
                tile_image.paste(source_image, (x,y))
                tile_image.save(tile_path)
 
    def _create_descriptor(self, images, destination):
        """Creates a Deep Zoom collection descriptor from a list of images."""
        doc = xml.dom.minidom.Document()
        collection = doc.createElementNS(NS_DEEPZOOM, "Collection")
        collection.setAttribute("xmlns", NS_DEEPZOOM)
        collection.setAttribute("MaxLevel", str(self.max_level))
        collection.setAttribute("TileSize", str(self.tile_size))
        collection.setAttribute("Format", str(self.tile_format))
        collection.setAttribute("Quality", str(self.image_quality))
 
        items = doc.createElementNS(NS_DEEPZOOM, "Items")
 
        next_item_id = 0
        for path in images:
            descriptor = DeepZoomImageDescriptor()
            descriptor.open(path)
            id = next_item_id
            n = next_item_id
            source = path # relative path
            width = descriptor.width
            height = descriptor.height
 
            item = doc.createElementNS(NS_DEEPZOOM, "I")
            item.setAttribute("Id", str(id))
            item.setAttribute("N", str(n))
            item.setAttribute("Source", str(source))
 
            size = doc.createElementNS(NS_DEEPZOOM, "Size")
            size.setAttribute("Width", str(width))
            size.setAttribute("Height", str(height))
            item.appendChild(size)
 
            items.appendChild(item)
            next_item_id += 1
 
        collection.setAttribute("NextItemId", str(next_item_id))
 
        collection.appendChild(items)
        doc.appendChild(collection)
 
        descriptor = doc.toxml(encoding="UTF-8")
# descriptor = doc.toprettyxml(indent=" ", encoding="UTF-8")
        file = open(destination, "w")
        file.write(descriptor)
        file.close()
 */