<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
	/**
	 * The working directory.
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $workDir = 'work';

	/**
	 * The packages directory.
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $packageDir = 'package';

	/**
	 * The release directory.
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $releaseDir = '../releases';

	/**
	 * Base path.
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $basePath = '../code';

	/**
	 * The semantic versioning instance.
	 *
	 * @var    \Robo\Task\Development\SemVer
	 * @since  1.0
	 */
	private $semVer;


	/**
	 * Build the release package.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	public function buildPackage()
	{
		$this->output()->writeln('Build Conference package');

		// Load the semver handler
		$semver = $this->taskSemVer();
		$semver->setFormat('%M.%m.%p%s');
		$this->semVer = $semver->__toString();

		// Create the working directory
		$this->taskFilesystemStack()->mkdir($this->packageDir)->run();
		$this->taskFilesystemStack()->mkdir($this->workDir)->run();
		$this->taskFilesystemStack()->mkdir($this->releaseDir)->run();
		$this->cleanPackageDirectory();

		// Build the extension packages
		$this->buildConferenceComponent();
		$this->buildConferencePackage();

		// Clean up the folders
		$this->_remove($this->workDir);
		$this->_remove($this->packageDir);
	}

	/**
	 * Build the Conference component.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	private function buildConferenceComponent()
	{
		// Clean the working directory
		$this->cleanWorkingDirectory();

		// Copy the manifest files
		$this->_copy($this->basePath . '/administrator/components/com_conference/conference.xml', $this->workDir . '/conference.xml');
		$this->_copy($this->basePath . '/administrator/components/com_conference/script.php', $this->workDir . '/script.php');

		// Copy the administrator files
		$this->_mirrorDir($this->basePath . '/administrator/components/com_conference', $this->workDir . '/administrator/components/com_conference');

		// Copy the site files
		$this->_mirrorDir($this->basePath . '/components/com_conference', $this->workDir . '/components/com_conference');

		// Copy the media files
		$this->_mirrorDir($this->basePath . '/media/com_conference', $this->workDir . '/media/com_conference');

		// Remove files not included in the current release
		$this->removeFiles();

		// Replace some variables
		$this->replacePlaceholders();

		// Create the archive
		$this->taskPack($this->packageDir . '/com_conference_' . $this->semVer . '.zip')
			->addDir('', $this->workDir)
			->run();

		// Clean the build folder
		$this->cleanWorkingDirectory();
	}

	/**
	 * Build the Conference package.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	private function buildConferencePackage()
	{
		// Clean the working directory
		$this->cleanWorkingDirectory();

		$destinationFile = $this->releaseDir . '/pkg_conference_' . $this->semVer . '.zip';

		// Remove any existing file with the same name
		$this->_remove($destinationFile);

		// Copy the extensions
		$this->_mirrorDir($this->packageDir, $this->workDir . '/packages');

		// Copy the package files
		$this->_copyDir('./package_files', $this->workDir);

		// Replace some variables
		$this->replacePlaceholders();

		// Create the archive
		$this->taskPack($destinationFile)
			->addDir('', $this->workDir)
			->run();

		// Clean the build folder
		$this->cleanWorkingDirectory();

	}

	/**
	 * Perform replacement of placeholders.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	private function replacePlaceholders()
	{
		foreach (
			new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($this->workDir . '/'), RecursiveIteratorIterator::SELF_FIRST
			) as $fileInfo)
		{
			if ($fileInfo->isFile() && in_array($fileInfo->getExtension(), array('ini', 'txt', 'php', 'xml')))
			{
				$this->taskReplaceInFile($fileInfo->getRealPath())->from('[year]')->to(date('Y', time()))->run();
				$this->taskReplaceInFile($fileInfo->getRealPath())->from('[date]')->to(date('j F Y', time()))->run();
				$this->taskReplaceInFile($fileInfo->getRealPath())->from('[version]')->to($this->semVer)->run();
				$this->taskReplaceInFile($fileInfo->getRealPath())->from(' && 0 === 1')->to('')->run();
			}
		}
	}

	/**
	 * Clean the working directory.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	private function cleanWorkingDirectory()
	{
		$this->_cleanDir($this->workDir);
	}

	/**
	 * Clean the package directory.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	private function cleanPackageDirectory()
	{
		$this->_cleanDir($this->packageDir);
	}

	/**
	 * List of files to not include in the distribution.
	 *
	 * @return  void  .
	 *
	 * @since   2.0
	 */
	private function removeFiles()
	{
		$files = [

		];

		foreach ($files as $file) {
			$this->_remove($this->workDir . $file);
		}
	}
}
