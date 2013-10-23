<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 

// HTML5 method 
?>

<?php 
	$url = $_SERVER['REQUEST_URI'];
	
	if(($pos = strpos($url, '?')) !== false)
		$url = substr($url, 0, $pos);		
	
	$url .= '?';

	if($_SERVER['REQUEST_METHOD'] == 'POST') {

		header('Content-Type: application/x-json;charset=utf-8');
		
		//the actual maximum file size is post_max_size and not upload_max_size, fix this ?
		if(empty($this->headers['Size'])) {
		
			echo json_encode(array('success' => false, 'message' => 'File size is required'));
			exit();
		}
				
		//request file transfert infos
		if(!empty($this->headers['Prefetch'])) {
		
			$filename = '';
			$success = true;
			
			//create transfert log
			if(empty($this->headers['Guid'])) {
				
				$guid = uploadHelper::uuid();
				
				//really needed ?
				while(is_file(JT_TMP_PATH.DS.$guid))
					$guid = uploadHelper::uuid();
					
				//store file path & size
				$filename = uploadHelper::create_filename(basename($this->headers['Filename']), JT_TMP_PATH);
				//$chunk = $filename.$this->headers['Current'];
				
				//force empty file creation
				fclose(fopen($filename, 'w'));
				//fclose(fopen($chunk, 'w'));
				file_put_contents(JT_TMP_PATH.DS.$guid, basename($filename)."\n".$this->headers['Parts']);
				
				echo json_encode(array('guid' => $guid, 'success' => $success, 'remove' => $url.'r='.($guid ? '&guid='.$guid : '')));
				exit();
			}
			
			else {
				
				if(empty($this->headers['Chunk-Size']) || 
					!isset($this->headers['Current']) || 
					!isset($this->headers['Offset']) || 
					!is_numeric($this->headers['Chunk-Size']) || 
					!is_numeric($this->headers['Current']) || 
					!is_numeric($this->headers['Offset'])) {
				
					echo json_encode(array('success' => false, 'message' => 'Invalid headers sent'));
					exit();
				}
				
				$guid = $this->headers['Guid'];
				$filename = JT_TMP_PATH.DS.$guid;
				
				$infos = is_file($filename) ? explode("\n", file_get_contents($filename)) : array();
				
				//guid validation
				if(!preg_match('/[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/', $guid) || !is_file($filename) || empty($infos[0]) || !is_file(JT_TMP_PATH.DS.$infos[0])) {
				
					echo json_encode(array('success' => false, 'message' => 'Invalid guid'));
					exit();
				}
				
				$chunk = JT_TMP_PATH.DS.$infos[0].$this->headers['Current'];
				
				if(!is_file($chunk))
					fclose(fopen($chunk, 'w'));
			
				echo json_encode(array('guid' => $guid, 'size' => filesize($chunk), 'success' => $success, 'remove' => $url.'r='.($guid ? '&guid='.$guid : '')));
				exit();
			}
		}
		
		$success = true;
		$filename = '';
		$path = '';
		$guid = '';
		
		//resume upload
		if(!empty($this->headers['Guid'])) {
		
			$guid = $this->headers['Guid'];
			$file = JT_TMP_PATH.DS.$guid;
			
			$infos = is_file($file) ? explode("\n", file_get_contents($file)) : array();
						
			//guid validation
			if(!preg_match('/[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/', $guid)) {
			
				echo json_encode(array('success' => false, 'Invalid guid'));
				exit();
			}
			
			if(!is_file($file) || empty($infos[0]) || !is_file(JT_TMP_PATH.DS.$infos[0])) {
			
				echo json_encode(array('success' => false, 'File not found'));
				exit();
			}
			
			//here we store the partial upload
			//read the segment info
			$path = uploadHelper::encrypt(JT_TMP_PATH.DS.$infos[0]);
			$filename = JT_TMP_PATH.DS.$infos[0].$this->headers['Current'];
			
			ignore_user_abort(true);
			$handle = fopen($filename, 'ab');
			fwrite($handle, file_get_contents('php://input'));
			fclose($handle);
				
			//ugh! :)
			clearstatcache(/* true, $filename */);
			
			if(connection_aborted())
				exit();
		}
		
		else {
			
			$filename = uploadHelper::create_filename(basename($this->headers['Filename']), JT_TMP_PATH);			
			file_put_contents($filename, file_get_contents('php://input'));
			$path = uploadHelper::encrypt($filename);
		}
		
		$filesize = isset($this->headers['Chunk-Size']) ? $this->headers['Chunk-Size'] : $this->headers['Size'];
		$size = filesize($filename);
		
		if($size == 0 || (empty($this->headers['Partial']) && $size != $filesize))
			unlink($filename);
		else
			if(isset($this->headers['Guid']) && $filesize == $size) {
			
				//merge
				$handle = fopen(JT_TMP_PATH.DS.$infos[0], 'r+b');
				fseek($handle, $this->headers['Offset']);
				fwrite($handle, file_get_contents($filename));
				fclose($handle);
			}
			
			$return = array(
		
								'file' => basename($this->headers['Filename']),
								'path' => $path, 
								'success' =>  !empty($this->headers['Partial']) || $size == $filesize, 
								'size' => $size, 
								'remove' => $url.'r='.urlencode($path).($guid ? '&guid='.$guid : '')
							);
							
			if($guid)
				$return['clean'] = $url.'c='.$guid;
			
		echo json_encode($return);
	
	} 
		
	else
		//remove chunks
		if($guid = uploadHelper::getVar('c')) {

			if(preg_match('/[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/', $guid) && is_file(JT_TMP_PATH.DS.$guid)) {
			
				//remove chunks
				$infos = file(JT_TMP_PATH.DS.$guid);
				
				$infos[0] = trim($infos[0]);
				
				for($i = 0; $i < $infos[1]; $i++)
					if(is_file(JT_TMP_PATH.DS.$infos[0].$i))
						unlink(JT_TMP_PATH.DS.$infos[0].$i);
					
				unlink(JT_TMP_PATH.DS.$guid);
			}
		}
	
	else
		//remove file
		if($file = uploadHelper::getVar('r')) {
				
			if(is_file($file = uploadHelper::decrypt($file))) {
			
				//$file = realpath($file);
				if(is_file(JT_TMP_PATH.DS.basename($file)))
					unlink($file);
					
				//remove file
				if($guid = uploadHelper::getVar('guid')) {

					if(preg_match('/[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/', $guid) && is_file(JT_TMP_PATH.DS.$guid)) {
					
						//remove chunks
						$infos = file(JT_TMP_PATH.DS.$guid);
						
						$infos[0] = trim($infos[0]);
				
						for($i = 0; $i < $infos[1]; $i++)
							if(is_file(JT_TMP_PATH.DS.$infos[0].$i))
								unlink(JT_TMP_PATH.DS.$infos[0].$i);
							
						unlink(JT_TMP_PATH.DS.$guid);
					}
				}
			}
		}
?>
