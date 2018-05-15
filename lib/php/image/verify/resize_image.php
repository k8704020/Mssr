<?php 

//����ת��Y�����O
/***************************************/ 
/*�\ ��G�Q��PHP��GD�禡�ͦ�����q�Y��*/ 
/*�B������:PHP5.01/GD2*/ 
/*���O�����G
�i�H��ܬO/�_���ϡA�O/�_��j�Ϲ��C
�p�G���ϫh�ͦ����Ϫ��ؤo�P�z��J���@�ˡC 
��h�G�ɥi��O����ϧ��� 

�p�G�����ϡA�h���ӭ�Ϥ�ҥͦ��s�� 
��h�G�ھڤ�ҥH��J�����Ϊ̼e�����

�p�G����j�Ϲ��A�h���Ϥؤo���j��s�Ϥؤo�ɡA������Ϥؤo
*/

/*�Ѽƻ����G
$imgout:��X�Ϥ�����}
$imgsrc:���Ϥ���}
$width:�s�Ϫ��e�� 
$height:�s�Ϫ����� 
$cut:�O�_���ϡA1���O�A0���_
$enlarge:�O�_��j�Ϲ��A1���O�A0���_*/ 
/***************************************/ 

class ResizeImage 
{ 
	var $type;						//�Ϥ����� 
	var $width;						//��ڼe�� 
	var $height;					//��ڰ��� 
	var $resize_width;				//���ܫ᪺�e�� 
	var $resize_height;				//���ܫ᪺���� 
	var $cut;						//�O�_���� 
	var $enlarge;					//�O�_��j�Ϲ�
	var $srcimg;					//�ӷ����� 
	var $dstimg;					//�ؼй��ɦ�} 
	var $im;						//�{�ɫإߪ����� 
	var $status;					//�^�Ǫ��A

	function resizeimage($imgout, $imgsrc, $width, $height,$cut,$enlarge) 
	{ 
		$this->dstimg = $imgout;			//�ؼй��ɦ�} 
		$this->srcimg = $imgsrc;			//�ӷ����� 
		$this->resize_width = $width;		//���ܫ᪺�e��
		$this->resize_height = $height;		//���ܫ᪺����
		$this->cut = $cut;					//�O�_���� 
		$this->enlarge = $enlarge;			//�O�_��j�Ϲ�
		$this->initi_img();					//��l�ƹ��� 
		$this->width = imagesx($this->im);  //�ӷ����ɹ�ڼe��
		$this->height = imagesy($this->im); //�ӷ����ɹ�ڰ���
		$this->newimg();					//�ͦ��s���� 
		ImageDestroy ($this->im);			//�����ϧ�
	} 

	function newimg() 
	{ 
		//���� 
		if(($this->cut)=="1"){ 
			if($this->enlarge=='0'){ //����j�Ϲ��A�u�Y��
				//�վ��X���Ϥ��j�p�A�p���W�L���w���j�p�h������j�p
				if($this->resize_width < $this->width)
					$resize_width = $this->resize_width;
				else
					$resize_width = $this->width;

				if($this->resize_height < $this->height)
					$resize_height = $this->resize_height;
				else
					$resize_height = $this->height;
			}else{//��j�Ϲ�
				$resize_width = $this->resize_width;
				$resize_height = $this->resize_height;
			}

			//���ܫ᪺���ɪ���� 
			$resize_ratio = ($this->resize_width)/($this->resize_height); 
			//��ڹ��ɪ���� 
			$ratio = ($this->width)/($this->height); 

			if($ratio>=$resize_ratio){ //�����u�� 
				$newimg = imagecreatetruecolor($resize_width,$resize_height); 
				//�ͦ��զ�I��
				$white = imagecolorallocate($newimg, 255, 255, 255);
				imagefilledrectangle($newimg,0,0,$resize_width,$resize_height,$white);
				imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, $resize_width,$resize_height, (($this->height)*$resize_ratio), $this->height); 
				$this->status = ImageJpeg ($newimg,$this->dstimg); 
			} 
			if($ratio<$resize_ratio){ //�e���u�� 
				$newimg = imagecreatetruecolor($resize_width,$resize_height); 
				//�ͦ��զ�I��
				$white = imagecolorallocate($newimg, 255, 255, 255);
				imagefilledrectangle($newimg,0,0,$resize_width,$resize_height,$white);
				imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, $resize_width, $resize_height, $this->width, (($this->width)/$resize_ratio)); 
				$this->status = ImageJpeg ($newimg,$this->dstimg); 
			} 
		}else{ //������ 
			if($this->enlarge=='0'){//����j�Ϲ��A�u�Y��
				//�վ��X���Ϥ��j�p�A�p���W�L���w���j�p�h������j�p
				if($this->resize_width < $this->width)
					$resize_width = $this->resize_width;
				else
					$resize_width = $this->width;

				if($this->resize_height < $this->height)
					$resize_height = $this->resize_height;
				else
					$resize_height = $this->height;
			}else{//��j�Ϲ�
				$resize_width = $this->resize_width;
				$resize_height = $this->resize_height;
			}

			//���ܫ᪺���ɪ���� 
			$resize_ratio = ($this->resize_width)/($this->resize_height); 
			//��ڹ��ɪ���� 
			$ratio = ($this->width)/($this->height); 

			if($this->width>=$this->height){ //�Ϥ����e
				$newimg = imagecreatetruecolor($resize_width,($resize_height)/$ratio); 
				//�ͦ��զ�I��
				$white = imagecolorallocate($newimg, 255, 255, 255);
				imagefilledrectangle($newimg,0,0,$resize_width,($resize_width)/$ratio,$white);
				imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, $resize_width, ($resize_width)/$ratio, $this->width, $this->height); 
				$this->status = ImageJpeg ($newimg,$this->dstimg); 
			} 
			if($this->width<$this->height){ //�Ϥ�����
				$newimg = imagecreatetruecolor(($resize_height)*$ratio,$resize_height); 
				//�ͦ��զ�I��
				$white = imagecolorallocate($newimg, 255, 255, 255);
				imagefilledrectangle($newimg,0,0,($resize_height)*$ratio,$resize_height,$white);
				imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, ($resize_height)*$ratio, $resize_height, $this->width, $this->height); 
				$this->status = ImageJpeg ($newimg,$this->dstimg); 
			} 
		} 
	} 

	//��l�ƹ��� 
	function initi_img()
	{ 
		//���o�Ϥ������� 
		$getimgdata=@getimagesize($this->srcimg);
		$this->type = $getimgdata['mime']; 

		//�ھ��������Ū���覡
		if($this->type=='image/gif'){ 
			$this->im = imagecreatefromgif($this->srcimg); 
		}else if($this->type=='image/png'){ 
			$this->im = imagecreatefrompng($this->srcimg); 
		}else{ 
			$this->im = imagecreatefromjpeg($this->srcimg); 
		} 
	} 
}
?>