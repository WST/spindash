<?php


/**
* SpinDash — A web development framework
* © 2007–2013 Ilya I. Averkov <admin@jsmart.web.id>
*
* Contributors:
* Irfan Mahfudz Guntur <ayes@bsmsite.com>
* Evgeny Bulgakov <evgeny@webline-masters.ru>
*/

require_once 'phing/tasks/system/MatchingTask.php';
require_once 'phing/types/FileSet.php';
require_once 'phing/tasks/ext/pearpackage/Fileset.php';

require_once 'PEAR/PackageFileManager/File.php';
require_once 'PEAR/PackageFileManager2.php';

class BuildPearPackage extends MatchingTask
{
	private $dir;
	private $version;
	private $state = 'stable';
	private $notes;
	private $filesets = array();
	private $packageFile;

	private function setOptions($pkg) {
		$options = array();
		$options['baseinstalldir'] = 'spindash';
		$options['packagedirectory'] = $this->dir->getAbsolutePath();
		$options['filelistgenerator'] = 'Fileset';
		$options['phing_project'] = $this->getProject();
		$options['phing_filesets'] = $this->filesets;

		if(!is_null($this->packageFile)) {
			$f = new PhingFile($this->packageFile->getAbsolutePath());
			$options['packagefile'] = $f->getName();
			$options['outputdirectory'] = $f->getParent() . DIRECTORY_SEPARATOR;
			$this->log("Creating package file: " . $f->getPath(), Project::MSG_INFO);
		} else {
			$this->log("Creating [default] package.xml file in base directory.", Project::MSG_INFO);
		}
		
		$pkg->setOptions($options);
	}

	public function main() {
		$package = new PEAR_PackageFileManager2();
		$this->setOptions($package);
		$package->setPackage('spindash');
		$package->setSummary('Simple web development framework');
		$package->setDescription('Simple web development framework used by SmartCommunity and Webline Masters staff. It makes it possible to charge a spin dash before starting to write a web application.');
		$package->setChannel('pear.averkov.net');
		$package->setPackageType('php');

		$package->setReleaseVersion($this->version);
		$package->setAPIVersion($this->version);
		$package->setReleaseStability($this->state);
		$package->setAPIStability($this->state);

		$package->setNotes($this->notes);

		$package->setLicense('MIT', 'http://www.opensource.org/licenses/mit-license.php');
		$package->addMaintainer('lead', 'WST', 'Ilya I. Averkov', 'admin@jsmart.web.id');
		$package->addRelease();
		$package->setPhpDep('5.4.0');
		$package->setPearinstallerDep('1.4.0');
		$package->addPackageDepWithChannel('required', 'phing', 'pear.phing.info', '2.3.0');
		$package->addExtensionDep('required', 'pdo');
		$package->addExtensionDep('required', 'mbstring');
		$package->generateContents();

		if(PEAR::isError($e = $package->writePackageFile())) {
			throw new BuildException("Unable to write package file.", new Exception($e->getMessage()));
		}
	}

	public function getFileSets() {
		return $this->filesets;
	}
	
	public function createFileSet() {
		return $this->filesets[array_push($this->filesets, new FileSet()) - 1];
	}
	
	public function setVersion($v) {
		$this->version = $v;
	}
	
	public function setState($v) {
		$this->state = $v;
	}
	
	public function setNotes($v) {
		$this->notes = $v;
	}
    
	public function setDir(PhingFile $f) {
		$this->dir = $f;
    }
    
	public function setDestFile(PhingFile $f) {
		$this->packageFile = $f;
	}
}
