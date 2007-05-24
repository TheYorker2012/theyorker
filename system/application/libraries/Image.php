<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Image {

	private $ci;

	public function Image() {
		$this->ci = &get_instance();
	}

	public function getPhoto($photoID) {
		$data = $this->get($photoID, 'photos');
		return '<img src="/photos/'.$photoID.'" height="'.$data['height'].'" width="'.$data['width'].'" alt="'.$data['title'].'" title="'.$data['title'].'" />';
	}

	public function getThumb($photoID, $type, $viewLarge = false, $extraTags = array(), $extraArguments = array()) {
		$data = $this->get($photoID, 'thumbs', $type);
		$tagInner = '';
		$data['alt'] = $data['title'];
		foreach (array_merge($data, $extraTags) as $name => $value) $tagInner.= $name.'="'.$value.'" ';
		$tag = '<img src="/photos/'.$type.'/'.$photoID.'" '.$tagInner.'/>';
		if ($viewLarge) $tag = '<a href="/photos/full/'.$photoID.'">'.$tag.'</a>';
		return $tag;
	}

	public function getImage($imageID, $type, $extraTags = array(), $extraArguments = array()) {
		if (is_int($type)) {
			$sql = 'SELECT image_type_codename FROM image_types WHERE image_type_id = ?';
			$codename = $this->db->query($sql, array($type))->first_row()->image_type_codename;
			$data = $this->get($imageID, 'images', $codename);
		} else {
			$data = $this->get($imageID, 'images', $type);
		}
		$tagInner = '';
		$data['alt'] = $data['title'];
		foreach (array_merge($data, $extraTags) as $name => $value) $tagInner.= $name.'="'.$value.'" ';
		return '<img src="/image/'.$type.'/'.$imageID.'" '.$tagInner.'/>';
	}

	public function getPhotoURL($photoID, $type) {
		return '/photos/'.$type.'/'.$photoID;
	}

	private function get($id, $table, $type = null) {
		$result = null;
		switch ($table) {
			case "photos":
				$sql = 'SELECT photo_title AS title, photo_width AS width, photo_height AS height, photo_gallery, photo_complete, photo_deleted
				        FROM photos
				        WHERE photo_id = ? LIMIT 1';
				$result = $this->ci->db->query($sql, array($id));
				break;
			case 'thumbs':
				$sql = 'SELECT image_types.image_type_width AS width,
				        image_types.image_type_height AS height,
				        photos.photo_title AS title,
				        photos.photo_gallery,
				        photos.photo_deleted
				        FROM photos
				        INNER JOIN photo_thumbs
				        ON photos.photo_id = photo_thumbs.photo_thumbs_photo_id
				        INNER JOIN image_types
				        ON photo_thumbs.photo_thumbs_image_type_id = image_types.image_type_id
				        WHERE photos.photo_id = ? AND image_types.image_type_codename = ?';

				$result = $this->ci->db->query($sql, array($id, $type));
				break;
			case 'images':
				$sql = 'SELECT image_title AS title, image_type_width AS width, image_type_height AS height
				        FROM images, image_types
				        WHERE image_id = ?
				          AND image_type_id = image_image_type_id
				        LIMIT 1';
				$result = $this->ci->db->query($sql, array($id));
				break;
			default:
				return false;
		}
		if ($result->num_rows() == 1 and $table == 'images') {
			return $result->first_row('array');
		} elseif ($result->num_rows() == 1 && $result->first_row()->photo_deleted == 0) {
			return $result->first_row('array');
		} else {
			switch ($table) {
				case 'photos':
					return array('height' => 512, 'width' => 512, 'title' => 'Photo not found');
					break;
				case 'thumbs':
				case 'images':
					$sql = 'SELECT image_type_width AS width, image_type_height AS height, image_type_name AS title
					        FROM image_types WHERE image_type_codename=?';
					$result = $this->ci->db->query($sql, array($type));
					$result = $result->first_row('array');
					return array('height' => $result['height'], 'width' => $result['width'],  'title' => $result['title'].' not found');
					break;
			}
		}
	}

	public function add($type, &$newImage, $info = array()) {
		$imageStr = $this->image2string($newImage, $info['mime']);
		switch ($type) {
			case 'photo':
				$sql = 'INSERT INTO photos (photo_author_user_entity_id, photo_title, photo_width, photo_height, photo_mime, photo_data)
				        VALUES (?, ?, ?, ?, ?, "'.mysql_escape_string($imageStr).'")'; // We don't want the binary escaped
				$this->ci->db->query($sql, array($info['author_id'], $info['title'], $info['x'], $info['y'], $info['mime']));
				break;
			case 'image':
				if (isset($info['type_id'])) {
					$id = $info['type_id'];
				} else {
					die('portion not implemented yet');
					//$id = $info['type_code'];
				}
				$sql = 'INSERT INTO images (image_title, image_image_type_id, image_mime, image_data)
				        VALUES (?, ?, ?, "'.mysql_escape_string($imageStr).'")'; // We don't want the binary escaped
				$this->ci->db->query($sql, array($info['title'], $id, $info['mime']));
				break;
			default:
				return false;
		}
		return $this->ci->db->insert_id();
	}

	public function delete($type, $id, $image_type = null) {
		switch($type) {
			case 'photo':
				//set switch to deleted
				$sql = 'DELETE FROM photos WHERE photo_id = ? LIMIT 1';
				if ($this->ci->db->query($sql, array($id))) {
					return true;
				}
				break;
			case 'image':
				//delete from db
				$sql = 'DELETE FROM images WHERE image_id = ? LIMIT 1';
				if ($this->ci->db->query($sql, array($id))) {
					return true;
				}
				break;
			case 'thumb':
				//delete from db
				$sql = 'DELETE FROM photo_thumbs WHERE photo_thumbs_photo_id = ? and photo_thumbs_image_type_id = ? LIMIT 1';
				if ($this->ci->db->query($sql, array($id, $image_type))) {
					return true;
				}
				break;
		}
		return false;
	}

	public function thumbnail($photoID, $type, $x1, $y1, $x2, $y2) {

		//GRAB
		$sql = 'SELECT photo_data, photo_mime FROM photos WHERE photo_id = ? LIMIT 1';
		$result = $this->ci->db->query($sql, array($photoID));
		if ($result->num_rows() == 1) {
			$result = $result->first_row();
			$image = imagecreatefromstring($result->photo_data);
		} else {
			return false;
		}
		//CROP resized too
		$newImage = imagecreatetruecolor($type->x, $type->y);
		if (!imagecopyresampled($newImage, $image, 0, 0, $x1, $y1, $type->x, $type->y, $x2, $y2)) {
			return false;
		}
		//STORE
		$newImage = $this->image2string($newImage, $result->photo_mime);
		$sql = 'INSERT INTO photo_thumbs VALUES (?, ?, "'.mysql_escape_string($newImage).'")';
		$this->ci->db->query($sql, array($photoID, $type->id));
		return true;
	}

	private function image2string(&$newImage, $mime) {
		//THIS SUCKS!!
		$contents = ob_get_contents();
		if ($contents !== false) ob_clean(); else ob_start();
		switch ($mime) {
			case 'image/png':
				imagepng($newImage);
				break;
			case 'image/jpeg':
				imagejpeg($newImage);
				break;
			case 'image/gif':
				imagegif($newImage);
				break;
		}
		$data = ob_get_contents();
		if ($contents !== false) {
		  ob_clean();
		  echo $contents;
		}
		else ob_end_clean();
		return $data;
		//I HATE THIS CODE /\
	}
}
?>
