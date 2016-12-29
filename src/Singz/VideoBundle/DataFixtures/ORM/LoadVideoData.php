<?php
namespace Singz\SocialBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Singz\VideoBundle\Entity\Video;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LoadVideoData  extends AbstractFixture implements OrderedFixtureInterface
{
	private $nb = 20;

	public function load(ObjectManager $manager)
	{
		// list of videos found mainly on 
		// https://www.videezy.com/
		$videos = array(
			'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_2mb.mp4',
			'http://techslides.com/demos/sample-videos/small.mp4',
			'https://static.videezy.com/system/resources/previews/000/002/481/original/golden-gate-bridge-timelapse-hd-stock-video.mp4',
			'https://static.videezy.com/system/resources/previews/000/000/696/original/10.mp4',
			'https://static.videezy.com/system/resources/previews/000/004/795/original/dearborn-st-bridge-4k.mp4',
			'https://static.videezy.com/system/resources/previews/000/004/132/original/Falls_Log_1.mp4',
			'https://static.videezy.com/system/resources/previews/000/002/808/original/Singer.mp4',
			'https://static.videezy.com/system/resources/previews/000/002/161/original/Good_dog.mp4',
			'https://static.videezy.com/system/resources/previews/000/003/761/original/sleddogs.mp4',
			'https://static.videezy.com/system/resources/previews/000/001/279/original/waterfall2.mp4',
			'https://static.videezy.com/system/resources/previews/000/002/249/original/SeveralAnimals.mp4',
			'https://static.videezy.com/system/resources/previews/000/002/377/original/black-cat-in-grass.mp4',
			'https://static.videezy.com/system/resources/previews/000/004/183/original/banjo2.mp4',
			'https://static.videezy.com/system/resources/previews/000/002/806/original/Piano.mp4',
			'https://static.videezy.com/system/resources/previews/000/002/807/original/Saxophone.mp4',
			'https://static.videezy.com/system/resources/previews/000/002/809/original/Tamborine.mp4',
			
		);
		
		for($i=0; $i<$this->nb; $i++){
			$randomVideoUrl = $videos[rand(0, count($videos)-1)];
			$path = $this->downloadFile($randomVideoUrl, $i.'.mp4');
			$file = new File($path);
			// Create our video and set details
			$video = new Video();
			$video->setFile($file);
			$manager->persist($video);
			//keep the object
			$this->addReference('video '.$i, $video);
		}
		$manager->flush();
	}

	public function getOrder()
	{
		return 1;
	}

	private function downloadFile($url, $filename){
		//Upload directory
		$uploadDir = 'web/uploads/fixtures/';
		// if upload dir does not exist
		if(!is_dir($uploadDir)){
			mkdir($uploadDir, 0777, true);
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec ($ch);
		curl_close ($ch);
	
		$destination = $uploadDir.$filename;
		
		$file = fopen($destination, "w");
		fputs($file, $data);
		fclose($file);
		return $destination;
	}
}