<?php

namespace Singz\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Singz\AdminBundle\Entity\Setting;

class SingzSettingCreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('singz:setting:create')
            ->setDescription('Create the default Singz settings into the database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	// Display start
        $output->writeln('Checking default Singz settings ...');
        // Get entity manager
        $em = $this->getContainer()->get('doctrine')->getManager();
        // Get default settings
        $settings = $this->getContainer()->getParameter('settings');
        // Check if the settings exist
        foreach($settings as $name => $value){
        	$setting = $em->getRepository('SingzAdminBundle:Setting')->findOneBy(array(
        		'name' => $name
        	));
        	if(!$setting){
        		$setting = new Setting();
        		$setting->setName($name);
        		$setting->setValue($value);
        		$em->persist($setting);
        		$output->writeln('The setting "'.$name.'" is created with the "'.$value.'" value');
        		continue;
        	}
        	$output->writeln('The setting "'.$name.'" exists');
        }
        // Create into the database
        $em->flush();
        

        $output->writeln('OK');
    }

}
